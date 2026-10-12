<?php

require_once 'config/database.php';

$pdo = Database::getInstance()->getConnection();

$categories = $pdo
    ->query("SELECT * FROM categories ORDER BY name")
    ->fetchAll();

$suppliers = $pdo
    ->query("SELECT * FROM suppliers ORDER BY name")
    ->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name']);
    $categoryId = (int) $_POST['category_id'];
    $supplierId = (int) $_POST['supplier_id'];
    $price = (float) $_POST['price'];
    $stock = (int) $_POST['stock'];

    $sql = "
        INSERT INTO products
        (name, category_id, supplier_id, price, stock)
        VALUES (?, ?, ?, ?, ?)
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        $name,
        $categoryId,
        $supplierId,
        $price,
        $stock
    ]);

    header('Location: index.php?msg=Produk berhasil ditambahkan');
    exit;
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Produk</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="container">

    <h1>Tambah Produk</h1>

    <form method="POST">

        <label>Nama Produk</label>
        <input type="text" name="name" required>

        <label>Kategori</label>

        <select name="category_id" required>

            <option value="">-- Pilih Kategori --</option>

            <?php foreach ($categories as $category): ?>

                <option value="<?= (int) $category['id'] ?>">
                    <?= htmlspecialchars($category['name']) ?>
                </option>

            <?php endforeach; ?>

        </select>

        <label>Supplier</label>

        <select name="supplier_id" required>

            <option value="">-- Pilih Supplier --</option>

            <?php foreach ($suppliers as $supplier): ?>

                <option value="<?= (int) $supplier['id'] ?>">
                    <?= htmlspecialchars($supplier['name']) ?>
                </option>

            <?php endforeach; ?>

        </select>

        <label>Harga</label>
        <input type="number" name="price" min="0" required>

        <label>Stok</label>
        <input type="number" name="stock" min="0" required>

        <button type="submit">Simpan</button>

        <a href="index.php">Kembali</a>

    </form>

</div>

</body>
</html>