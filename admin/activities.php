<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/auth.php';
requireLogin();
$adminTitle = 'Activités & Projets';
$msg = '';

if (isset($_GET['delete'])) {
    query("DELETE FROM activities WHERE id=?", [(int)$_GET['delete']]);
    header('Location: activities.php?saved=1'); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $f = [
        'title_fr'       => trim($_POST['title_fr'] ?? ''),
        'title_en'       => trim($_POST['title_en'] ?? ''),
        'title_sw'       => trim($_POST['title_sw'] ?? ''),
        'description_fr' => $_POST['description_fr'] ?? '',
        'description_en' => $_POST['description_en'] ?? '',
        'description_sw' => $_POST['description_sw'] ?? '',
        'objectives_fr'  => $_POST['objectives_fr'] ?? '',
        'objectives_en'  => $_POST['objectives_en'] ?? '',
        'objectives_sw'  => $_POST['objectives_sw'] ?? '',
        'department'     => trim($_POST['department'] ?? ''),
        'date_start'     => $_POST['date_start'] ?: null,
        'date_end'       => $_POST['date_end'] ?: null,
        'status'         => $_POST['status'] ?? 'ongoing',
        'is_featured'    => isset($_POST['is_featured']) ? 1 : 0,
    ];
    $youtube = trim($_POST['youtube_url'] ?? '');
    $cover = uploadFile('cover_image', 'activities');

    if ($id) {
        $coverSql = $cover ? ",cover_image=?" : "";
        $p = array_values($f);
        $p[] = $youtube;
        if ($cover) $p[] = $cover;
        $p[] = $id;
        query("UPDATE activities SET title_fr=?,title_en=?,title_sw=?,description_fr=?,description_en=?,description_sw=?,objectives_fr=?,objectives_en=?,objectives_sw=?,department=?,date_start=?,date_end=?,status=?,is_featured=?,youtube_url=?$coverSql WHERE id=?", $p);
    } else {
        $slug = slugify($f['title_fr'] ?: $f['title_en']);
        $base = $slug; $i = 1;
        while (fetchOne("SELECT id FROM activities WHERE slug=?", [$slug])) { $slug = $base.'-'.$i++; }
        $p2 = array_merge([$slug], array_values($f), [$youtube]);
        if ($cover) $p2[] = $cover;
        query("INSERT INTO activities (slug,title_fr,title_en,title_sw,description_fr,description_en,description_sw,objectives_fr,objectives_en,objectives_sw,department,date_start,date_end,status,is_featured,youtube_url" . ($cover?",cover_image":"") . ") VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?" . ($cover?",?":"") . ")", $p2);
    }
    header('Location: activities.php?saved=1'); exit;
}

$edit = isset($_GET['edit']) ? fetchOne("SELECT * FROM activities WHERE id=?", [(int)$_GET['edit']]) : null;
$activities = fetchAll("SELECT * FROM activities ORDER BY created_at DESC");
require_once 'includes/admin_header.php';
?>

<?php if (isset($_GET['saved'])): ?><div class="alert alert-success">Enregistré avec succès.</div><?php endif; ?>

<div class="admin-form" style="margin-bottom:2rem">
  <h2 style="margin-bottom:1.5rem;color:#1a6b3c"><?= $edit ? 'Modifier l\'activité' : 'Nouvelle activité' ?></h2>
  <form method="POST" enctype="multipart/form-data">
    <?php if ($edit): ?><input type="hidden" name="id" value="<?= $edit['id'] ?>"><?php endif; ?>
    <div class="form-row">
      <div class="form-group"><label>Titre (FR)</label><input type="text" name="title_fr" class="form-control" value="<?= e($edit['title_fr']??'') ?>"></div>
      <div class="form-group"><label>Title (EN)</label><input type="text" name="title_en" class="form-control" value="<?= e($edit['title_en']??'') ?>"></div>
    </div>
    <div class="form-group"><label>Kichwa (SW)</label><input type="text" name="title_sw" class="form-control" value="<?= e($edit['title_sw']??'') ?>"></div>
    <div class="form-row">
      <div class="form-group"><label>Département</label><input type="text" name="department" class="form-control" value="<?= e($edit['department']??'') ?>"></div>
      <div class="form-group"><label>Statut</label>
        <select name="status" class="form-control">
          <?php foreach (['ongoing','completed','planned'] as $s): ?>
          <option value="<?= $s ?>" <?= ($edit['status']??'ongoing')===$s?'selected':'' ?>><?= $s ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    <div class="form-row">
      <div class="form-group"><label>Date début</label><input type="date" name="date_start" class="form-control" value="<?= e($edit['date_start']??'') ?>"></div>
      <div class="form-group"><label>Date fin</label><input type="date" name="date_end" class="form-control" value="<?= e($edit['date_end']??'') ?>"></div>
    </div>
    <div class="form-group"><label>Image de couverture</label><input type="file" name="cover_image" class="form-control" accept="image/*"></div>
    <div class="form-group">
      <label><i class="fab fa-youtube" style="color:#FF0000"></i> URL YouTube (optionnel)</label>
      <input type="text" name="youtube_url" class="form-control" value="<?= e($edit['youtube_url'] ?? '') ?>" placeholder="https://www.youtube.com/watch?v=...">
      <small style="color:#888">Vidéo de présentation de l'activité.</small>
    </div>
    <div class="form-group"><label>Description (FR)</label><textarea name="description_fr" class="form-control" rows="4"><?= e($edit['description_fr']??'') ?></textarea></div>
    <div class="form-group"><label>Description (EN)</label><textarea name="description_en" class="form-control" rows="4"><?= e($edit['description_en']??'') ?></textarea></div>
    <div class="form-group"><label>Maelezo (SW)</label><textarea name="description_sw" class="form-control" rows="4"><?= e($edit['description_sw']??'') ?></textarea></div>
    <div class="form-group"><label>Objectifs (FR)</label><textarea name="objectives_fr" class="form-control" rows="3"><?= e($edit['objectives_fr']??'') ?></textarea></div>
    <div class="form-group"><label>Objectives (EN)</label><textarea name="objectives_en" class="form-control" rows="3"><?= e($edit['objectives_en']??'') ?></textarea></div>
    <div class="form-group"><label>Malengo (SW)</label><textarea name="objectives_sw" class="form-control" rows="3"><?= e($edit['objectives_sw']??'') ?></textarea></div>
    <div class="form-group"><label><input type="checkbox" name="is_featured" <?= ($edit['is_featured']??0)?'checked':'' ?>> Mettre en avant (homepage)</label></div>
    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Enregistrer</button>
    <?php if ($edit): ?><a href="activities.php" class="btn btn-outline" style="margin-left:.5rem">Annuler</a><?php endif; ?>
  </form>
</div>

<div class="admin-table-wrap">
  <div class="admin-table-header"><h2>Activités (<?= count($activities) ?>)</h2></div>
  <table>
    <thead><tr><th>Image</th><th>Titre</th><th>Département</th><th>Statut</th><th>Vedette</th><th>Actions</th></tr></thead>
    <tbody>
      <?php foreach ($activities as $a): ?>
      <tr>
        <td><?php if ($a['cover_image']): ?><img src="<?= BASE_URL ?>/uploads/<?= e($a['cover_image']) ?>" class="img-thumb" alt=""><?php endif; ?></td>
        <td><?= e(truncate($a['title_fr']?:$a['title_en'],50)) ?></td>
        <td><?= e($a['department']) ?></td>
        <td><span class="badge <?= $a['status']==='ongoing'?'badge-green':($a['status']==='planned'?'badge-orange':'badge-blue') ?>"><?= e($a['status']) ?></span></td>
        <td><?= $a['is_featured']?'<span class="badge badge-green">Oui</span>':'—' ?></td>
        <td>
          <a href="?edit=<?= $a['id'] ?>" class="btn btn-sm btn-outline"><i class="fas fa-edit"></i></a>
          <a href="?delete=<?= $a['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer?')"><i class="fas fa-trash"></i></a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php require_once 'includes/admin_footer.php'; ?>
