<?php
require_once 'TodoApp.php';


session_start();

// Initialiser l'app si elle n'existe pas en session
if (!isset($_SESSION['todoApp'])) {
    $_SESSION['todoApp'] = serialize(new TodoApp());
}

$todoApp = unserialize($_SESSION['todoApp']);

// Gestion des actions
if ($_POST) {
    if (isset($_POST['add']) && !empty($_POST['title'])) {
        try {
            $todoApp->addTodo($_POST['title']);
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    } elseif (isset($_POST['complete'])) {
        $todoApp->completeTodo((int)$_POST['complete']);
    } elseif (isset($_POST['delete'])) {
        $todoApp->deleteTodo((int)$_POST['delete']);
    }
    
    $_SESSION['todoApp'] = serialize($todoApp);
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

$todos = $todoApp->getTodos();
$stats = $todoApp->getStats();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Todo App</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .stats { background: #f5f5f5; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .add-form { margin-bottom: 30px; }
        .add-form input[type="text"] { width: 70%; padding: 10px; margin-right: 10px; }
        .add-form button { padding: 10px 15px; background: #007cba; color: white; border: none; cursor: pointer; }
        .todo-item { display: flex; justify-content: space-between; align-items: center; padding: 10px; border: 1px solid #ddd; margin-bottom: 5px; }
        .todo-item.completed { background: #e8f5e8; text-decoration: line-through; }
        .actions button { margin-left: 5px; padding: 5px 10px; border: none; cursor: pointer; }
        .complete-btn { background: #28a745; color: white; }
        .delete-btn { background: #dc3545; color: white; }
        .error { color: red; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>📝 Simple Todo Apps</h1>
        <p>Application simple pour CI/CD Pipeline</p>
    </div>

    <div class="stats">
        <strong>Statistiques:</strong> 
        Total: <?= $stats['total'] ?> | 
        Terminées: <?= $stats['completed'] ?> | 
        En cours: <?= $stats['pending'] ?> | 
        Taux de completion: <?= $stats['completion_rate'] ?>%
    </div>

    <?php if (isset($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" class="add-form">
        <input type="text" name="title" placeholder="Nouvelle tâche..." required>
        <button type="submit" name="add">Ajouter</button>
    </form>

    <?php if (empty($todos)): ?>
        <p>Aucune tâche pour le moment. Ajoutez-en une !</p>
    <?php else: ?>
        <?php foreach ($todos as $todo): ?>
            <div class="todo-item <?= $todo['completed'] ? 'completed' : '' ?>">
                <span>
                    <strong>#<?= $todo['id'] ?></strong> - <?= htmlspecialchars($todo['title']) ?>
                    <small>(<?= $todo['created_at'] ?>)</small>
                </span>
                <div class="actions">
                    <?php if (!$todo['completed']): ?>
                        <form method="POST" style="display: inline;">
                            <button type="submit" name="complete" value="<?= $todo['id'] ?>" class="complete-btn">✓</button>
                        </form>
                    <?php endif; ?>
                    <form method="POST" style="display: inline;">
                        <button type="submit" name="delete" value="<?= $todo['id'] ?>" class="delete-btn">✗</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>