<?php

require_once 'config/database.php';

$pdo = Database::getInstance()->getConnection();

$search = trim($_GET['search'] ?? '');

$perPage = 5;
$page = max(1, (int) ($_GET['page'] ?? 1));
$offset = ($page - 1) * $perPage;

$where = '';
$params = [];

if ($search !== '') {
    $where = ' WHERE p.name LIKE :search';
    $params['search'] = '%' . $search . '%';
}

$countSql = "
    SELECT COUNT(*)
    FROM products p
    JOIN categories c ON p.category_id = c.id
    JOIN suppliers s ON p.supplier_id = s.id
    $where
";

$countStmt = $pdo->prepare($countSql);
$countStmt->execute($params);
$totalProducts = (int) $countStmt->fetchColumn();

$totalPages = (int) ceil($totalProducts / $perPage);

if ($totalPages > 0 && $page > $totalPages) {
    $page = $totalPages;
    $offset = ($page - 1) * $perPage;
}

$sql = "
    SELECT
        p.id,
        p.name,
        p.price,
        p.stock,
        c.name AS category_name,
        s.name AS supplier_name
    FROM products p
    JOIN categories c ON p.category_id = c.id
    JOIN suppliers s ON p.supplier_id = s.id
    $where
    ORDER BY p.id ASC
    LIMIT :limit OFFSET :offset
";

$stmt = $pdo->prepare($sql);

foreach ($params as $key => $value) {
    $stmt->bindValue(':' . $key, $value, PDO::PARAM_STR);
}

$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

$stmt->execute();

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$message = $_GET['msg'] ?? '';

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Inventaris Barang</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <div class="container">

        <h1>Inventaris Barang</h1>

        <?php if ($message): ?>
            <div class="flash">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <a href="create.php" class="btn">+ Tambah Produk</a>


        <form method="GET" action="index.php" class="search-form">
            <input type="text" name="search" placeholder="Cari nama produk..."
                value="<?= htmlspecialchars($_GET['search'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

            <button type="submit">Cari</button>
            <a href="index.php">Reset</a>
        </form>


        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Produk</th>
                    <th>Kategori</th>
                    <th>Supplier</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>

                <?php foreach ($products as $product): ?>

                    <tr>
                        <td><?= (int) $product['id'] ?></td>

                        <td>
                            <?= htmlspecialchars($product['name']) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($product['category_name']) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($product['supplier_name']) ?>
                        </td>

                        <td>
                            Rp <?= number_format($product['price'], 0, ',', '.') ?>
                        </td>

                        <td>
                            <?= (int) $product['stock'] ?>
                        </td>

                        <td>
                            <a href="edit.php?id=<?= (int) $product['id'] ?>">
                                Edit
                            </a>

                            <form method="POST" action="delete.php" style="display:inline;"
                                onsubmit="return confirm('Yakin ingin menghapus produk ini?');">

                                <input type="hidden" name="id" value="<?= (int) $product['id'] ?>">

                                <button type="submit">
                                    Hapus
                                </button>

                            </form>
                        </td>
                    </tr>

                <?php endforeach; ?>

            </tbody>
        </table>

        <?php if ($totalProducts > 0): ?>
            <p>
                Menampilkan <?= $offset + 1 ?>
                sampai <?= $offset + count($products) ?>
                dari <?= $totalProducts ?> produk.
            </p>

            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?<?= htmlspecialchars(http_build_query([
                        'search' => $search,
                        'page' => $page - 1
                    ]), ENT_QUOTES, 'UTF-8') ?>">
                        &laquo; Sebelumnya
                    </a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?<?= htmlspecialchars(http_build_query([
                        'search' => $search,
                        'page' => $i
                    ]), ENT_QUOTES, 'UTF-8') ?>" class="<?= $i === $page ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <a href="?<?= htmlspecialchars(http_build_query([
                        'search' => $search,
                        'page' => $page + 1
                    ]), ENT_QUOTES, 'UTF-8') ?>">
                        Berikutnya &raquo;
                    </a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <p>Produk tidak ditemukan.</p>
        <?php endif; ?>


    </div>

</body>

</html>