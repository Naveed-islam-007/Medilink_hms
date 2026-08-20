<?php
// ============================================================
// search.php
// A read-only search page: find doctors by name, specialty
// or symptom. This is the "Read" part of CRUD with a JOIN.
// ============================================================
require_once 'config/db.php';
require_once 'config/auth.php';
require_login();

$page_title = 'Specialty & Symptom Search';

$q    = trim($_GET['q'] ?? '');
$rows = [];

if ($q !== '') {
    $like = "%$q%";
    $stmt = $pdo->prepare(
        "SELECT d.*, h.name AS hospital
         FROM doctors d
         LEFT JOIN hospitals h ON h.id = d.hospital_id
         WHERE d.specialization LIKE ? OR d.symptoms LIKE ? OR d.name LIKE ?
         ORDER BY d.name"
    );
    $stmt->execute([$like, $like, $like]);
    $rows = $stmt->fetchAll();
}

include 'includes/header.php';
?>

<div class="panel">
    <h2>Find a Doctor</h2>
    <p>Search by doctor name, specialization, or symptoms.</p>
    <form class="searchbar">
        <input name="q" value="<?= htmlspecialchars($q) ?>" placeholder="e.g. cardiology, chest pain, skin allergy">
        <button class="btn">Search</button>
    </form>
</div>

<?php if ($q !== ''): ?>
    <div class="panel">
        <h2>Results</h2>
        <div class="table-wrap">
            <table>
                <tr>
                    <th>Doctor</th>
                    <th>Specialization</th>
                    <th>Symptoms</th>
                    <th>Hospital</th>
                    <th>Contact</th>
                </tr>
                <?php foreach ($rows as $r): ?>
                    <tr>
                        <td><?= htmlspecialchars($r['name']) ?></td>
                        <td><?= htmlspecialchars($r['specialization']) ?></td>
                        <td><?= htmlspecialchars($r['symptoms']) ?></td>
                        <td><?= htmlspecialchars($r['hospital'] ?? '') ?></td>
                        <td><?= htmlspecialchars($r['phone']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <?php if (!$rows): ?>
            <p>No matching doctors found.</p>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
