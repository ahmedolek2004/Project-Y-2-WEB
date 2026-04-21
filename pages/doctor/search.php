<?php
require_once __DIR__ . '/../../includes/autoload.php';
Middleware::checkRoles(['doctor','admin']);
$doctor  = new Doctor();
$keyword = htmlspecialchars(trim($_GET['q'] ?? ''));
$results = $keyword ? $doctor->searchPatients($keyword) : [];
$pageTitle = 'Search Patients';
include __DIR__ . '/../../includes/header.php';
?>
<h1 class="page-title">🔍 Search <span>Patients</span></h1>
<div class="card">
  <form method="GET" class="d-flex gap-1 mb-2">
    <input type="text" name="q" placeholder="Search by name or email..." value="<?= $keyword ?>" style="flex:1" autofocus>
    <button class="btn btn-primary">Search</button>
  </form>
  <?php if ($keyword): ?>
  <p class="text-muted mb-2">Found <?= count($results) ?> result(s)</p>
  <div class="table-wrap">
    <table>
      <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Action</th></tr></thead>
      <tbody>
        <?php foreach ($results as $p): ?>
        <tr>
          <td><?= htmlspecialchars($p['name']) ?></td>
          <td><?= htmlspecialchars($p['email']) ?></td>
          <td><?= htmlspecialchars($p['phone'] ?? '—') ?></td>
          <td><a href="add_record.php?patient_id=<?= $p['id'] ?>" class="btn btn-primary btn-sm">+ Record</a></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($results)): ?>
        <tr><td colspan="4" class="text-center text-muted">No patients found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>