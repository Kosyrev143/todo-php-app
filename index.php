<?php
// Подключение к базе данных
$host = 'MySQL-8.0';
$dbname = 'todo_db';
$username = 'root';
$password = 'asdfjkl';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}

// Добавление задачи
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'])) {
    $title = $_POST['title'];
    $stmt = $pdo->prepare("INSERT INTO tasks (title) VALUES (:title)");
    $stmt->bindParam(':title', $title);
    $stmt->execute();
    header("Location: index.php");
    exit;
}

// Отметка задачи как выполненной
if (isset($_GET['complete'])) {
    $id = $_GET['complete'];
    $stmt = $pdo->prepare("UPDATE tasks SET is_completed = 1 WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    header("Location: index.php");
    exit;
}

// Удаление задачи
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    header("Location: index.php");
    exit;
}

// Получение всех задач
$stmt = $pdo->query("SELECT * FROM tasks");
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

<!-- Форма для добавления новой задачи -->
<form action="index.php" method="POST">
    <input type="text" name="title" placeholder="Введите задачу" required>
    <button type="submit">Добавить</button>
</form>

<!-- Список задач -->
<ul>
    <?php foreach ($tasks as $task): ?>
        <li>
            <?php if ($task['is_completed']): ?>
                <s><?= htmlspecialchars($task['title']) ?></s> <!-- Зачеркнутый текст для выполненных задач -->
            <?php else: ?>
                <?= htmlspecialchars($task['title']) ?>
                <a href="?complete=<?= $task['id'] ?>">[Отметить как выполнено]</a>
            <?php endif; ?>
            <a href="?delete=<?= $task['id'] ?>">[Удалить]</a>
        </li>
    <?php endforeach; ?>
</ul>
</body>
</html>
