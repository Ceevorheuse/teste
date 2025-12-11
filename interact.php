<?php
session_start();
require 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Faça login primeiro!']);
    exit;
}

$user_id = $_SESSION['user_id'];
$action = $_POST['action'] ?? '';

// --- LÓGICA DE LIKE ---
if ($action === 'like') {
    $post_id = $_POST['post_id'];

    // Verifica se já curtiu
    $check = $conn->query("SELECT id FROM likes WHERE user_id = $user_id AND post_id = $post_id");

    if ($check->num_rows > 0) {
        // Se já curtiu, REMOVE (Descurtir)
        $conn->query("DELETE FROM likes WHERE user_id = $user_id AND post_id = $post_id");
        $liked = false;
    } else {
        // Se não curtiu, INSERE (Curtir)
        $conn->query("INSERT INTO likes (user_id, post_id) VALUES ($user_id, $post_id)");
        $liked = true;
    }

    // Conta o novo total
    $total = $conn->query("SELECT COUNT(*) as c FROM likes WHERE post_id = $post_id")->fetch_assoc()['c'];

    echo json_encode(['status' => 'success', 'liked' => $liked, 'count' => $total]);
    exit;
}

// --- LÓGICA DE COMENTÁRIO ---
if ($action === 'comment') {
    $post_id = $_POST['post_id'];
    $content = htmlspecialchars($_POST['content']);

    if (empty($content)) {
        echo json_encode(['status' => 'error', 'message' => 'Comentário vazio.']);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO comments (user_id, post_id, content) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $user_id, $post_id, $content);
    
    if ($stmt->execute()) {
        // Retorna os dados para adicionar na tela na hora
        echo json_encode([
            'status' => 'success', 
            'username' => $_SESSION['username'],
            'avatar' => $_SESSION['avatar'] ?? 'https://api.dicebear.com/7.x/avataaars/svg?seed=' . $_SESSION['username'],
            'content' => $content
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Erro ao salvar.']);
    }
    exit;
}
?>