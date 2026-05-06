<?php
session_start();
require_once 'baglanti.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $userId = $_SESSION['kullanici_id'];

    if ($action === 'deactivate') {
        $stmt = $db->prepare("UPDATE users SET hesap_durumu = 'Pasif' WHERE id = ?");
    } else if ($action === 'delete') {
        $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
    }

    if ($stmt->execute([$userId])) {
        echo json_encode(['status' => 'success']);
    }
}