<?php
require_once 'helpers.php';
require_once 'db.php';
http_response_code(404);
$pageTitle = t('not_found_title') . ' — CAPD ASBL';
require_once 'includes/header.php';
?>

<div class="not-found">
  <div class="not-found-inner">
    <div class="big-404">404</div>
    <h2><?= t('not_found_title') ?></h2>
    <p><?= t('not_found_msg') ?></p>
    <div class="not-found-actions">
      <a href="<?= BASE_URL ?>" class="btn btn-primary">
        <i class="fas fa-home" aria-hidden="true"></i> <?= t('back_home') ?>
      </a>
      <a href="<?= BASE_URL ?>/contact.php" class="btn btn-outline-primary">
        <i class="fas fa-envelope" aria-hidden="true"></i> <?= t('nav_contact') ?>
      </a>
    </div>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>
