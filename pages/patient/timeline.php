<?php
require_once __DIR__ . '/../../includes/autoload.php';
Middleware::checkRole('patient');
$patient  = new Patient();
$timeline = $patient->getTimeline();
$pageTitle = 'My Timeline';
include __DIR__ . '/../../includes/header.php';
?>
<h1 class="page-title">📅 Medical <span>Timeline</span></h1>
<?php if (empty($timeline)): ?>
<div class="alert alert-info">No medical history yet.</div>
<?php else: ?>
<div class="timeline">
  <?php foreach ($timeline as $item): ?>
  <div class="timeline-item">
    <div class="timeline-dot"></div>
    <div class="timeline-date">
      <?= date('d M Y', strtotime($item['visit_date'])) ?>
      — Dr. <?= htmlspecialchars($item['doctor_name']) ?>
    </div>
    <div class="timeline-body">
      <strong>Diagnosis:</strong> <?= htmlspecialchars($item['diagnosis']) ?><br>
      <?php if ($item['notes']): ?>
      <strong>Notes:</strong> <?= htmlspecialchars($item['notes']) ?><br>
      <?php endif; ?>
      <span class="badge badge-doctor" style="margin-top:.4rem">
        💊 <?= $item['prescription_count'] ?> prescription(s)
      </span>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>
<?php include __DIR__ . '/../../includes/footer.php'; ?>