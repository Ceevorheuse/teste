<?php
session_start();
require 'db.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

// --- LÓGICA DE LOGIN ---
if ($action === 'login') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, username, password, avatar_path FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['avatar'] = $row['avatar_path']; // Salva avatar na sessão para usar fácil
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Senha incorreta.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Usuário não encontrado.']);
    }
    exit;
}

// --- LÓGICA DE CADASTRO ---
if ($action === 'register') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email']; // Opcional, mas bom ter

    // Verifica se já existe
    $check = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $check->bind_param("s", $username);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Este usuário já existe.']);
        exit;
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (username, password, bio, avatar_path) VALUES (?, ?, 'Novo aqui!', 'https://api.dicebear.com/7.x/avataaars/svg?seed=' . ?)");
    
    // Nota: usando o username como semente do avatar pra garantir um avatar único
    $stmt->bind_param("sss", $username, $hashed_password, $username);

    if ($stmt->execute()) {
        // Loga o usuário automaticamente após cadastro
        $_SESSION['user_id'] = $stmt->insert_id;
        $_SESSION['username'] = $username;
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Erro no banco de dados.']);
    }
    exit;
}
?>