<?php

require_once 'config/db.php';
require_once 'config/auth.php';
require_login();

$page_title = 'Pharmacy & Medicine Management';


if (isset($_POST['save'])) {

    $pharmacy_id = $_POST['pharmacy_id'];
    $name        = trim($_POST['name']);
    $category    = trim($_POST['category']);
    $quantity    = (int) $_POST['quantity'];
    $price       = (float) $_POST['price'];

    if (!empty($_POST['medicine_id'])) {
        // UPDATE an existing medicine
        $stmt = $pdo->prepare(
            "UPDATE medicines
             SET pharmacy_id = ?, name = ?, category = ?, quantity = ?, price = ?
             WHERE id = ?"
        );
        $stmt->execute([$pharmacy_id, $name, $category, $quantity, $price, $_POST['medicine_id']]);
    } else {
        // INSERT a new medicine
        $stmt = $pdo->prepare(
            "INSERT INTO medicines (pharmacy_id, name, category, quantity, price)
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->execute([$pharmacy_id, $name, $category, $quantity, $price]);
    }

    header("Location: medicines.php");
    exit;
}


if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM medicines WHERE id = ?");
    $stmt->execute([$_GET['delete']]);

    header("Location: medicines.php");
    exit;
}


$editing = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM medicines WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $editing = $stmt->fetch();
}


$pharmacies = $pdo->query("SELECT * FROM pharmacies ORDER BY name")->fetchAll();


$q    = trim($_GET['q'] ?? '');
$like = "%$q%";

$stmt = $pdo->prepare(
    "SELECT m.*, p.name AS pharmacy
     FROM medicines m
     JOIN pharmacies p ON p.id = m.pharmacy_id
     WHERE m.name LIKE ? OR m.category LIKE ? OR p.name LIKE ?
     ORDER BY m.name"
);
$stmt->execute([$like, $like, $like]);
$rows = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="panel">
    <h2><?= $editing ? 'Edit Medicine' : 'Add Medicine' ?></h2>

    <form method="post" class="grid">

        <?php if ($editing): ?>
            <input type="hidden" name="medicine_id" value="<?= $editing['id'] ?>">
        <?php endif; ?>

        <div class="field">
            <label>Pharmacy</label>
            <select name="pharmacy_id" required>
                <?php foreach ($pharmacies as $p): ?>
                    <option value="<?= $p['id'] ?>"
                        <?= (isset($editing['pharmacy_id']) && $editing['pharmacy_id'] == $p['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="field">
            <label>Medicine</label>
            <input name="name" required value="<?= htmlspecialchars($editing['name'] ?? '') ?>">
        </div>

        <div class="field">
            <label>Category</label>
            <input name="category" value="<?= htmlspecialchars($editing['category'] ?? '') ?>">
        </div>

        <div class="field">
            <label>Quantity</label>
            <input type="number" name="quantity" min="0" value="<?= htmlspecialchars($editing['quantity'] ?? '0') ?>">
        </div>

        <div class="field">
            <label>Price</label>
            <input type="number" step="0.01" name="price" value="<?= htmlspecialchars($editing['price'] ?? '0') ?>">
        </div>

        <div class="actions">
            <button class="btn" name="save"><?= $editing ? 'Update Medicine' : 'Add Medicine' ?></button>
            <?php if ($editing): ?>
                <a class="btn secondary" href="medicines.php">Cancel</a>
            <?php endif; ?>
        </div>

    </form>
</div>

<div class="panel">
    <form class="searchbar">
        <input name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Search medicine, category or pharmacy">
        <button class="btn">Search</button>
    </form>

    <div class="table-wrap">
        <table>
            <tr>
                <th>Medicine</th>
                <th>Category</th>
                <th>Pharmacy</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Action</th>
            </tr>
            <?php foreach ($rows as $r): ?>
                <tr>
                    <td><?= htmlspecialchars($r['name']) ?></td>
                    <td><?= htmlspecialchars($r['category']) ?></td>
                    <td><?= htmlspecialchars($r['pharmacy']) ?></td>
                    <td><?= htmlspecialchars($r['quantity']) ?></td>
                    <td><?= htmlspecialchars($r['price']) ?></td>
                    <td class="actions">
                        <a class="btn secondary" href="?edit=<?= $r['id'] ?>">Edit</a>
                        <a class="btn danger" href="?delete=<?= $r['id'] ?>"
                           onclick="return confirm('Delete this medicine?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
