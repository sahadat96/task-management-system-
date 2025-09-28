<!-- resources/views/tasks/index.blade.php -->
@extends('master')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">
                     Add Multiple Tasks
                    <span class="badge bg-light text-dark float-end" id="task-count">0 tasks</span>
                </h4>
            </div>
            <div class="card-body">
                <form id="task-form">
                    @csrf
                    <div id="task-repeater">
                        
                    </div>
                    
                    <div class="d-flex gap-2 mt-3">
                        <button type="button" class="btn btn-success" id="add-task">
                            <i class="fas fa-plus"></i> Add New Task
                        </button>
                        <button type="submit" class="btn btn-primary" id="submit-tasks">
                            <i class="fas fa-save"></i> Save All Tasks
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="reset-form">
                            <i class="fas fa-redo"></i> Reset
                        </button>
                    </div>
                </form>
                
                <div id="message-area" class="mt-3"></div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
                <h4 class="mb-0">
                    <i class="fas fa-list"></i> Tasks List
                    <span class="badge bg-light text-dark float-end" id="existing-task-count">Loading...</span>
                </h4>
            </div>
            <div class="card-body">
                <div id="tasks-list">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading tasks...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    let taskCount = 0;
    
    addTaskField();
    
    function addTaskField() {
        const taskId = taskCount++;
        const today = new Date().toISOString().split('T')[0];
        
        const taskField = `
            <div class="task-group border p-3 mb-3 fade-in" id="task-${taskId}">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" name="tasks[${taskId}][title]" 
                                   class="form-control title-input" 
                                   placeholder="Enter task title" required>
                            <div class="invalid-feedback">Title is required</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Priority</label>
                            <select name="tasks[${taskId}][priority]" class="form-control priority-select">
                                <option value="1"> Low (1)</option>
                                <option value="2"> High (2)</option>
                                <option value="3" selected> Urgent (3)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="tasks[${taskId}][status]" class="form-control status-select">
                                <option value="pending"> Pending</option>
                                <option value="in_progress"> In Progress</option>
                                <option value="completed"> Completed</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="tasks[${taskId}][description]" 
                                     class="form-control description-textarea" 
                                     rows="2" 
                                     placeholder="Enter task description (optional)"></textarea>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Due Date</label>
                            <input type="date" name="tasks[${taskId}][due_date]" 
                                   class="form-control due-date-input"
                                   min="${today}">
                        </div>
                    </div>
                </div>
                <div class="text-end">
                    <button type="button" class="btn btn-danger btn-sm remove-task" 
                            data-task-id="${taskId}">
                        <i class="fas fa-trash"></i> Remove Task
                    </button>
                </div>
            </div>
        `;
        $('#task-repeater').append(taskField);
        updateTaskCount();
    }
    
    // Add task button click event
    $('#add-task').click(function() {
        addTaskField();
        // Scroll to the new task
        $('html, body').animate({
            scrollTop: $('#task-' + (taskCount - 1)).offset().top - 100
        }, 500);
    });
    
    // Remove task button event (delegated)
    $('#task-repeater').on('click', '.remove-task', function() {
        const taskId = $(this).data('task-id');
        const $taskElement = $(`#task-${taskId}`);
        
        // Add fade out animation
        $taskElement.fadeOut(300, function() {
            $(this).remove();
            updateTaskCount();
        });
    });
    
    // Reset form button
    $('#reset-form').click(function() {
        if (confirm('Are you sure you want to reset the form? All entered data will be lost.')) {
            $('.task-group').remove();
            taskCount = 0;
            addTaskField();
            clearMessage();
        }
    });
    
    // Update task count display
    function updateTaskCount() {
        const count = $('.task-group').length;
        $('#task-count').text(count + ' task' + (count !== 1 ? 's' : ''));
    }
    
    // Form submission
    $('#task-form').submit(function(e) {
        e.preventDefault();
        
        const submitBtn = $('#submit-tasks');
        const originalText = submitBtn.html();
        
        // Validate form
        if (!validateForm()) {
            showMessage('Please fix the validation errors before submitting.', 'warning');
            return;
        }
        
        // Disable button and show loading
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
        
        $.ajax({
            url: '/api/tasks',
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                showMessage(response.message, 'success');
                loadExistingTasks();
                
                // Reset form but keep one task field after successful submission
                setTimeout(() => {
                    $('.task-group').remove();
                    taskCount = 0;
                    addTaskField();
                }, 1000);
            },
            error: function(xhr) {
                const errors = xhr.responseJSON?.errors || {};
                let errorMessage = 'Failed to save tasks. ';
                
                if (Object.keys(errors).length > 0) {
                    // Display validation errors
                    errorMessage = 'Validation errors:<br>';
                    Object.keys(errors).forEach(field => {
                        errorMessage += `• ${errors[field].join('<br>• ')}<br>`;
                    });
                } else {
                    errorMessage += xhr.responseJSON?.message || 'Please try again.';
                }
                
                showMessage(errorMessage, 'danger');
            },
            complete: function() {
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    // Form validation
    function validateForm() {
        let isValid = true;
        
        // Clear previous validation
        $('.is-invalid').removeClass('is-invalid');
        
        // Check each task title
        $('.title-input').each(function() {
            if (!$(this).val().trim()) {
                $(this).addClass('is-invalid');
                isValid = false;
            }
        });
        
        return isValid;
    }
    
    // Show message function
    function showMessage(message, type) {
        const alertClass = `alert-${type}`;
        const icon = type === 'success' ? 'fa-check-circle' : 
                    type === 'warning' ? 'fa-exclamation-triangle' : 'fa-exclamation-circle';
        
        $('#message-area').html(`
            <div class="alert ${alertClass} alert-dismissible fade show">
                <i class="fas ${icon}"></i> ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
        
        // Auto-hide success messages after 5 seconds
        if (type === 'success') {
            setTimeout(() => {
                $('.alert').alert('close');
            }, 5000);
        }
    }
    
    // Clear message
    function clearMessage() {
        $('#message-area').empty();
    }
    
    // Load existing tasks
    function loadExistingTasks() {
        $.ajax({
            url: '/api/get-tasks',
            method: 'GET',
            success: function(response) {
                const tasks = response.data;
                $('#existing-task-count').text(tasks.length + ' task' + (tasks.length !== 1 ? 's' : ''));
                
                if (tasks.length === 0) {
                    $('#tasks-list').html(`
                        <div class="text-center text-muted py-4">
                            <p>No tasks found</p>
                        </div>
                    `);
                    return;
                }
                
                let tasksHtml = '';
                tasks.forEach(task => {
                    const dueDate = task.due_date ? new Date(task.due_date).toLocaleDateString() : 'Not set';
                    const createdDate = new Date(task.created_at).toLocaleDateString();
                    
                    let priorityClass, priorityText;
                    switch(task.priority) {
                        case 1: priorityClass = 'bg-secondary'; priorityText = 'Low'; break;
                        case 2: priorityClass = 'bg-info'; priorityText = 'Medium'; break;
                        case 3: priorityClass = 'bg-warning'; priorityText = 'High'; break;
                    }
                    
                    let statusIcon, statusClass;
                    switch(task.status) {
                        case 'pending': statusIcon = ''; statusClass = 'text-warning'; break;
                        case 'in_progress': statusIcon = ''; statusClass = 'text-primary'; break;
                        case 'completed': statusIcon = ''; statusClass = 'text-success'; break;
                    }
                    
                    tasksHtml += `
                        <div class="task-item border-bottom pb-3 mb-3 fade-in">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h5 class="mb-1">${task.title}</h5>
                                    <p class="text-muted mb-2">${task.description || 'No description provided'}</p>
                                    <div class="d-flex gap-3 text-sm">
                                        <span class="${statusClass}">
                                            <i class="fas fa-tasks"></i> ${statusIcon} ${task.status.replace('_', ' ')}
                                        </span>
                                        <span class="text-muted">
                                            <i class="far fa-calendar"></i> Due: ${dueDate}
                                        </span>
                                        <span class="text-muted">
                                            <i class="far fa-clock"></i> Created: ${createdDate}
                                        </span>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <span class="badge ${priorityClass} priority-badge">${priorityText} (${task.priority})</span>
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                $('#tasks-list').html(tasksHtml);
            },
            error: function() {
                $('#tasks-list').html(`
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i> Failed to load tasks. Please refresh the page.
                    </div>
                `);
            }
        });
    }
    
    // Load existing tasks on page load
    loadExistingTasks();
    
    // Auto-refresh tasks every 30 seconds
    setInterval(loadExistingTasks, 30000);
});
</script>
@endsection