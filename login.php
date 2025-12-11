<?php
session_start();
require 'db.php'; // Usa o seu arquivo de conexão

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Busca o usuário usando $conn
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Verifica a senha
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $username;
            
            // Redireciona para o upload
            header("Location: upload.php");
            exit;
        } else {
            echo "Senha incorreta!";
        }
    } else {
        echo "Usuário não encontrado!";
    }
}
?>

<!DOCTYPE html>
<html>
<head><title>Login</title></head>
<body>
    <h2>Entrar</h2>
    <form method="POST">
        <input type="text" name="username" placeholder="Usuário" required><br><br>
        <input type="password" name="password" placeholder="Senha" required><br><br>
        <button type="submit">Entrar</button>
    </form>
    <p>Não tem conta? <a href="register.php">Cadastre-se</a></p>
</body>
</html>