<?php
header('Content-Type: application/json; charset=utf-8');
require_once 'baglanti.php';

$id = isset($_GET['yazi_id']) ? intval($_GET['yazi_id']) : 0;

if ($id > 0) {
    // Sürümleri tarihe göre en yeniden en eskiye doğru çekiyoruz
    $sorgu = $db->prepare("SELECT * FROM post_versions WHERE yazi_id = ? ORDER BY tarih DESC");
    $sorgu->execute([$id]);
    echo json_encode($sorgu->fetchAll(PDO::FETCH_ASSOC));
} else {
    echo json_encode([]);
}
?>