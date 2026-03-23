<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/auth.php';
requireLogin();
$adminTitle = 'Paramètres';
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $keys = ['site_name','site_email','smtp_host','smtp_port','smtp_user','smtp_pass','smtp_from',
             'address_fr','address_en','address_sw','phone','facebook','twitter','youtube',
             'instagram','linkedin','tiktok','airtel_money_number','airtel_money_name',
             'google_maps_embed','default_lang'];
    foreach ($keys as $key) {
        $val = $_POST[$key] ?? '';
        query("INSERT INTO settings (setting_key, setting_value) VALUES (?,?) ON DUPLICATE KEY UPDATE setting_value=?", [$key, $val, $val]);
    }
    // Handle favicon upload
    if (!empty($_FILES['favicon']['tmp_name'])) {
        $ext = strtolower(pathinfo($_FILES['favicon']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['png','ico','jpg','jpeg','svg','gif'])) {
            $dir = __DIR__ . '/../uploads/favicon/';
            if (!is_dir($dir)) mkdir($dir, 0755, true);
            $filename = 'favicon.' . $ext;
            move_uploaded_file($_FILES['favicon']['tmp_name'], $dir . $filename);
            query("INSERT INTO settings (setting_key, setting_value) VALUES ('favicon',?) ON DUPLICATE KEY UPDATE setting_value=?", [$filename, $filename]);
        }
    }
    $msg = 'Paramètres enregistrés.';
}

$settings = [];
$rows = fetchAll("SELECT setting_key, setting_value FROM settings");
foreach ($rows as $r) $settings[$r['setting_key']] = $r['setting_value'];
$s = fn($k) => e($settings[$k] ?? '');

require_once 'includes/admin_header.php';
?>

<?php if ($msg): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>

<form method="POST" enctype="multipart/form-data">
<div class="admin-form" style="margin-bottom:1.5rem">
  <h3 style="color:#1a6b3c;margin-bottom:1.5rem">Informations générales</h3>
  <div class="form-row">
    <div class="form-group"><label>Nom du site</label><input type="text" name="site_name" class="form-control" value="<?= $s('site_name') ?>"></div>
    <div class="form-group"><label>Email de contact</label><input type="email" name="site_email" class="form-control" value="<?= $s('site_email') ?>"></div>
  </div>
  <div class="form-row">
    <div class="form-group"><label>Téléphone</label><input type="text" name="phone" class="form-control" value="<?= $s('phone') ?>"></div>
    <div class="form-group"><label>Langue par défaut</label>
      <select name="default_lang" class="form-control">
        <option value="fr" <?= $settings['default_lang']==='fr'?'selected':'' ?>>Français</option>
        <option value="en" <?= $settings['default_lang']==='en'?'selected':'' ?>>English</option>
        <option value="sw" <?= $settings['default_lang']==='sw'?'selected':'' ?>>Kiswahili</option>
      </select>
    </div>
  </div>
  <div class="form-group"><label>Adresse (FR)</label><input type="text" name="address_fr" class="form-control" value="<?= $s('address_fr') ?>"></div>
  <div class="form-group"><label>Address (EN)</label><input type="text" name="address_en" class="form-control" value="<?= $s('address_en') ?>"></div>
  <div class="form-group"><label>Anwani (SW)</label><input type="text" name="address_sw" class="form-control" value="<?= $s('address_sw') ?>"></div>
  <div class="form-group">
    <label>Favicon (icône onglet navigateur)</label>
    <div style="display:flex;align-items:center;gap:1rem;flex-wrap:wrap">
      <?php $fav = $settings['favicon'] ?? ''; if ($fav): ?>
        <img src="<?= BASE_URL ?>/uploads/favicon/<?= e($fav) ?>" alt="favicon actuel" style="width:32px;height:32px;object-fit:contain;border:1px solid #ddd;border-radius:4px;padding:2px">
        <small style="color:#888">Favicon actuel</small>
      <?php endif; ?>
      <input type="file" name="favicon" class="form-control" accept=".png,.ico,.jpg,.jpeg,.svg,.gif" style="flex:1;min-width:200px">
    </div>
    <small style="color:#888">Formats acceptés : PNG, ICO, SVG, JPG. Taille recommandée : 32×32 ou 64×64 px. Laissez vide pour conserver l'actuel.</small>
  </div>
</div>

<div class="admin-form" style="margin-bottom:1.5rem">
  <h3 style="color:#1a6b3c;margin-bottom:1.5rem">Réseaux sociaux</h3>
  <div class="form-row">
    <div class="form-group"><label><i class="fab fa-facebook" style="color:#1877F2"></i> Facebook URL</label><input type="url" name="facebook" class="form-control" value="<?= $s('facebook') ?>"></div>
    <div class="form-group"><label><i class="fab fa-twitter" style="color:#1DA1F2"></i> Twitter URL</label><input type="url" name="twitter" class="form-control" value="<?= $s('twitter') ?>"></div>
  </div>
  <div class="form-row">
    <div class="form-group"><label><i class="fab fa-instagram" style="color:#E1306C"></i> Instagram URL</label><input type="url" name="instagram" class="form-control" value="<?= $s('instagram') ?>"></div>
    <div class="form-group"><label><i class="fab fa-youtube" style="color:#FF0000"></i> YouTube URL</label><input type="url" name="youtube" class="form-control" value="<?= $s('youtube') ?>"></div>
  </div>
  <div class="form-row">
    <div class="form-group"><label><i class="fab fa-linkedin" style="color:#0A66C2"></i> LinkedIn URL</label><input type="url" name="linkedin" class="form-control" value="<?= $s('linkedin') ?>"></div>
    <div class="form-group"><label><i class="fab fa-tiktok"></i> TikTok URL</label><input type="url" name="tiktok" class="form-control" value="<?= $s('tiktok') ?>"></div>
  </div>
</div>

<div class="admin-form" style="margin-bottom:1.5rem">
  <h3 style="color:#1a6b3c;margin-bottom:1.5rem">Airtel Money (Dons)</h3>
  <div class="form-row">
    <div class="form-group"><label>Numéro Airtel Money</label><input type="text" name="airtel_money_number" class="form-control" value="<?= $s('airtel_money_number') ?>" placeholder="+243 0XX XXX XXXX"></div>
    <div class="form-group"><label>Nom du bénéficiaire</label><input type="text" name="airtel_money_name" class="form-control" value="<?= $s('airtel_money_name') ?>" placeholder="CAPD ASBL"></div>
  </div>
</div>

<div class="admin-form" style="margin-bottom:1.5rem">
  <h3 style="color:#1a6b3c;margin-bottom:1.5rem">Configuration email (SMTP)</h3>
  <div class="form-row">
    <div class="form-group"><label>SMTP Host</label><input type="text" name="smtp_host" class="form-control" value="<?= $s('smtp_host') ?>"></div>
    <div class="form-group"><label>SMTP Port</label><input type="number" name="smtp_port" class="form-control" value="<?= $s('smtp_port') ?>"></div>
  </div>
  <div class="form-row">
    <div class="form-group"><label>SMTP User</label><input type="text" name="smtp_user" class="form-control" value="<?= $s('smtp_user') ?>"></div>
    <div class="form-group"><label>SMTP Password</label><input type="password" name="smtp_pass" class="form-control" value="<?= $s('smtp_pass') ?>"></div>
  </div>
  <div class="form-group"><label>From Email</label><input type="email" name="smtp_from" class="form-control" value="<?= $s('smtp_from') ?>"></div>
</div>

<div class="admin-form" style="margin-bottom:1.5rem">
  <h3 style="color:#1a6b3c;margin-bottom:1.5rem">Google Maps</h3>
  <div class="form-group">
    <label>Code embed iframe Google Maps</label>
    <textarea name="google_maps_embed" class="form-control" rows="4" placeholder='&lt;iframe src="https://www.google.com/maps/embed?..."&gt;&lt;/iframe&gt;'><?= $s('google_maps_embed') ?></textarea>
    <small style="color:#888">Copiez le code embed depuis Google Maps → Partager → Intégrer une carte</small>
  </div>
</div>

<button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Enregistrer tous les paramètres</button>
</form>

<?php require_once 'includes/admin_footer.php'; ?>
