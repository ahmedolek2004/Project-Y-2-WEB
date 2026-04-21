<?php
require_once __DIR__ . '/../../classes/Permission.php';
require_once __DIR__ . '/../../classes/User.php';
require_once __DIR__ . '/../../classes/Validator.php';

Permission::require('admin', 'doctor');

$userModel = new User();
$query     = Validator::sanitize($_GET['q'] ?? '');
$searchType = $_GET['type'] ?? 'all'; // all, patient, doctor
$results   = [];

if ($query !== '') {
    if ($searchType === 'patient') {
        $results = $userModel->searchPatients($query);
    } elseif ($searchType === 'doctor') {
        $results = $userModel->searchByRole($query, 'doctor');
    } else {
        // Search all users
        $results = $userModel->searchByQuery($query);
    }
}

$currentRole = $_SESSION['user_role'] ?? '';
$pageTitle = 'Search';
include __DIR__ . '/../../includes/header.php';
?>
<div class="page-header">
    <h1 class="page-title">Search <span>Users</span></h1>
    <p class="page-subtitle">Find patients or doctors by name or email</p>
</div>

<!-- Search Tabs -->
<div class="search-tabs">
    <a href="?type=all<?= $query ? '&q='.urlencode($query) : '' ?>" class="tab-link <?= $searchType === 'all' ? 'active' : '' ?>">
        <span class="tab-icon">◉</span> All Users
    </a>
    <a href="?type=patient<?= $query ? '&q='.urlencode($query) : '' ?>" class="tab-link <?= $searchType === 'patient' ? 'active' : '' ?>">
        <span class="tab-icon">◎</span> Patients
    </a>
    <a href="?type=doctor<?= $query ? '&q='.urlencode($query) : '' ?>" class="tab-link <?= $searchType === 'doctor' ? 'active' : '' ?>">
        <span class="tab-icon">⊕</span> Doctors
    </a>
</div>

<form method="GET" action="" class="search-form">
    <input type="text" name="q" value="<?= htmlspecialchars($query) ?>"
           placeholder="Search by name or email…" autofocus>
    <input type="hidden" name="type" value="<?= htmlspecialchars($searchType) ?>">
    <button type="submit" class="btn btn-primary">Search</button>
    <?php if ($query): ?>
        <a href="?" class="btn btn-secondary">Clear</a>
    <?php endif; ?>
</form>

<?php if ($query && empty($results)): ?>
    <div class="alert alert-info">◌ No <?= $searchType === 'patient' ? 'patients' : ($searchType === 'doctor' ? 'doctors' : 'users') ?> found matching "<?= htmlspecialchars($query) ?>"</div>
<?php elseif (!empty($results)): ?>
    <div class="card">
        <div class="card-title">Found <?= count($results) ?> result<?= count($results) !== 1 ? 's' : '' ?></div>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Joined</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $row): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><strong><?= htmlspecialchars($row['name']) ?></strong></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td>
                            <span class="badge <?= $row['role'] === 'admin' ? 'badge-purple' : ($row['role'] === 'doctor' ? 'badge-blue' : 'badge-teal') ?>">
                                <?= ucfirst($row['role']) ?>
                            </span>
                        </td>
                        <td><?= date('M j, Y', strtotime($row['created_at'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php elseif ($query === ''): ?>
    <div class="card" style="max-width:480px; text-align:center; padding:3rem;">
        <div style="font-size:2rem; margin-bottom:1rem; color:var(--text-muted);">◉</div>
        <div class="text-muted">Enter a name or email above to search</div>
    </div>
<?php endif; ?>

<style>
    .search-tabs {
        display: flex;
        gap: 8px;
        margin-bottom: 1.5rem;
        border-bottom: 1px solid var(--border);
        flex-wrap: wrap;
    }

    .tab-link {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 12px 16px;
        border: none;
        background: transparent;
        color: var(--text-secondary);
        text-decoration: none;
        font-size: 0.85rem;
        cursor: pointer;
        border-bottom: 2px solid transparent;
        transition: all var(--transition);
        font-weight: 500;
    }

    .tab-link:hover {
        color: var(--text-primary);
    }

    .tab-link.active {
        color: var(--accent-teal);
        border-bottom-color: var(--accent-teal);
    }

    .tab-icon {
        font-size: 1rem;
    }

    @media (max-width: 768px) {
        .search-tabs {
            gap: 4px;
            margin-bottom: 1rem;
        }

        .tab-link {
            padding: 10px 12px;
            font-size: 0.75rem;
        }

        .search-form {
            flex-direction: column;
        }

        .search-form input,
        .search-form button {
            width: 100%;
        }
    }
</style>

<?php include __DIR__ . '/../../includes/footer.php'; ?>

