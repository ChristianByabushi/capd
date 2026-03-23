<?php
require_once __DIR__ . '/../helpers.php';
require_once __DIR__ . '/../db.php';
$lang = initLang();
$siteName = getSetting('site_name') ?: 'CAPD ASBL';
$currentLang = getLang();
?>
<!DOCTYPE html>
<html lang="<?= $currentLang ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($pageTitle ?? $siteName) ?></title>
  <meta name="description" content="<?= e($pageDesc ?? 'CAPD ASBL — Paix, développement et jeunesse') ?>">
  <?php $favicon = getSetting('favicon'); ?>
  <link rel="icon" href="<?= $favicon ? BASE_URL.'/uploads/favicon/'.e($favicon) : BASE_URL.'/images/logo-capd-300x292.png' ?>" type="image/png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
  <?= $extraHead ?? '' ?>
</head>
<body>

<!-- ===== ANNOUNCEMENT BAR ===== -->
<div class="announce-bar" id="announceBar">
  <div class="announce-inner">
    <span class="announce-tag">INFO</span>
    <span>CAPD ASBL — Paix, développement et jeunesse consciente en RDC</span>
    <a href="<?= BASE_URL ?>/about.php"><?= t('read_more') ?> &rsaquo;</a>
  </div>
  <button class="announce-close" onclick="document.getElementById('announceBar').style.display='none'" aria-label="Fermer">&times;</button>
</div>

<!-- ===== MAIN NAVBAR ===== -->
<nav class="navbar" id="mainNavbar" role="navigation" aria-label="Navigation principale">
  <div class="navbar-inner">

    <!-- Brand -->
    <a href="<?= BASE_URL ?>" class="navbar-brand">
      <img src="<?= BASE_URL ?>/images/logo-capd-300x292.png" alt="<?= e($siteName) ?> logo">
      <div class="brand-text">
        <span class="brand-name"><?= e($siteName) ?></span>
        <span class="brand-sub">Paix &amp; Développement</span>
      </div>
    </a>

    <!-- Desktop nav -->
    <ul class="navbar-nav desktop-nav" role="list">
      <li class="nav-item has-dropdown">
        <a href="<?= BASE_URL ?>/about.php" class="nav-link <?= activeClass('about') ?>">
          <?= t('nav_about') ?> <i class="fas fa-chevron-down nav-arrow"></i>
        </a>
        <div class="dropdown-menu">
          <div class="dropdown-grid">
            <a href="<?= BASE_URL ?>/about.php" class="dropdown-item">
              <strong>Notre Vision</strong>
              <span>Paix perpétuelle et développement durable</span>
            </a>
            <a href="<?= BASE_URL ?>/members.php" class="dropdown-item">
              <strong><?= t('nav_members') ?></strong>
              <span>Conseil, comités et secrétariat</span>
            </a>
            <a href="<?= BASE_URL ?>/about.php#domains" class="dropdown-item">
              <strong>Domaines d'intervention</strong>
              <span>Éducation, santé, paix, environnement</span>
            </a>
            <a href="<?= BASE_URL ?>/about.php#organs" class="dropdown-item">
              <strong>Organes de gestion</strong>
              <span>Structure organisationnelle</span>
            </a>
          </div>
        </div>
      </li>
      <li class="nav-item has-dropdown">
        <a href="<?= BASE_URL ?>/activities.php" class="nav-link <?= activeClass('activities') ?>">
          <?= t('nav_activities') ?> <i class="fas fa-chevron-down nav-arrow"></i>
        </a>
        <div class="dropdown-menu">
          <div class="dropdown-grid">
            <a href="<?= BASE_URL ?>/activities.php" class="dropdown-item">
              <strong>Toutes les activités</strong>
              <span>Parcourir tous nos projets</span>
            </a>
            <a href="<?= BASE_URL ?>/activities.php?status=ongoing" class="dropdown-item">
              <strong>En cours</strong>
              <span>Projets actuellement actifs</span>
            </a>
            <a href="<?= BASE_URL ?>/activities.php?status=completed" class="dropdown-item">
              <strong>Réalisés</strong>
              <span>Projets terminés avec succès</span>
            </a>
            <a href="<?= BASE_URL ?>/formations.php" class="dropdown-item">
              <strong><?= t('nav_formations') ?></strong>
              <span>Centres et programmes de formation</span>
            </a>
          </div>
        </div>
      </li>
      <li class="nav-item has-dropdown">
        <a href="<?= BASE_URL ?>/blog.php" class="nav-link <?= activeClass('blog') ?>">
          Actualités <i class="fas fa-chevron-down nav-arrow"></i>
        </a>
        <div class="dropdown-menu">
          <div class="dropdown-grid">
            <a href="<?= BASE_URL ?>/blog.php?cat=news" class="dropdown-item">
              <strong>Nouvelles &amp; Histoires</strong>
              <span>Dernières nouvelles de nos projets</span>
            </a>
            <a href="<?= BASE_URL ?>/blog.php?cat=communique" class="dropdown-item">
              <strong>Communiqués</strong>
              <span>Déclarations officielles</span>
            </a>
            <a href="<?= BASE_URL ?>/blog.php?cat=report" class="dropdown-item">
              <strong>Rapports</strong>
              <span>Rapports d'activités et bilans</span>
            </a>
          </div>
        </div>
      </li>
      <li class="nav-item">
        <a href="<?= BASE_URL ?>/contact.php" class="nav-link <?= activeClass('contact') ?>"><?= t('nav_contact') ?></a>
      </li>

      <!-- Desktop lang switcher inline -->
      <li class="nav-item nav-lang-desktop">
        <div class="lang-switcher">
          <?php foreach (LANGUAGES as $code => $label): ?>
            <a href="?lang=<?= $code ?>"
               class="<?= $currentLang === $code ? 'active' : '' ?>"
               hreflang="<?= $code ?>"
               aria-label="<?= $label ?>">
              <?= strtoupper($code) ?>
            </a>
          <?php endforeach; ?>
        </div>
      </li>
    </ul>

    <!-- Right side: donate + hamburger -->
    <div class="navbar-right">
      <a href="<?= BASE_URL ?>/donate.php" class="btn-donate"><?= t('nav_donate') ?></a>
      <button class="navbar-toggle" id="mobileMenuBtn" aria-label="Ouvrir le menu" aria-expanded="false" aria-controls="mobileDrawer">
        <span class="hamburger-icon">
          <span></span><span></span><span></span>
        </span>
        <span class="menu-label">MENU</span>
      </button>
    </div>

  </div>
</nav>

<!-- ===== MOBILE FULL-SCREEN DRAWER ===== -->
<div class="mobile-drawer" id="mobileDrawer" aria-hidden="true" role="dialog" aria-label="Menu mobile">
  <div class="mobile-drawer-inner">

    <!-- Drawer header -->
    <div class="mobile-drawer-header">
      <a href="<?= BASE_URL ?>" class="navbar-brand">
        <img src="<?= BASE_URL ?>/images/logo-capd-300x292.png" alt="<?= e($siteName) ?>">
        <div class="brand-text">
          <span class="brand-name"><?= e($siteName) ?></span>
          <span class="brand-sub">Paix &amp; Développement</span>
        </div>
      </a>
      <button class="drawer-close" id="mobileMenuClose" aria-label="Fermer le menu">
        <i class="fas fa-times"></i>
        <span>FERMER</span>
      </button>
    </div>

    <!-- Donate banner inside drawer -->
    <div class="drawer-donate-banner">
      <span>Soutenez notre mission</span>
      <a href="<?= BASE_URL ?>/donate.php" class="btn-donate"><?= t('nav_donate') ?></a>
    </div>

    <!-- Mobile nav links with accordion -->
    <nav class="mobile-nav" aria-label="Navigation mobile">

      <div class="mobile-nav-item has-sub">
        <button class="mobile-nav-btn" aria-expanded="false">
          <?= t('nav_about') ?> <i class="fas fa-chevron-down"></i>
        </button>
        <div class="mobile-sub">
          <a href="<?= BASE_URL ?>/about.php">Notre Vision</a>
          <a href="<?= BASE_URL ?>/members.php"><?= t('nav_members') ?></a>
          <a href="<?= BASE_URL ?>/about.php#domains">Domaines d'intervention</a>
          <a href="<?= BASE_URL ?>/about.php#organs">Organes de gestion</a>
        </div>
      </div>

      <div class="mobile-nav-item has-sub">
        <button class="mobile-nav-btn" aria-expanded="false">
          <?= t('nav_activities') ?> <i class="fas fa-chevron-down"></i>
        </button>
        <div class="mobile-sub">
          <a href="<?= BASE_URL ?>/activities.php">Toutes les activités</a>
          <a href="<?= BASE_URL ?>/activities.php?status=ongoing">En cours</a>
          <a href="<?= BASE_URL ?>/activities.php?status=completed">Réalisés</a>
          <a href="<?= BASE_URL ?>/formations.php"><?= t('nav_formations') ?></a>
        </div>
      </div>

      <div class="mobile-nav-item has-sub">
        <button class="mobile-nav-btn" aria-expanded="false">
          Actualités <i class="fas fa-chevron-down"></i>
        </button>
        <div class="mobile-sub">
          <a href="<?= BASE_URL ?>/blog.php?cat=news">Nouvelles &amp; Histoires</a>
          <a href="<?= BASE_URL ?>/blog.php?cat=communique">Communiqués</a>
          <a href="<?= BASE_URL ?>/blog.php?cat=report">Rapports</a>
        </div>
      </div>

      <div class="mobile-nav-item">
        <a href="<?= BASE_URL ?>/contact.php" class="mobile-nav-link"><?= t('nav_contact') ?></a>
      </div>

    </nav>

    <!-- Mobile lang + extras -->
    <div class="drawer-footer">
      <div class="drawer-lang">
        <span>Langue :</span>
        <?php foreach (LANGUAGES as $code => $label): ?>
          <a href="?lang=<?= $code ?>"
             class="<?= $currentLang === $code ? 'active' : '' ?>"
             hreflang="<?= $code ?>">
            <?= $label ?>
          </a>
        <?php endforeach; ?>
      </div>
    </div>

  </div>
</div>
<!-- Drawer backdrop -->
<div class="drawer-backdrop" id="drawerBackdrop"></div>
