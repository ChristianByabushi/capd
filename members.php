<?php
require_once 'helpers.php';
require_once 'db.php';
$pageTitle = t('members_title') . ' — CAPD ASBL';
require_once 'includes/header.php';
$members = fetchAll("SELECT * FROM members WHERE is_active=1 ORDER BY display_order ASC, organ ASC");
$organs  = ['conseil_administration','comite_gestion','comite_controle','secretariat_executif','membre'];
$organKeys = ['conseil_administration'=>'organ_ca','comite_gestion'=>'organ_cg','comite_controle'=>'organ_cc','secretariat_executif'=>'organ_se','membre'=>'organ_membre'];
?>

<div class="page-hero">
  <div class="container">
    <h1><?= t('members_title') ?></h1>
    <nav class="breadcrumb" aria-label="Fil d'Ariane">
      <a href="<?= BASE_URL ?>"><?= t('nav_home') ?></a>
      <span>/</span><span><?= t('members_title') ?></span>
    </nav>
  </div>
</div>

<section class="section">
  <div class="container">
    <?php foreach ($organs as $organ):
      $group = array_filter($members, fn($m) => $m['organ'] === $organ);
      if (!$group) continue;
    ?>
    <h2 style="color:var(--primary);margin-bottom:1.5rem;padding-bottom:.5rem;border-bottom:3px solid var(--accent)">
      <?= t($organKeys[$organ]) ?>
    </h2>
    <div class="members-grid" style="margin-bottom:3.5rem">
      <?php foreach ($group as $m): ?>
      <div class="member-card">
        <?php if ($m['photo']): ?>
        <div class="member-photo">
          <img src="<?= BASE_URL ?>/uploads/<?= e($m['photo']) ?>" alt="<?= e($m['full_name']) ?>" loading="lazy">
        </div>
        <?php else: ?>
        <div class="member-photo-placeholder"><i class="fas fa-user" aria-hidden="true"></i></div>
        <?php endif; ?>
        <div class="member-info">
          <h3><?= e($m['full_name']) ?></h3>
          <span><?= e(loc($m,'position')) ?></span>
          <?php if (loc($m,'bio')): ?>
          <p style="font-size:.85rem;color:var(--gray);margin-top:.5rem;line-height:1.5"><?= e(truncate(loc($m,'bio'),120)) ?></p>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endforeach; ?>

    <?php if (!$members): ?>
    <p class="text-center" style="color:var(--gray);padding:3rem 0">Aucun membre à afficher.</p>
    <?php endif; ?>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
