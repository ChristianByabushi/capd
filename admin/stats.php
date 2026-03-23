<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/auth.php';
requireRole('superadmin', 'admin');
$adminTitle = 'Statistiques homepage';

if (isset($_GET['delete'])) { query("DELETE FROM stats WHERE id=?", [(int)$_GET['delete']]); header('Location: stats.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $f = [trim($_POST['label_fr']??''), trim($_POST['label_en']??''), trim($_POST['label_sw']??''), trim($_POST['value']??''), trim($_POST['icon']??''), (int)($_POST['display_order']??0)];
    if ($id) {
        query("UPDATE stats SET label_fr=?,label_en=?,label_sw=?,value=?,icon=?,display_order=? WHERE id=?", array_merge($f, [$id]));
    } else {
        query("INSERT INTO stats (label_fr,label_en,label_sw,value,icon,display_order) VALUES (?,?,?,?,?,?)", $f);
    }
    header('Location: stats.php?saved=1'); exit;
}

$edit = isset($_GET['edit']) ? fetchOne("SELECT * FROM stats WHERE id=?", [(int)$_GET['edit']]) : null;
$stats = fetchAll("SELECT * FROM stats ORDER BY display_order ASC");
require_once 'includes/admin_header.php';
?>
<?php if (isset($_GET['saved'])): ?><div class="alert alert-success">Enregistré.</div><?php endif; ?>
<div class="admin-form" style="margin-bottom:2rem">
  <h2 style="margin-bottom:1.5rem;color:#1a6b3c"><?= $edit?'Modifier':'Nouvelle statistique' ?></h2>
  <form method="POST">
    <?php if ($edit): ?><input type="hidden" name="id" value="<?= $edit['id'] ?>"><?php endif; ?>
    <div class="form-row">
      <div class="form-group"><label>Libellé (FR)</label><input type="text" name="label_fr" class="form-control" value="<?= e($edit['label_fr']??'') ?>"></div>
      <div class="form-group"><label>Label (EN)</label><input type="text" name="label_en" class="form-control" value="<?= e($edit['label_en']??'') ?>"></div>
    </div>
    <div class="form-row">
      <div class="form-group"><label>Lebo (SW)</label><input type="text" name="label_sw" class="form-control" value="<?= e($edit['label_sw']??'') ?>"></div>
      <div class="form-group"><label>Valeur (ex: 46, 5000+)</label><input type="text" name="value" class="form-control" value="<?= e($edit['value']??'') ?>"></div>
    </div>
    <div class="form-row">
      <div class="form-group"><label>Icône Font Awesome (ex: fa-users)</label><input type="text" name="icon" class="form-control" value="<?= e($edit['icon']??'') ?>"></div>
      <div class="form-group"><label>Ordre</label><input type="number" name="display_order" class="form-control" value="<?= (int)($edit['display_order']??0) ?>"></div>
    </div>
    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Enregistrer</button>
    <?php if ($edit): ?><a href="stats.php" class="btn btn-outline" style="margin-left:.5rem">Annuler</a><?php endif; ?>
  </form>
</div>
<div class="admin-table-wrap">
  <div class="admin-table-header"><h2>Statistiques</h2></div>
  <table>
    <thead><tr><th>Icône</th><th>Libellé (FR)</th><th>Valeur</th><th>Ordre</th><th>Actions</th></tr></thead>
    <tbody>
      <?php foreach ($stats as $s): ?>
      <tr>
        <td><i class="fas <?= e($s['icon']) ?>" style="font-size:1.3rem;color:#1a6b3c"></i></td>
        <td><?= e($s['label_fr']) ?></td>
        <td><strong><?= e($s['value']) ?></strong></td>
        <td><?= $s['display_order'] ?></td>
        <td>
          <a href="?edit=<?= $s['id'] ?>" class="btn btn-sm btn-outline"><i class="fas fa-edit"></i></a>
          <a href="?delete=<?= $s['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer?')"><i class="fas fa-trash"></i></a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php require_once 'includes/admin_footer.php'; ?>
