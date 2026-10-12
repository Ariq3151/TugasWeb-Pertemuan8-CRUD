<?php

require_once 'config/database.php';

$pdo = Database::getInstance()->getConnection();

$id = (int) ($_GET['id'] ?? 0);

$stmt = $pdo->prepare(
    "SELECT * FROM products WHERE id = ?"
);

$stmt->execute([$id]);

$product = $stmt->fetch();

if (!$product) {
    die("Produk tidak ditemukan.");
}

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

    $stmt = $pdo->prepare("
        UPDATE products
        SET name = ?,
            category_id = ?,
            supplier_id = ?,
            price = ?,
            stock = ?
        WHERE id = ?
    ");

    $stmt->execute([
        $name,
        $categoryId,
        $supplierId,
        $price,
        $stock,
        $id
    ]);

    header('Location: index.php?msg=Produk berhasil diperbarui');
    exit;
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Edit Produk</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="container">

    <h1>Edit Produk</h1>

    <form method="POST">

        <label>Nama Produk</label>

        <input
            type="text"
            name="name"
            value="<?= htmlspecialchars($product['name']) ?>"
            required
        >

        <label>Kategori</label>

        <select name="category_id" required>

            <?php foreach ($categories as $category): ?>

                <option
                    value="<?= (int) $category['id'] ?>"
                    <?= $product['category_id'] == $category['id'] ? 'selected' : '' ?>
                >
                    <?= htmlspecialchars($category['name']) ?>
                </option>

            <?php endforeach; ?>

        </select>

        <label>Supplier</label>

        <select name="supplier_id" required>

            <?php foreach ($suppliers as $supplier): ?>

                <option
                    value="<?= (int) $supplier['id'] ?>"
                    <?= $product['supplier_id'] == $supplier['id'] ? 'selected' : '' ?>
                >
                    <?= htmlspecialchars($supplier['name']) ?>
                </option>

            <?php endforeach; ?>

        </select>

        <label>Harga</label>

        <input
            type="number"
            name="price"
            value="<?= htmlspecialchars($product['price']) ?>"
            min="0"
            required
        >

        <label>Stok</label>

        <input
            type="number"
            name="stock"
            value="<?= htmlspecialchars($product['stock']) ?>"
            min="0"
            required
        >

        <button type="submit">
            Update
        </button>

        <a href="index.php">
            Kembali
        </a>

    </form>

</div>

</body>
</html>