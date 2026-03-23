<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/auth.php';
requireLogin();
$adminTitle = 'Slides Hero';

if (isset($_GET['delete'])) { query("DELETE FROM hero_slides WHERE id=?", [(int)$_GET['delete']]); header('Location: hero.php'); exit; }
if (isset($_GET['toggle'])) {
    $s = fetchOne("SELECT is_active FROM hero_slides WHERE id=?", [(int)$_GET['toggle']]);
    if ($s) query("UPDATE hero_slides SET is_active=? WHERE id=?", [$s['is_active']?0:1, (int)$_GET['toggle']]);
    header('Location: hero.php'); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $f = [
        trim($_POST['title_fr']??''), trim($_POST['title_en']??''), trim($_POST['title_sw']??''),
        trim($_POST['subtitle_fr']??''), trim($_POST['subtitle_en']??''), trim($_POST['subtitle_sw']??''),
        trim($_POST['btn1_label_fr']??''), trim($_POST['btn1_label_en']??''), trim($_POST['btn1_label_sw']??''),
        trim($_POST['btn1_url']??''),
        trim($_POST['btn2_label_fr']??''), trim($_POST['btn2_label_en']??''), trim($_POST['btn2_label_sw']??''),
        trim($_POST['btn2_url']??''),
        (int)($_POST['display_order']??0), isset($_POST['is_active'])?1:0,
    ];
    $img = uploadFile('image', 'hero');
    if ($id) {
        $imgSql = $img ? ",image=?" : "";
        $p = $f;
        if ($img) $p[] = $img;
        $p[] = $id;
        query("UPDATE hero_slides SET title_fr=?,title_en=?,title_sw=?,subtitle_fr=?,subtitle_en=?,subtitle_sw=?,btn1_label_fr=?,btn1_label_en=?,btn1_label_sw=?,btn1_url=?,btn2_label_fr=?,btn2_label_en=?,btn2_label_sw=?,btn2_url=?,display_order=?,is_active=?$imgSql WHERE id=?", $p);
    } else {
        $p = $f;
        if ($img) $p[] = $img;
        query("INSERT INTO hero_slides (title_fr,title_en,title_sw,subtitle_fr,subtitle_en,subtitle_sw,btn1_label_fr,btn1_label_en,btn1_label_sw,btn1_url,btn2_label_fr,btn2_label_en,btn2_label_sw,btn2_url,display_order,is_active".($img?",image":"").") VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?".($img?",?":"").")", $p);
    }
    header('Location: hero.php?saved=1'); exit;
}

$edit = isset($_GET['edit']) ? fetchOne("SELECT * FROM hero_slides WHERE id=?", [(int)$_GET['edit']]) : null;
$slides = fetchAll("SELECT * FROM hero_slides ORDER BY display_order ASC");
require_once 'includes/admin_header.php';
?>
<?php if (isset($_GET['saved'])): ?><div class="alert alert-success">Enregistré.</div><?php endif; ?>
<div class="admin-form" style="margin-bottom:2rem">
  <h2 style="margin-bottom:1.5rem;color:#1a6b3c"><?= $edit?'Modifier le slide':'Nouveau slide' ?></h2>
  <form method="POST" enctype="multipart/form-data">
    <?php if ($edit): ?><input type="hidden" name="id" value="<?= $edit['id'] ?>"><?php endif; ?>
    <div class="form-group"><label>Image de fond</label><input type="file" name="image" class="form-control" accept="image/*"></div>
    <div class="form-row">
      <div class="form-group"><label>Titre (FR)</label><input type="text" name="title_fr" class="form-control" value="<?= e($edit['title_fr']??'') ?>"></div>
      <div class="form-group"><label>Title (EN)</label><input type="text" name="title_en" class="form-control" value="<?= e($edit['title_en']??'') ?>"></div>
    </div>
    <div class="form-group"><label>Kichwa (SW)</label><input type="text" name="title_sw" class="form-control" value="<?= e($edit['title_sw']??'') ?>"></div>
    <div class="form-group"><label>Sous-titre (FR)</label><textarea name="subtitle_fr" class="form-control" rows="2"><?= e($edit['subtitle_fr']??'') ?></textarea></div>
    <div class="form-group"><label>Subtitle (EN)</label><textarea name="subtitle_en" class="form-control" rows="2"><?= e($edit['subtitle_en']??'') ?></textarea></div>
    <div class="form-group"><label>Maelezo (SW)</label><textarea name="subtitle_sw" class="form-control" rows="2"><?= e($edit['subtitle_sw']??'') ?></textarea></div>
    <div class="form-row">
      <div class="form-group"><label>Bouton 1 label (FR)</label><input type="text" name="btn1_label_fr" class="form-control" value="<?= e($edit['btn1_label_fr']??'') ?>"></div>
      <div class="form-group"><label>Bouton 1 URL</label><input type="text" name="btn1_url" class="form-control" value="<?= e($edit['btn1_url']??'') ?>"></div>
    </div>
    <div class="form-row">
      <div class="form-group"><label>Bouton 2 label (FR)</label><input type="text" name="btn2_label_fr" class="form-control" value="<?= e($edit['btn2_label_fr']??'') ?>"></div>
      <div class="form-group"><label>Bouton 2 URL</label><input type="text" name="btn2_url" class="form-control" value="<?= e($edit['btn2_url']??'') ?>"></div>
    </div>
    <div class="form-row">
      <div class="form-group"><label>Ordre</label><input type="number" name="display_order" class="form-control" value="<?= (int)($edit['display_order']??0) ?>"></div>
      <div class="form-group" style="display:flex;align-items:flex-end;padding-bottom:.5rem"><label><input type="checkbox" name="is_active" <?= ($edit['is_active']??1)?'checked':'' ?>> Actif</label></div>
    </div>
    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Enregistrer</button>
    <?php if ($edit): ?><a href="hero.php" class="btn btn-outline" style="margin-left:.5rem">Annuler</a><?php endif; ?>
  </form>
</div>
<div class="admin-table-wrap">
  <div class="admin-table-header"><h2>Slides (<?= count($slides) ?>)</h2></div>
  <table>
    <thead><tr><th>Image</th><th>Titre</th><th>Ordre</th><th>Statut</th><th>Actions</th></tr></thead>
    <tbody>
      <?php foreach ($slides as $s): ?>
      <tr>
        <td><?php if ($s['image']): ?><img src="<?= BASE_URL ?>/uploads/<?= e($s['image']) ?>" class="img-thumb" alt=""><?php endif; ?></td>
        <td><?= e($s['title_fr']?:$s['title_en']) ?></td>
        <td><?= $s['display_order'] ?></td>
        <td><span class="badge <?= $s['is_active']?'badge-green':'badge-red' ?>"><?= $s['is_active']?'Actif':'Inactif' ?></span></td>
        <td>
          <a href="?edit=<?= $s['id'] ?>" class="btn btn-sm btn-outline"><i class="fas fa-edit"></i></a>
          <a href="?toggle=<?= $s['id'] ?>" class="btn btn-sm btn-accent"><i class="fas fa-eye<?= $s['is_active']?'-slash':'' ?>"></i></a>
          <a href="?delete=<?= $s['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer?')"><i class="fas fa-trash"></i></a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php require_once 'includes/admin_footer.php'; ?>
