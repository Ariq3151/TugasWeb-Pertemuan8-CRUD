<?php

require_once 'config/database.php';

$pdo = Database::getInstance()->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = (int) ($_POST['id'] ?? 0);

    $stmt = $pdo->prepare(
        "DELETE FROM products WHERE id = ?"
    );

    $stmt->execute([$id]);

    header('Location: index.php?msg=Produk berhasil dihapus');
    exit;
}

header('Location: index.php');
exit;