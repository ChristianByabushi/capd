<?php
require_once 'helpers.php';
require_once 'db.php';
$slug = $_GET['slug'] ?? '';
$post = fetchOne("SELECT * FROM posts WHERE slug=? AND is_published=1", [$slug]);
if (!$post) { header('Location: 404.php'); exit; }
$pageTitle = e(loc($post,'title')) . ' — CAPD ASBL';
require_once 'includes/header.php';
$shareUrl   = urlencode(BASE_URL . '/post.php?slug=' . $slug);
$shareTitle = urlencode(loc($post,'title'));
$related = fetchAll("SELECT * FROM posts WHERE is_published=1 AND id!=? AND category=? ORDER BY published_at DESC LIMIT 3", [$post['id'], $post['category']]);
?>

<div class="page-hero">
  <div class="container">
    <span style="background:var(--accent);color:white;padding:.25rem .75rem;border-radius:50px;font-size:.8rem;font-weight:700"><?= e($post['category']) ?></span>
    <h1 style="margin-top:1rem"><?= e(loc($post,'title')) ?></h1>
    <nav class="breadcrumb" aria-label="Fil d'Ariane">
      <a href="<?= BASE_URL ?>"><?= t('nav_home') ?></a>
      <span>/</span>
      <a href="blog.php"><?= t('blog_title') ?></a>
      <span>/</span><span><?= e(loc($post,'title')) ?></span>
    </nav>
  </div>
</div>

<section class="section">
  <div class="container">
    <div class="single-content">

      <div class="single-meta">
        <?php if ($post['published_at']): ?><span><i class="fas fa-calendar" aria-hidden="true"></i> <?= formatDate($post['published_at']) ?></span><?php endif; ?>
      </div>

      <?php if ($post['cover_image']): ?>
      <div class="single-cover">
        <img src="<?= BASE_URL ?>/uploads/<?= e($post['cover_image']) ?>" alt="<?= e(loc($post,'title')) ?>">
      </div>
      <?php endif; ?>

      <div class="single-body">
        <?= nl2br(e(loc($post,'content'))) ?>
      </div>

      <?php if (!empty($post['youtube_url'])): ?>
      <div style="margin:2rem 0"><?= youtubeEmbed($post['youtube_url'], loc($post,'title')) ?></div>
      <?php endif; ?>

      <div class="share-buttons">
        <span style="font-weight:600;color:var(--gray)"><?= t('share_on') ?>:</span>
        <a href="https://wa.me/?text=<?= $shareTitle ?>%20<?= $shareUrl ?>" target="_blank" rel="noopener" class="share-btn whatsapp"><i class="fab fa-whatsapp" aria-hidden="true"></i> WhatsApp</a>
        <a href="https://www.facebook.com/sharer/sharer.php?u=<?= $shareUrl ?>" target="_blank" rel="noopener" class="share-btn facebook"><i class="fab fa-facebook-f" aria-hidden="true"></i> Facebook</a>
        <a href="https://twitter.com/intent/tweet?text=<?= $shareTitle ?>&url=<?= $shareUrl ?>" target="_blank" rel="noopener" class="share-btn twitter"><i class="fab fa-twitter" aria-hidden="true"></i> Twitter</a>
      </div>

      <div style="margin-top:2rem">
        <a href="blog.php" class="btn btn-outline-primary"><i class="fas fa-arrow-left" aria-hidden="true"></i> <?= t('view_all') ?></a>
      </div>
    </div>

    <!-- Related posts -->
    <?php if ($related): ?>
    <div style="margin-top:4rem">
      <h3 style="color:var(--primary);margin-bottom:1.5rem">Articles similaires</h3>
      <div class="cards-grid">
        <?php foreach ($related as $r): ?>
        <article class="card">
          <div class="card-img">
            <?php if ($r['cover_image']): ?>
            <img src="<?= BASE_URL ?>/uploads/<?= e($r['cover_image']) ?>" alt="<?= e(loc($r,'title')) ?>" loading="lazy">
            <?php else: ?>
            <div style="height:180px;background:linear-gradient(135deg,var(--accent),var(--accent-dark))"></div>
            <?php endif; ?>
          </div>
          <div class="card-body">
            <h3><a href="post.php?slug=<?= e($r['slug']) ?>"><?= e(loc($r,'title')) ?></a></h3>
            <p><?= e(truncate(loc($r,'excerpt') ?: loc($r,'content'), 100)) ?></p>
          </div>
        </article>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
