<?php
require_once 'helpers.php';
require_once 'db.php';
$pageTitle = t('blog_title') . ' — CAPD ASBL';
require_once 'includes/header.php';

$cat  = $_GET['cat'] ?? 'all';
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 9; $offset = ($page-1)*$perPage;

$where = ['is_published=1'];
$types = ''; $params = [];
if ($cat !== 'all') { $where[] = 'category=?'; $types .= 's'; $params[] = $cat; }
$whereStr = implode(' AND ', $where);

$total = fetchOne("SELECT COUNT(*) as c FROM posts WHERE $whereStr", $types, $params)['c'] ?? 0;
$posts = fetchAll("SELECT * FROM posts WHERE $whereStr ORDER BY published_at DESC LIMIT $perPage OFFSET $offset", $types, $params);
$pages = ceil($total / $perPage);
?>

<div class="page-hero">
  <div class="container">
    <h1><?= t('blog_title') ?></h1>
    <nav class="breadcrumb" aria-label="Fil d'Ariane">
      <a href="<?= BASE_URL ?>"><?= t('nav_home') ?></a>
      <span>/</span><span><?= t('blog_title') ?></span>
    </nav>
  </div>
</div>

<section class="section">
  <div class="container">
    <div class="filter-bar" role="group" aria-label="Catégories">
      <a href="?cat=all" class="filter-btn <?= $cat==='all'?'active':'' ?>"><?= t('activities_filter_all') ?></a>
      <a href="?cat=news" class="filter-btn <?= $cat==='news'?'active':'' ?>">News</a>
      <a href="?cat=communique" class="filter-btn <?= $cat==='communique'?'active':'' ?>">Communiqués</a>
      <a href="?cat=report" class="filter-btn <?= $cat==='report'?'active':'' ?>">Rapports</a>
    </div>

    <?php if ($posts): ?>
    <div class="cards-grid">
      <?php foreach ($posts as $post): ?>
      <article class="card">
        <div class="card-img">
          <?php if ($post['cover_image']): ?>
          <img src="<?= BASE_URL ?>/uploads/<?= e($post['cover_image']) ?>" alt="<?= e(loc($post,'title')) ?>" loading="lazy">
          <?php else: ?>
          <div style="height:220px;background:linear-gradient(135deg,var(--accent),var(--accent-dark));display:flex;align-items:center;justify-content:center">
            <i class="fas fa-newspaper" style="font-size:3rem;color:rgba(255,255,255,.4)" aria-hidden="true"></i>
          </div>
          <?php endif; ?>
          <span class="card-badge"><?= e($post['category']) ?></span>
        </div>
        <div class="card-body">
          <div class="card-meta">
            <span><i class="fas fa-calendar" aria-hidden="true"></i> <?= $post['published_at'] ? formatDate($post['published_at']) : '' ?></span>
          </div>
          <h3><a href="post.php?slug=<?= e($post['slug']) ?>"><?= e(loc($post,'title')) ?></a></h3>
          <p><?= e(truncate(loc($post,'excerpt') ?: loc($post,'content'), 120)) ?></p>
          <div class="card-footer-link">
            <a href="post.php?slug=<?= e($post['slug']) ?>" class="btn btn-outline-primary btn-sm"><?= t('read_more') ?></a>
          </div>
        </div>
      </article>
      <?php endforeach; ?>
    </div>

    <?php if ($pages > 1): ?>
    <nav class="pagination" aria-label="Pagination">
      <?php for ($i=1;$i<=$pages;$i++): ?>
      <?php if ($i===$page): ?>
      <span class="current" aria-current="page"><?= $i ?></span>
      <?php else: ?>
      <a href="?cat=<?= urlencode($cat) ?>&page=<?= $i ?>"><?= $i ?></a>
      <?php endif; ?>
      <?php endfor; ?>
    </nav>
    <?php endif; ?>

    <?php else: ?>
    <p class="text-center" style="color:var(--gray);padding:3rem 0">Aucun article trouvé.</p>
    <?php endif; ?>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
