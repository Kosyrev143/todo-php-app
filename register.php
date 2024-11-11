<?php
session_start();
$pdo = new PDO("mysql:host=localhost;dbname=todo_db", "root", "asdfjkl");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Добавляем нового пользователя в базу данных
    $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $password);
    $stmt->execute();

    // Получаем ID только что зарегистрированного пользователя
    $user_id = $pdo->lastInsertId();

    // Сохраняем ID пользователя в сессию для автоматической авторизации
    $_SESSION['user_id'] = $user_id;

    // Перенаправляем на index.php
    header("Location: index.php");
    exit;
}
?>

<!-- Форма регистрации -->
<form method="POST">
    <input type="text" name="username" placeholder="Логин" required>
    <input type="password" name="password" placeholder="Пароль" required>
    <button type="submit">Регистрация</button>
    <a href="login.php">Вход</a>
</form>
