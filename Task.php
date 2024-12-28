<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Todo App</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/milligram/1.4.1/milligram.min.css">
    <style>
        body {
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
        }
        .task-list {
            margin-top: 20px;
        }
        .task-item {
            display: flex;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .task-text {
            flex-grow: 1;
            margin: 0 15px;
        }
        .done {
            text-decoration: line-through;
            color: #999;
        }
        .error {
            color: red;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <h1>Todo List</h1>
    
    <?php
    // Initialize tasks array
    $tasks = [];
    $error = '';
    
    // Load tasks from file
    if (file_exists('tasks.json')) {
        $tasks = json_decode(file_get_contents('tasks.json'), true) ?? [];
    }
    
    // Handle form submissions
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['add_task'])) {
            $newTask = trim($_POST['task']);
            if (!empty($newTask)) {
                $tasks[] = [
                    'id' => uniqid(),
                    'text' => $newTask,
                    'done' => false
                ];
                file_put_contents('tasks.json', json_encode($tasks));
                header('Location: ' . $_SERVER['PHP_SELF']);
                exit;
            } else {
                $error = 'Task cannot be empty!';
            }
        } elseif (isset($_POST['toggle_task'])) {
            $taskId = $_POST['task_id'];
            foreach ($tasks as &$task) {
                if ($task['id'] === $taskId) {
                    $task['done'] = !$task['done'];
                    break;
                }
            }
            file_put_contents('tasks.json', json_encode($tasks));
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        } elseif (isset($_POST['delete_task'])) {
            $taskId = $_POST['task_id'];
            $tasks = array_filter($tasks, function($task) use ($taskId) {
                return $task['id'] !== $taskId;
            });
            file_put_contents('tasks.json', json_encode(array_values($tasks)));
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        }
    }
    ?>
    
    <!-- Add Task Form -->
    <form method="POST" action="">
        <div class="row">
            <div class="column column-80">
                <input type="text" name="task" placeholder="Enter a new task" required>
            </div>
            <div class="column">
                <button type="submit" name="add_task" class="button-primary">Add Task</button>
            </div>
        </div>
    </form>
    
    <?php if ($error): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <!-- Task List -->
    <div class="task-list">
        <?php foreach ($tasks as $task): ?>
            <div class="task-item">
                <form method="POST" action="" style="margin: 0;">
                    <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                    <button type="submit" name="toggle_task" class="button button-clear">
                        <?php echo $task['done'] ? '☑' : '☐'; ?>
                    </button>
                </form>
                
                <span class="task-text <?php echo $task['done'] ? 'done' : ''; ?>">
                    <?php echo htmlspecialchars($task['text']); ?>
                </span>
                
                <form method="POST" action="" style="margin: 0;">
                    <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                    <button type="submit" name="delete_task" class="button button-clear">🗑️</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>