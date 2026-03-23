<?php
require_once 'helpers.php';
require_once 'db.php';
$pageTitle = t('formations_title') . ' — CAPD ASBL';
require_once 'includes/header.php';
$centres   = fetchAll("SELECT * FROM centres WHERE is_active=1 ORDER BY display_order ASC");
$feedbacks = fetchAll("SELECT f.*, c.name_fr, c.name_en, c.name_sw FROM feedbacks f LEFT JOIN centres c ON f.centre_id=c.id WHERE f.is_approved=1 ORDER BY f.created_at DESC LIMIT 9");
?>

<div class="page-hero">
  <div class="container">
    <h1><?= t('formations_title') ?></h1>
    <nav class="breadcrumb" aria-label="Fil d'Ariane">
      <a href="<?= BASE_URL ?>"><?= t('nav_home') ?></a>
      <span>/</span><span><?= t('formations_title') ?></span>
    </nav>
  </div>
</div>

<!-- Centres -->
<section class="section">
  <div class="container">
    <div class="section-title">
      <h2><?= t('formations_title') ?></h2>
    </div>
    <?php if ($centres): ?>
    <div class="cards-grid">
      <?php foreach ($centres as $c): ?>
      <article class="card">
        <div class="card-img">
          <?php if ($c['cover_image']): ?>
          <img src="<?= BASE_URL ?>/uploads/<?= e($c['cover_image']) ?>" alt="<?= e(loc($c,'name')) ?>" loading="lazy">
          <?php else: ?>
          <div style="height:220px;background:linear-gradient(135deg,var(--primary),var(--primary-light));display:flex;align-items:center;justify-content:center">
            <i class="fas fa-chalkboard-teacher" style="font-size:3rem;color:rgba(255,255,255,.4)" aria-hidden="true"></i>
          </div>
          <?php endif; ?>
          <span class="card-badge"><?= e($c['domain']) ?></span>
        </div>
        <div class="card-body">
          <h3><?= e(loc($c,'name')) ?></h3>
          <p><?= e(truncate(loc($c,'description'), 130)) ?></p>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <p class="text-center" style="color:var(--gray);padding:3rem 0">Aucune formation disponible pour le moment.</p>
    <?php endif; ?>
  </div>
</section>

<!-- Feedbacks -->
<?php if ($feedbacks): ?>
<section class="section section-alt" aria-labelledby="feedbacks-title">
  <div class="container">
    <div class="section-title">
      <h2 id="feedbacks-title"><?= t('feedbacks_title') ?></h2>
    </div>
    <div class="testimonials-grid">
      <?php foreach ($feedbacks as $fb): ?>
      <div class="testimonial-card">
        <div class="stars" aria-label="<?= $fb['rating'] ?> étoiles">
          <?php for ($i=0;$i<5;$i++): ?>
          <i class="fas fa-star" style="<?= $i>=$fb['rating']?'opacity:.3':'' ?>" aria-hidden="true"></i>
          <?php endfor; ?>
        </div>
        <p>"<?= e($fb['feedback_text']) ?>"</p>
        <div class="testimonial-author">
          — <?= e($fb['learner_name']) ?>
          <?php if ($fb['name_fr']): ?><span style="color:var(--gray);font-weight:400"> · <?= e(loc($fb,'name')) ?></span><?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
