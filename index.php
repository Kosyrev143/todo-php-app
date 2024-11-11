<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$pdo = new PDO("mysql:host=localhost;dbname=todo_db", "root", "asdfjkl");
$user_id = $_SESSION['user_id'];

// Добавление новой задачи
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'])) {
    $title = $_POST['title'];
    $stmt = $pdo->prepare("INSERT INTO tasks (title, is_completed, user_id) VALUES (:title, 0, :user_id)");
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    header("Location: index.php");
    exit;
}

// Отметка задачи как выполненной
if (isset($_GET['complete'])) {
    $id = $_GET['complete'];
    $stmt = $pdo->prepare("UPDATE tasks SET is_completed = 1 WHERE id = :id AND user_id = :user_id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    header("Location: index.php");
    exit;
}

// Удаление задачи
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = :id AND user_id = :user_id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    header("Location: index.php");
    exit;
}

// Редактирование задачи
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    $id = $_POST['edit_id'];
    $title = $_POST['edit_title'];
    $stmt = $pdo->prepare("UPDATE tasks SET title = :title WHERE id = :id AND user_id = :user_id");
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    header("Location: index.php");
    exit;
}

// Получение задач для текущего пользователя
$stmt = $pdo->prepare("SELECT * FROM tasks WHERE user_id = :user_id");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Список задач</title>
</head>
<body>
<h1>Список задач</h1>
<form action="index.php" method="POST">
    <input type="text" name="title" placeholder="Введите задачу" required>
    <button type="submit">Добавить</button>
</form>
<ul>
    <?php foreach ($tasks as $task): ?>
        <li>
            <?php if ($task['is_completed']): ?>
                <s><?= htmlspecialchars($task['title']) ?></s>
            <?php else: ?>
                <?= htmlspecialchars($task['title']) ?>
                <a href="?complete=<?= $task['id'] ?>">[Выполнено]</a>
            <?php endif; ?>
            <a href="?delete=<?= $task['id'] ?>">[Удалить]</a>
            <form action="index.php" method="POST" style="display:inline;">
                <input type="hidden" name="edit_id" value="<?= $task['id'] ?>">
                <input type="text" name="edit_title" placeholder="Изменить задачу" required>
                <button type="submit">Редактировать</button>
            </form>
        </li>
    <?php endforeach; ?>
</ul>
<a href="logout.php">Выйти</a>
</body>
</html>
