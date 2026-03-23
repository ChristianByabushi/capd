<?php
require_once 'helpers.php';
require_once 'db.php';
$pageTitle = t('activities_title') . ' — CAPD ASBL';
require_once 'includes/header.php';

$status = $_GET['status'] ?? 'all';
$dept   = $_GET['dept'] ?? '';
$page   = max(1, (int)($_GET['page'] ?? 1));
$perPage = 9;
$offset  = ($page - 1) * $perPage;

$where = ['1=1'];
$types = ''; $params = [];
if ($status !== 'all') { $where[] = 'status=?'; $types .= 's'; $params[] = $status; }
if ($dept) { $where[] = 'department=?'; $types .= 's'; $params[] = $dept; }
$whereStr = implode(' AND ', $where);

$total = fetchOne("SELECT COUNT(*) as c FROM activities WHERE $whereStr", $types, $params)['c'] ?? 0;
$activities = fetchAll("SELECT * FROM activities WHERE $whereStr ORDER BY created_at DESC LIMIT $perPage OFFSET $offset", $types, $params);
$departments = fetchAll("SELECT DISTINCT department FROM activities WHERE department != '' ORDER BY department");
$pages = ceil($total / $perPage);
?>

<div class="page-hero">
  <div class="container">
    <h1><?= t('activities_title') ?></h1>
    <nav class="breadcrumb" aria-label="Fil d'Ariane">
      <a href="<?= BASE_URL ?>"><?= t('nav_home') ?></a>
      <span>/</span><span><?= t('activities_title') ?></span>
    </nav>
  </div>
</div>

<section class="section">
  <div class="container">

    <!-- Filters -->
    <div class="filter-bar" role="group" aria-label="Filtres">
      <a href="?status=all" class="filter-btn <?= $status==='all'?'active':'' ?>"><?= t('activities_filter_all') ?></a>
      <a href="?status=ongoing" class="filter-btn <?= $status==='ongoing'?'active':'' ?>"><?= t('activities_ongoing') ?></a>
      <a href="?status=completed" class="filter-btn <?= $status==='completed'?'active':'' ?>"><?= t('activities_completed') ?></a>
      <a href="?status=planned" class="filter-btn <?= $status==='planned'?'active':'' ?>"><?= t('activities_planned') ?></a>
      <?php foreach ($departments as $d): ?>
      <a href="?dept=<?= urlencode($d['department']) ?>" class="filter-btn <?= $dept===$d['department']?'active':'' ?>"><?= e($d['department']) ?></a>
      <?php endforeach; ?>
    </div>

    <?php if ($activities): ?>
    <div class="cards-grid">
      <?php foreach ($activities as $act): ?>
      <article class="card">
        <div class="card-img">
          <?php if ($act['cover_image']): ?>
          <img src="<?= BASE_URL ?>/uploads/<?= e($act['cover_image']) ?>" alt="<?= e(loc($act,'title')) ?>" loading="lazy">
          <?php else: ?>
          <div style="height:220px;background:linear-gradient(135deg,var(--primary),var(--primary-light));display:flex;align-items:center;justify-content:center">
            <i class="fas fa-project-diagram" style="font-size:3rem;color:rgba(255,255,255,.4)" aria-hidden="true"></i>
          </div>
          <?php endif; ?>
          <span class="card-badge"><?= e($act['status']) ?></span>
        </div>
        <div class="card-body">
          <div class="card-meta">
            <?php if ($act['date_start']): ?><span><i class="fas fa-calendar" aria-hidden="true"></i> <?= formatDate($act['date_start']) ?></span><?php endif; ?>
            <?php if ($act['department']): ?><span><i class="fas fa-tag" aria-hidden="true"></i> <?= e($act['department']) ?></span><?php endif; ?>
          </div>
          <h3><a href="activity.php?slug=<?= e($act['slug']) ?>"><?= e(loc($act,'title')) ?></a></h3>
          <p><?= e(truncate(loc($act,'description'), 120)) ?></p>
          <div class="card-footer-link">
            <a href="activity.php?slug=<?= e($act['slug']) ?>" class="btn btn-outline-primary btn-sm"><?= t('read_more') ?></a>
          </div>
        </div>
      </article>
      <?php endforeach; ?>
    </div>

    <!-- Pagination -->
    <?php if ($pages > 1): ?>
    <nav class="pagination" aria-label="Pagination">
      <?php for ($i=1;$i<=$pages;$i++): ?>
      <?php if ($i===$page): ?>
      <span class="current" aria-current="page"><?= $i ?></span>
      <?php else: ?>
      <a href="?status=<?= urlencode($status) ?>&dept=<?= urlencode($dept) ?>&page=<?= $i ?>"><?= $i ?></a>
      <?php endif; ?>
      <?php endfor; ?>
    </nav>
    <?php endif; ?>

    <?php else: ?>
    <p class="text-center" style="color:var(--gray);padding:3rem 0">Aucune activité trouvée.</p>
    <?php endif; ?>

  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
