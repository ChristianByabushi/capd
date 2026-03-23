<?php if (!session_id()) session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($adminTitle ?? 'Admin') ?> — CAPD Admin</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    *{box-sizing:border-box;margin:0;padding:0}
    body{font-family:'Segoe UI',sans-serif;background:#f0f2f5;color:#333;display:flex;min-height:100vh}
    a{text-decoration:none;color:inherit}
    /* Sidebar */
    .sidebar{width:260px;background:#1a1a2e;color:#ccc;display:flex;flex-direction:column;flex-shrink:0;position:fixed;top:0;left:0;bottom:0;overflow-y:auto;z-index:100}
    .sidebar-brand{padding:1.5rem;border-bottom:1px solid rgba(255,255,255,.08);display:flex;align-items:center;gap:.75rem}
    .sidebar-brand img{height:40px}
    .sidebar-brand span{font-weight:800;color:#fff;font-size:1rem}
    .sidebar-nav{padding:1rem 0;flex:1}
    .sidebar-nav a{display:flex;align-items:center;gap:.75rem;padding:.75rem 1.5rem;font-size:.9rem;transition:.2s;color:#aaa}
    .sidebar-nav a:hover,.sidebar-nav a.active{background:rgba(255,255,255,.07);color:#fff}
    .sidebar-nav a i{width:18px;text-align:center;color:#f4a61d}
    .sidebar-section{padding:.5rem 1.5rem;font-size:.7rem;text-transform:uppercase;letter-spacing:.1em;color:#555;margin-top:.5rem}
    .sidebar-footer{padding:1rem 1.5rem;border-top:1px solid rgba(255,255,255,.08);font-size:.85rem}
    .sidebar-footer a{color:#aaa;display:flex;align-items:center;gap:.5rem}
    .sidebar-footer a:hover{color:#f4a61d}
    /* Main */
    .admin-main{margin-left:260px;flex:1;display:flex;flex-direction:column;min-height:100vh}
    .admin-topbar{background:#fff;padding:1rem 2rem;display:flex;align-items:center;justify-content:space-between;box-shadow:0 2px 8px rgba(0,0,0,.06);position:sticky;top:0;z-index:50}
    .admin-topbar h1{font-size:1.2rem;color:#1a6b3c}
    .admin-topbar .user{display:flex;align-items:center;gap:.75rem;font-size:.9rem}
    .admin-topbar .user i{color:#1a6b3c}
    .admin-content{padding:2rem;flex:1}
    /* Cards */
    .admin-cards{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:1.5rem;margin-bottom:2rem}
    .admin-card{background:#fff;border-radius:10px;padding:1.5rem;box-shadow:0 2px 10px rgba(0,0,0,.06);display:flex;align-items:center;gap:1rem}
    .admin-card-icon{width:55px;height:55px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;color:#fff;flex-shrink:0}
    .admin-card h3{font-size:1.6rem;font-weight:800;color:#1a1a2e}
    .admin-card p{font-size:.8rem;color:#888;margin-top:.2rem}
    /* Table */
    .admin-table-wrap{background:#fff;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,.06);overflow:hidden}
    .admin-table-header{padding:1.25rem 1.5rem;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid #f0f0f0}
    .admin-table-header h2{font-size:1rem;color:#1a1a2e}
    table{width:100%;border-collapse:collapse}
    th{background:#f8f9fa;padding:.85rem 1rem;text-align:left;font-size:.8rem;text-transform:uppercase;letter-spacing:.05em;color:#888;font-weight:700}
    td{padding:.85rem 1rem;border-bottom:1px solid #f5f5f5;font-size:.9rem;vertical-align:middle}
    tr:last-child td{border-bottom:none}
    tr:hover td{background:#fafafa}
    /* Badges */
    .badge{display:inline-block;padding:.25rem .65rem;border-radius:50px;font-size:.75rem;font-weight:700}
    .badge-green{background:#d4edda;color:#155724}
    .badge-orange{background:#fff3cd;color:#856404}
    .badge-red{background:#f8d7da;color:#721c24}
    .badge-blue{background:#cce5ff;color:#004085}
    /* Forms */
    .admin-form{background:#fff;border-radius:10px;padding:2rem;box-shadow:0 2px 10px rgba(0,0,0,.06)}
    .form-row{display:grid;grid-template-columns:1fr 1fr;gap:1.25rem}
    .form-group{margin-bottom:1.25rem}
    .form-group label{display:block;font-weight:600;margin-bottom:.4rem;font-size:.875rem;color:#444}
    .form-control{width:100%;padding:.7rem .9rem;border:2px solid #e9ecef;border-radius:8px;font-size:.9rem;transition:.2s;background:#fff}
    .form-control:focus{outline:none;border-color:#1a6b3c;box-shadow:0 0 0 3px rgba(26,107,60,.1)}
    textarea.form-control{resize:vertical;min-height:120px}
    select.form-control{cursor:pointer}
    .btn{display:inline-flex;align-items:center;gap:.5rem;padding:.65rem 1.4rem;border-radius:8px;font-weight:600;font-size:.875rem;cursor:pointer;border:2px solid transparent;transition:.2s}
    .btn-primary{background:#1a6b3c;color:#fff;border-color:#1a6b3c}
    .btn-primary:hover{background:#124d2b}
    .btn-accent{background:#f4a61d;color:#fff;border-color:#f4a61d}
    .btn-accent:hover{background:#d4880a}
    .btn-danger{background:#dc3545;color:#fff;border-color:#dc3545}
    .btn-danger:hover{background:#b02a37}
    .btn-sm{padding:.4rem .9rem;font-size:.8rem}
    .btn-outline{background:transparent;color:#1a6b3c;border-color:#1a6b3c}
    .btn-outline:hover{background:#1a6b3c;color:#fff}
    .alert{padding:.85rem 1rem;border-radius:8px;margin-bottom:1.25rem;font-size:.9rem}
    .alert-success{background:#d4edda;color:#155724}
    .alert-error{background:#f8d7da;color:#721c24}
    .img-thumb{width:50px;height:50px;object-fit:cover;border-radius:6px}
    @media(max-width:768px){.sidebar{transform:translateX(-100%)}.admin-main{margin-left:0}.form-row{grid-template-columns:1fr}}
  </style>
</head>
<body>

<aside class="sidebar">
  <div class="sidebar-brand">
    <img src="<?= BASE_URL ?>/images/logo-capd-300x292.png" alt="CAPD">
    <span>CAPD Admin</span>
  </div>
  <nav class="sidebar-nav">
    <div class="sidebar-section">Principal</div>
    <a href="<?= BASE_URL ?>/admin/index.php" <?= basename($_SERVER['PHP_SELF'])==='index.php'?'class="active"':'' ?>><i class="fas fa-tachometer-alt"></i> Tableau de bord</a>

    <div class="sidebar-section">Contenu</div>
    <?php if (can('manage_hero')): ?>
    <a href="<?= BASE_URL ?>/admin/hero.php" <?= basename($_SERVER['PHP_SELF'])==='hero.php'?'class="active"':'' ?>><i class="fas fa-images"></i> Slides Hero</a>
    <?php endif; ?>
    <?php if (can('manage_activities')): ?>
    <a href="<?= BASE_URL ?>/admin/activities.php" <?= basename($_SERVER['PHP_SELF'])==='activities.php'?'class="active"':'' ?>><i class="fas fa-project-diagram"></i> Activités</a>
    <?php endif; ?>
    <?php if (can('manage_blog')): ?>
    <a href="<?= BASE_URL ?>/admin/blog.php" <?= basename($_SERVER['PHP_SELF'])==='blog.php'?'class="active"':'' ?>><i class="fas fa-newspaper"></i> Blog</a>
    <?php endif; ?>
    <?php if (can('manage_formations')): ?>
    <a href="<?= BASE_URL ?>/admin/formations.php" <?= basename($_SERVER['PHP_SELF'])==='formations.php'?'class="active"':'' ?>><i class="fas fa-chalkboard-teacher"></i> Formations</a>
    <?php endif; ?>
    <?php if (can('manage_feedbacks')): ?>
    <a href="<?= BASE_URL ?>/admin/feedbacks.php" <?= basename($_SERVER['PHP_SELF'])==='feedbacks.php'?'class="active"':'' ?>><i class="fas fa-star"></i> Témoignages</a>
    <?php endif; ?>

    <?php if (isAdmin()): ?>
    <div class="sidebar-section">Organisation</div>
    <?php if (can('manage_members')): ?>
    <a href="<?= BASE_URL ?>/admin/members.php" <?= basename($_SERVER['PHP_SELF'])==='members.php'?'class="active"':'' ?>><i class="fas fa-users"></i> Membres</a>
    <?php endif; ?>
    <?php if (can('manage_partners')): ?>
    <a href="<?= BASE_URL ?>/admin/partners.php" <?= basename($_SERVER['PHP_SELF'])==='partners.php'?'class="active"':'' ?>><i class="fas fa-handshake"></i> Partenaires</a>
    <?php endif; ?>
    <?php if (can('manage_stats')): ?>
    <a href="<?= BASE_URL ?>/admin/stats.php" <?= basename($_SERVER['PHP_SELF'])==='stats.php'?'class="active"':'' ?>><i class="fas fa-chart-bar"></i> Statistiques</a>
    <?php endif; ?>
    <?php endif; ?>

    <div class="sidebar-section">Système</div>
    <?php if (can('view_donations')): ?>
    <a href="<?= BASE_URL ?>/admin/donations.php" <?= basename($_SERVER['PHP_SELF'])==='donations.php'?'class="active"':'' ?>><i class="fas fa-donate"></i> Dons reçus</a>
    <?php endif; ?>
    <?php if (can('view_messages')): ?>
    <a href="<?= BASE_URL ?>/admin/messages.php" <?= basename($_SERVER['PHP_SELF'])==='messages.php'?'class="active"':'' ?>><i class="fas fa-envelope"></i> Messages</a>
    <?php endif; ?>
    <?php if (can('manage_users')): ?>
    <a href="<?= BASE_URL ?>/admin/users.php" <?= basename($_SERVER['PHP_SELF'])==='users.php'?'class="active"':'' ?>><i class="fas fa-user-shield"></i> Utilisateurs</a>
    <?php endif; ?>
    <?php if (can('manage_settings')): ?>
    <a href="<?= BASE_URL ?>/admin/settings.php" <?= basename($_SERVER['PHP_SELF'])==='settings.php'?'class="active"':'' ?>><i class="fas fa-cog"></i> Paramètres</a>
    <?php endif; ?>
  </nav>
  <div class="sidebar-footer">
    <a href="<?= BASE_URL ?>" target="_blank"><i class="fas fa-external-link-alt"></i> Voir le site</a>
    <a href="<?= BASE_URL ?>/admin/logout.php" style="margin-top:.5rem"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
  </div>
</aside>

<main class="admin-main">
  <div class="admin-topbar">
    <h1><?= e($adminTitle ?? 'Dashboard') ?></h1>
    <div class="user">
      <i class="fas fa-user-circle"></i>
      <span><?= e($_SESSION['admin_name'] ?? 'Admin') ?></span>
      <?php
        $roleLabel = ['superadmin'=>'Superadmin','admin'=>'Admin','editor'=>'Éditeur'];
        $roleColor = ['superadmin'=>'#dc3545','admin'=>'#f4a61d','editor'=>'#17a2b8'];
        $r = $_SESSION['admin_role'] ?? '';
      ?>
      <span style="background:<?= $roleColor[$r]??'#888' ?>;color:#fff;padding:.15rem .6rem;border-radius:50px;font-size:.72rem;font-weight:700"><?= $roleLabel[$r]??$r ?></span>
    </div>
  </div>
  <div class="admin-content">
