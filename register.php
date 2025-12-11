<?php
require 'db.php'; // Usa o seu arquivo de conexão

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Verifica se usuário já existe usando $conn
    $check = $conn->query("SELECT id FROM users WHERE username = '$username'");
    
    if ($check->num_rows > 0) {
        echo "Este nome de usuário já está em uso!";
    } else {
        // Criptografa a senha
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Prepara a inserção usando $conn
        $stmt = $conn->prepare("INSERT INTO users (username, password, bio, avatar_path) VALUES (?, ?, 'Novo usuário', 'avatars/default.png')");
        $stmt->bind_param("ss", $username, $hashed_password);

        if ($stmt->execute()) {
            echo "Cadastro realizado! <a href='login.php'>Faça Login aqui</a>";
        } else {
            echo "Erro: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head><title>Cadastro</title></head>
<body>
    <h2>Criar Conta</h2>
    <form method="POST">
        <input type="text" name="username" placeholder="Usuário" required><br><br>
        <input type="password" name="password" placeholder="Senha" required><br><br>
        <button type="submit">Cadastrar</button>
    </form>
</body>
</html>