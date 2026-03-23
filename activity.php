<?php
require_once 'helpers.php';
require_once 'db.php';
$slug = $_GET['slug'] ?? '';
$act  = fetchOne("SELECT * FROM activities WHERE slug=?", [$slug]);
if (!$act) { header('Location: 404.php'); exit; }
$media = fetchAll("SELECT * FROM activity_media WHERE activity_id=?", [$act['id']]);
$pageTitle = e(loc($act,'title')) . ' — CAPD ASBL';
require_once 'includes/header.php';
$shareUrl = urlencode(BASE_URL . '/activity.php?slug=' . $slug);
$shareTitle = urlencode(loc($act,'title'));
?>

<div class="page-hero">
  <div class="container">
    <h1><?= e(loc($act,'title')) ?></h1>
    <nav class="breadcrumb" aria-label="Fil d'Ariane">
      <a href="<?= BASE_URL ?>"><?= t('nav_home') ?></a>
      <span>/</span>
      <a href="activities.php"><?= t('activities_title') ?></a>
      <span>/</span><span><?= e(loc($act,'title')) ?></span>
    </nav>
  </div>
</div>

<section class="section">
  <div class="container">
    <div class="single-content">

      <div class="single-meta">
        <?php if ($act['date_start']): ?><span><i class="fas fa-calendar" aria-hidden="true"></i> <?= formatDate($act['date_start']) ?><?= $act['date_end'] ? ' → ' . formatDate($act['date_end']) : '' ?></span><?php endif; ?>
        <?php if ($act['department']): ?><span><i class="fas fa-tag" aria-hidden="true"></i> <?= e($act['department']) ?></span><?php endif; ?>
        <span><i class="fas fa-circle" aria-hidden="true"></i> <?= e($act['status']) ?></span>
      </div>

      <?php if ($act['cover_image']): ?>
      <div class="single-cover">
        <img src="<?= BASE_URL ?>/uploads/<?= e($act['cover_image']) ?>" alt="<?= e(loc($act,'title')) ?>">
      </div>
      <?php endif; ?>

      <div class="single-body">
        <?= nl2br(e(loc($act,'description'))) ?>
        <?php if (loc($act,'objectives')): ?>
        <h3><?= getLang()==='fr'?'Objectifs':(getLang()==='en'?'Objectives':'Malengo') ?></h3>
        <?= nl2br(e(loc($act,'objectives'))) ?>
        <?php endif; ?>
      </div>

      <?php if (!empty($act['youtube_url'])): ?>
      <div style="margin:2rem 0"><?= youtubeEmbed($act['youtube_url'], loc($act,'title')) ?></div>
      <?php endif; ?>

      <!-- Media gallery -->
      <?php if ($media): ?>
      <div style="margin-top:2rem">
        <h3 style="color:var(--primary);margin-bottom:1rem">Galerie</h3>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:1rem">
          <?php foreach ($media as $m): ?>
          <?php if ($m['media_type']==='image' && $m['file_path']): ?>
          <div style="border-radius:var(--radius);overflow:hidden;height:160px">
            <img src="<?= BASE_URL ?>/uploads/<?= e($m['file_path']) ?>" alt="<?= e($m['caption']) ?>" style="width:100%;height:100%;object-fit:cover" loading="lazy">
          </div>
          <?php elseif ($m['media_type']==='video' && $m['video_url']): ?>
          <div style="border-radius:var(--radius);overflow:hidden">
            <iframe src="<?= e($m['video_url']) ?>" style="width:100%;height:160px;border:none" allowfullscreen title="<?= e($m['caption']) ?>"></iframe>
          </div>
          <?php endif; ?>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>

      <!-- Share -->
      <div class="share-buttons">
        <span style="font-weight:600;color:var(--gray)"><?= t('share_on') ?>:</span>
        <a href="https://wa.me/?text=<?= $shareTitle ?>%20<?= $shareUrl ?>" target="_blank" rel="noopener" class="share-btn whatsapp"><i class="fab fa-whatsapp" aria-hidden="true"></i> WhatsApp</a>
        <a href="https://www.facebook.com/sharer/sharer.php?u=<?= $shareUrl ?>" target="_blank" rel="noopener" class="share-btn facebook"><i class="fab fa-facebook-f" aria-hidden="true"></i> Facebook</a>
        <a href="https://twitter.com/intent/tweet?text=<?= $shareTitle ?>&url=<?= $shareUrl ?>" target="_blank" rel="noopener" class="share-btn twitter"><i class="fab fa-twitter" aria-hidden="true"></i> Twitter</a>
      </div>

      <div style="margin-top:2rem">
        <a href="activities.php" class="btn btn-outline-primary"><i class="fas fa-arrow-left" aria-hidden="true"></i> <?= t('view_all') ?></a>
      </div>
    </div>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
