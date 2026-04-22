<?php
require_once 'baglanti.php';

try {
    $sorgu = $db->query("SELECT * FROM categories ORDER BY isim ASC");
    echo json_encode($sorgu->fetchAll(PDO::FETCH_ASSOC));
} catch (PDOException $e) {
    echo json_encode(["hata" => $e->getMessage()]);
}
?>