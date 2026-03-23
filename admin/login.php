<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../helpers.php';
require_once __DIR__ . '/auth.php';
if (!session_id()) session_start();
if (isLoggedIn()) { header('Location: index.php'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrfVerify();
    if (adminLogin(trim($_POST['username'] ?? ''), $_POST['password'] ?? '')) {
        redirect(BASE_URL . '/admin/index.php');
    } else {
        $error = 'Identifiants incorrects.';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin — CAPD ASBL</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    *{box-sizing:border-box;margin:0;padding:0}
    body{font-family:'Segoe UI',sans-serif;background:linear-gradient(135deg,#1a6b3c,#124d2b);min-height:100vh;display:flex;align-items:center;justify-content:center}
    .login-box{background:#fff;border-radius:12px;padding:2.5rem;width:100%;max-width:420px;box-shadow:0 20px 60px rgba(0,0,0,.3)}
    .login-logo{text-align:center;margin-bottom:2rem}
    .login-logo img{height:70px}
    .login-logo h2{color:#1a6b3c;margin-top:.75rem;font-size:1.3rem}
    .form-group{margin-bottom:1.25rem}
    label{display:block;font-weight:600;margin-bottom:.4rem;font-size:.9rem;color:#333}
    input{width:100%;padding:.75rem 1rem;border:2px solid #dee2e6;border-radius:8px;font-size:.95rem;transition:.3s}
    input:focus{outline:none;border-color:#1a6b3c;box-shadow:0 0 0 3px rgba(26,107,60,.1)}
    .btn{width:100%;padding:.85rem;background:#1a6b3c;color:#fff;border:none;border-radius:8px;font-size:1rem;font-weight:700;cursor:pointer;transition:.3s}
    .btn:hover{background:#124d2b}
    .alert{background:#f8d7da;color:#721c24;padding:.85rem 1rem;border-radius:8px;margin-bottom:1.25rem;font-size:.9rem}
    .back{text-align:center;margin-top:1.25rem;font-size:.875rem}
    .back a{color:#1a6b3c;font-weight:600}
  </style>
</head>
<body>
<div class="login-box">
  <div class="login-logo">
    <img src="<?= BASE_URL ?>/images/logo-capd-300x292.png" alt="CAPD ASBL">
    <h2>Administration</h2>
  </div>
  <?php if ($error): ?><div class="alert"><?= e($error) ?></div><?php endif; ?>
  <form method="POST">
    <div class="form-group">
      <label for="username">Nom d'utilisateur</label>
      <input type="text" id="username" name="username" required autocomplete="username">
    </div>
    <div class="form-group">
      <label for="password">Mot de passe</label>
      <input type="password" id="password" name="password" required autocomplete="current-password">
    </div>
    <button type="submit" class="btn"><i class="fas fa-sign-in-alt"></i> Connexion</button>
    <?= csrfField() ?>
  </form>
  <div class="back"><a href="<?= BASE_URL ?>">← Retour au site</a></div>
</div>
</body>
</html>
