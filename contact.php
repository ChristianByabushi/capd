<?php
require_once 'helpers.php';
require_once 'db.php';
$pageTitle = t('contact_title') . ' — CAPD ASBL';
require_once 'includes/header.php';

$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrfVerify();
    $name    = strip_tags(trim($_POST['name'] ?? ''));
    $email   = trim($_POST['email'] ?? '');
    $subject = strip_tags(trim($_POST['subject'] ?? ''));
    $message = strip_tags(trim($_POST['message'] ?? ''));

    if ($name && $email && $message && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Save to DB
        query("INSERT INTO contact_messages (sender_name, sender_email, subject, message) VALUES (?,?,?,?)",
              'ssss', [$name, $email, $subject, $message]);

        // Try to send email via PHP mail (or SMTP if configured)
        $to      = getSetting('site_email');
        $headers = "From: {$name} <{$email}>\r\nReply-To: {$email}\r\nContent-Type: text/plain; charset=UTF-8";
        @mail($to, "[CAPD] $subject", $message, $headers);

        $success = t('contact_success');
    } else {
        $error = t('contact_error');
    }
}

$address  = getSetting('address_' . getLang()) ?: getSetting('address_fr');
$phone    = getSetting('phone');
$email_s  = getSetting('site_email');
$mapEmbed = getSetting('google_maps_embed');
?>

<div class="page-hero">
  <div class="container">
    <h1><?= t('contact_title') ?></h1>
    <nav class="breadcrumb" aria-label="Fil d'Ariane">
      <a href="<?= BASE_URL ?>"><?= t('nav_home') ?></a>
      <span>/</span><span><?= t('contact_title') ?></span>
    </nav>
  </div>
</div>

<section class="section">
  <div class="container">
    <div class="contact-grid">

      <!-- Info -->
      <div>
        <h3><?= t('contact_title') ?></h3>
        <?php if ($address): ?>
        <div class="contact-item">
          <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
          <div><strong>Adresse</strong><span><?= e($address) ?></span></div>
        </div>
        <?php endif; ?>
        <?php if ($phone): ?>
        <div class="contact-item">
          <i class="fas fa-phone" aria-hidden="true"></i>
          <div><strong>Téléphone</strong><span><a href="tel:<?= e($phone) ?>"><?= e($phone) ?></a></span></div>
        </div>
        <?php endif; ?>
        <?php if ($email_s): ?>
        <div class="contact-item">
          <i class="fas fa-envelope" aria-hidden="true"></i>
          <div><strong>Email</strong><span><a href="mailto:<?= e($email_s) ?>"><?= e($email_s) ?></a></span></div>
        </div>
        <?php endif; ?>

        <?php if ($mapEmbed): ?>
        <div class="map-embed">
          <?= $mapEmbed ?>
        </div>
        <?php else: ?>
        <div class="map-embed" style="background:var(--light);height:250px;display:flex;align-items:center;justify-content:center;border-radius:var(--radius)">
          <div style="text-align:center;color:var(--gray)">
            <i class="fas fa-map-marked-alt" style="font-size:2.5rem;margin-bottom:.75rem" aria-hidden="true"></i>
            <p>Carte Google Maps<br><small>Configurable dans l'administration</small></p>
          </div>
        </div>
        <?php endif; ?>
      </div>

      <!-- Form -->
      <div>
        <?php if ($success): ?><div class="alert alert-success" role="alert"><?= e($success) ?></div><?php endif; ?>
        <?php if ($error):   ?><div class="alert alert-error"   role="alert"><?= e($error) ?></div><?php endif; ?>

        <form method="POST" action="contact.php" novalidate>
          <div class="form-group">
            <label for="name"><?= t('contact_name') ?> *</label>
            <input type="text" id="name" name="name" class="form-control" required
                   value="<?= e($_POST['name'] ?? '') ?>" autocomplete="name">
          </div>
          <div class="form-group">
            <label for="email"><?= t('contact_email') ?> *</label>
            <input type="email" id="email" name="email" class="form-control" required
                   value="<?= e($_POST['email'] ?? '') ?>" autocomplete="email">
          </div>
          <div class="form-group">
            <label for="subject"><?= t('contact_subject') ?></label>
            <input type="text" id="subject" name="subject" class="form-control"
                   value="<?= e($_POST['subject'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label for="message"><?= t('contact_message') ?> *</label>
            <textarea id="message" name="message" class="form-control" required><?= e($_POST['message'] ?? '') ?></textarea>
          </div>
          <button type="submit" class="btn btn-primary"><?= t('contact_send') ?> <i class="fas fa-paper-plane" aria-hidden="true"></i></button>
          <?= csrfField() ?>
        </form>
      </div>

    </div>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
