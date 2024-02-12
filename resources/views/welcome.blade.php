<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>To-Do List</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>To-Do List</h1>
        <div id="successMessage" class="alert alert-success" style="display: none;"></div>
        <div id="errorMessage" class="alert alert-danger" style="display: none;"></div>
        <table class="table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="todo-list">
                <!-- To-Do items will be added here dynamically -->
            </tbody>
        </table>
        <form id="add-form">
            <div class="form-group">
                <label for="task">Add Task:</label>
                <input type="text" class="form-control" id="task" required>
            </div>
            <button type="submit" class="btn btn-primary">Add Item</button>
        </form>
    </div>
    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmationModalLabel">Confirmation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this task?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirm-delete-btn">Delete</button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    </body>
<script>
$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    var tasks;

    fetchTasks();

    function fetchTasks() {
        $.get('/', function(response) {
            tasks  = {!! json_encode($tasks) !!};
            renderTasks(tasks);
        });
    }

    function updateTaskStatus(taskId, button) {
        $(this).removeClass('btn-primary');
        $.ajax({
            url: '/tasks/' + taskId,
            type: 'PUT',
            success: function(response) {
                $('#successMessage').text(response.message).show();
                tasks = response.tasks;
                renderTasks(tasks);
                setTimeout(function() {
                    $('#successMessage').hide();
                }, 3000);
            },
            error: function(xhr, status, error) {
                $('#errorMessage').text(xhr.responseText).show();
                setTimeout(function() {
                    $('#errorMessage').hide();
                }, 3000);
            }
        });
    }

    // Function to render tasks
    function renderTasks(tasks) {
        $('#todo-list').empty();
        tasks.forEach(function(task) {
            var row = $('<tr>');
            row.append('<td>' + task.title + '</td>');
            var actions = $('<td>');
            if (task.completed) {
                actions.append('<button class="btn btn-success">Done</button>');
            } else {
                actions.append('<button class="btn btn-primary mr-2" data-task-id="' + task.id + '"><i class="fas fa-check"></i></button>');
                actions.append('<button class="btn btn-danger" data-task-id="' + task.id + '"><i class="fas fa-window-close"></i></button>');
            }
            row.append(actions);
            $('#todo-list').append(row);

            actions.find('.btn-primary').click(function() {
                var button = $(this);
                var taskId = button.attr('data-task-id');
                updateTaskStatus(taskId, button);
            });
        });
    }

    $('#todo-list').on('click', '.btn-danger', function() {
        var taskId = $(this).closest('tr').find('.btn-primary').attr('data-task-id');
        $('#confirm-delete-btn').attr('data-task-id', taskId);
        $('#confirmationModal').modal('show');
    });

    $('#confirm-delete-btn').on('click', function() {
        var taskId = $(this).data('task-id');
        $.ajax({
            url: '/tasks-delete/' + taskId,
            type: 'DELETE',
            success: function(response) {
                $('#successMessage').text(response.message).show();
                $('#todo-list').find('[data-task-id="' + taskId + '"]').closest('tr').remove();
                tasks = response.tasks;
                renderTasks(tasks);
                $('#confirmationModal').modal('hide');
                setTimeout(function() {
                    $('#successMessage').hide();
                }, 3000);
            },
            error: function(xhr, status, error) {
                $('#confirmationModal').modal('hide');
                // Show error message
                $('#errorMessage').text(xhr.responseText).show();
                setTimeout(function() {
                    $('#errorMessage').hide();
                }, 3000);
            }
        });
    });

    $('#add-form').submit(function(event) {
        event.preventDefault();
        var taskTitle = $('#task').val().trim();
        if (taskTitle) {
            var data = {
                title: taskTitle
            };
            $.ajax({
                url: '/add-task',
                type: 'POST',
                data: data,
                success: function(response) {
                    if (response.success) {
                        $('#successMessage').text(response.message).show();
                        var newTask = response.tasks;
                        renderTasks(newTask);
                        
                        $('#task').val('');
                        setTimeout(function() {
                        $('#successMessage').hide();
                    }, 3000);
                    } else {
                        // Show error message
                        $('#errorMessage').text(response.message).show();
                        setTimeout(function() {
                            $('#errorMessage').hide();
                        }, 3000);
                        $('#task').val('');
                    }
                },
                error: function(xhr, status, error) {
                    $('#errorMessage').text('Failed to add task. Please try again.').show();
                    setTimeout(function() {
                        $('#errorMessage').hide();
                    }, 3000);
                }
            });
        } else {
            $('#validationMessage').text('Please enter a task.').show();
            setTimeout(function() {
                $('#validationMessage').hide();
            }, 3000);
        }
    });
});

</script>
</html>
