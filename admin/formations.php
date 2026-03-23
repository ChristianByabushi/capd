<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/auth.php';
requireRole('superadmin', 'admin', 'editor');
$adminTitle = 'Formations & Centres';

if (isset($_GET['delete'])) {
    if (!can('delete_content')) { header('Location: formations.php'); exit; }
    query("DELETE FROM centres WHERE id=?", [(int)$_GET['delete']]); header('Location: formations.php?saved=1'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $f = [
        trim($_POST['name_fr']??''), trim($_POST['name_en']??''), trim($_POST['name_sw']??''),
        $_POST['description_fr']??'', $_POST['description_en']??'', $_POST['description_sw']??'',
        $_POST['domain']??'education',
        (int)($_POST['display_order']??0), isset($_POST['is_active'])?1:0,
    ];
    $youtube = trim($_POST['youtube_url'] ?? '');
    $cover = uploadFile('cover_image', 'centres');
    if ($id) {
        $sql = $cover ? ",cover_image=?" : "";
        $p = $f;
        $p[] = $youtube;
        if ($cover) $p[] = $cover;
        $p[] = $id;
        query("UPDATE centres SET name_fr=?,name_en=?,name_sw=?,description_fr=?,description_en=?,description_sw=?,domain=?,display_order=?,is_active=?,youtube_url=?$sql WHERE id=?", $p);
    } else {
        $slug = slugify($f[0]?:$f[1]);
        $base=$slug;$i=1; while(fetchOne("SELECT id FROM centres WHERE slug=?",[$slug])){$slug=$base.'-'.$i++;}
        $p = array_merge([$slug], $f, [$youtube]);
        if ($cover) $p[] = $cover;
        query("INSERT INTO centres (slug,name_fr,name_en,name_sw,description_fr,description_en,description_sw,domain,display_order,is_active,youtube_url".($cover?",cover_image":"").") VALUES (?,?,?,?,?,?,?,?,?,?,?".($cover?",?":"").")", $p);
    }
    header('Location: formations.php?saved=1'); exit;
}

$edit = isset($_GET['edit']) ? fetchOne("SELECT * FROM centres WHERE id=?", [(int)$_GET['edit']]) : null;
$centres = fetchAll("SELECT * FROM centres ORDER BY display_order ASC");
$domains = ['genre','entrepreneuriat','education','sante','environnement','paix'];
require_once 'includes/admin_header.php';
?>
<?php if (isset($_GET['saved'])): ?><div class="alert alert-success">Enregistré.</div><?php endif; ?>
<div class="admin-form" style="margin-bottom:2rem">
  <h2 style="margin-bottom:1.5rem;color:#1a6b3c"><?= $edit?'Modifier':'Nouveau centre' ?></h2>
  <form method="POST" enctype="multipart/form-data">
    <?php if ($edit): ?><input type="hidden" name="id" value="<?= $edit['id'] ?>"><?php endif; ?>
    <div class="form-row">
      <div class="form-group"><label>Nom (FR)</label><input type="text" name="name_fr" class="form-control" value="<?= e($edit['name_fr']??'') ?>"></div>
      <div class="form-group"><label>Name (EN)</label><input type="text" name="name_en" class="form-control" value="<?= e($edit['name_en']??'') ?>"></div>
    </div>
    <div class="form-row">
      <div class="form-group"><label>Jina (SW)</label><input type="text" name="name_sw" class="form-control" value="<?= e($edit['name_sw']??'') ?>"></div>
      <div class="form-group"><label>Domaine</label>
        <select name="domain" class="form-control">
          <?php foreach ($domains as $d): ?><option value="<?= $d ?>" <?= ($edit['domain']??'education')===$d?'selected':'' ?>><?= $d ?></option><?php endforeach; ?>
        </select>
      </div>
    </div>
    <div class="form-group"><label>Image</label><input type="file" name="cover_image" class="form-control" accept="image/*"></div>
    <div class="form-group">
      <label><i class="fab fa-youtube" style="color:#FF0000"></i> URL YouTube (optionnel)</label>
      <input type="text" name="youtube_url" class="form-control" value="<?= e($edit['youtube_url'] ?? '') ?>" placeholder="https://www.youtube.com/watch?v=...">
      <small style="color:#888">Vidéo de présentation du centre.</small>
    </div>
    <div class="form-group"><label>Description (FR)</label><textarea name="description_fr" class="form-control" rows="4"><?= e($edit['description_fr']??'') ?></textarea></div>
    <div class="form-group"><label>Description (EN)</label><textarea name="description_en" class="form-control" rows="4"><?= e($edit['description_en']??'') ?></textarea></div>
    <div class="form-group"><label>Maelezo (SW)</label><textarea name="description_sw" class="form-control" rows="4"><?= e($edit['description_sw']??'') ?></textarea></div>
    <div class="form-row">
      <div class="form-group"><label>Ordre</label><input type="number" name="display_order" class="form-control" value="<?= (int)($edit['display_order']??0) ?>"></div>
      <div class="form-group" style="display:flex;align-items:flex-end;padding-bottom:.5rem"><label><input type="checkbox" name="is_active" <?= ($edit['is_active']??1)?'checked':'' ?>> Actif</label></div>
    </div>
    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Enregistrer</button>
    <?php if ($edit): ?><a href="formations.php" class="btn btn-outline" style="margin-left:.5rem">Annuler</a><?php endif; ?>
  </form>
</div>
<div class="admin-table-wrap">
  <div class="admin-table-header"><h2>Centres (<?= count($centres) ?>)</h2></div>
  <table>
    <thead><tr><th>Image</th><th>Nom</th><th>Domaine</th><th>Statut</th><th>Actions</th></tr></thead>
    <tbody>
      <?php foreach ($centres as $c): ?>
      <tr>
        <td><?php if ($c['cover_image']): ?><img src="<?= BASE_URL ?>/uploads/<?= e($c['cover_image']) ?>" class="img-thumb" alt=""><?php endif; ?></td>
        <td><?= e($c['name_fr']?:$c['name_en']) ?></td>
        <td><span class="badge badge-blue"><?= e($c['domain']) ?></span></td>
        <td><span class="badge <?= $c['is_active']?'badge-green':'badge-red' ?>"><?= $c['is_active']?'Actif':'Inactif' ?></span></td>
        <td>
          <a href="?edit=<?= $c['id'] ?>" class="btn btn-sm btn-outline"><i class="fas fa-edit"></i></a>
          <?php if (can('delete_content')): ?>
          <a href="?delete=<?= $c['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer?')"><i class="fas fa-trash"></i></a>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php require_once 'includes/admin_footer.php'; ?>
