<?php
session_start();
require 'db.php'; // Usa sua conexão $conn

// 1. Segurança: Se não estiver logado, manda para o login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$mensagem = ""; // Variável para mostrar sucesso ou erro na tela

// 2. Lógica de Upload (só roda se clicar no botão)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['memeFile'])) {
    
    $title = $_POST['title'];
    $userId = $_SESSION['user_id']; // Pega ID do usuário logado

    $targetDir = "uploads/";
    if (!file_exists($targetDir)) mkdir($targetDir);
    
    $fileName = basename($_FILES["memeFile"]["name"]);
    $targetFilePath = $targetDir . time() . "_" . $fileName; 

    // Tenta mover o arquivo
    if(move_uploaded_file($_FILES["memeFile"]["tmp_name"], $targetFilePath)){
        
        // Salva no banco
        $stmt = $conn->prepare("INSERT INTO posts (user_id, title, image_path) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $userId, $title, $targetFilePath);
        
        if($stmt->execute()){
            $mensagem = "<p style='color:green'>✅ Meme publicado com sucesso!</p>";
        } else {
            $mensagem = "<p style='color:red'>❌ Erro no banco: " . $conn->error . "</p>";
        }
    } else {
        $mensagem = "<p style='color:red'>❌ Erro ao salvar o arquivo na pasta.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Postar Meme - LibreFunny</title>
    <style>
        body { font-family: sans-serif; text-align: center; padding: 20px; }
        form { display: inline-block; border: 1px solid #ccc; padding: 20px; border-radius: 10px; }
        input { margin: 10px 0; display: block; width: 100%; }
    </style>
</head>
<body>

    <h2>Olá, <?php echo $_SESSION['username']; ?>! 👋</h2>
    <p>Poste seu meme abaixo ou <a href="logout.php">Sair</a></p>
    <hr>

    <?php echo $mensagem; ?>

    <form method="POST" enctype="multipart/form-data">
        <label>Título do Meme:</label>
        <input type="text" name="title" placeholder="Ex: Quando o código compila..." required>
        
        <label>Escolha a imagem:</label>
        <input type="file" name="memeFile" required>
        
        <button type="submit">Publicar Meme</button>
    </form>

</body>
</html>