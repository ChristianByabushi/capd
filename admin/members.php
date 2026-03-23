<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/auth.php';
requireRole('superadmin', 'admin');
$adminTitle = 'Membres';
$msg = '';

if (isset($_GET['delete'])) {
    query("DELETE FROM members WHERE id=?", [(int)$_GET['delete']]);
    header('Location: members.php?saved=1'); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $f = [
        'full_name'   => trim($_POST['full_name'] ?? ''),
        'position_fr' => trim($_POST['position_fr'] ?? ''),
        'position_en' => trim($_POST['position_en'] ?? ''),
        'position_sw' => trim($_POST['position_sw'] ?? ''),
        'bio_fr'      => $_POST['bio_fr'] ?? '',
        'bio_en'      => $_POST['bio_en'] ?? '',
        'bio_sw'      => $_POST['bio_sw'] ?? '',
        'organ'       => $_POST['organ'] ?? 'membre',
        'display_order' => (int)($_POST['display_order'] ?? 0),
        'is_active'   => isset($_POST['is_active']) ? 1 : 0,
    ];
    $photo = uploadFile('photo', 'members');

    if ($id) {
        $photoSql = $photo ? ",photo=?" : "";
        $p = array_values($f);
        if ($photo) $p[] = $photo;
        $p[] = $id;
        query("UPDATE members SET full_name=?,position_fr=?,position_en=?,position_sw=?,bio_fr=?,bio_en=?,bio_sw=?,organ=?,display_order=?,is_active=?$photoSql WHERE id=?", $p);
    } else {
        $p = array_values($f);
        if ($photo) $p[] = $photo;
        query("INSERT INTO members (full_name,position_fr,position_en,position_sw,bio_fr,bio_en,bio_sw,organ,display_order,is_active" . ($photo?",photo":"") . ") VALUES (?,?,?,?,?,?,?,?,?,?" . ($photo?",?":"") . ")", $p);
    }
    header('Location: members.php?saved=1'); exit;
}

$edit = isset($_GET['edit']) ? fetchOne("SELECT * FROM members WHERE id=?", [(int)$_GET['edit']]) : null;
$members = fetchAll("SELECT * FROM members ORDER BY display_order ASC, organ ASC");
$organs = ['conseil_administration'=>'Conseil d\'Administration','comite_gestion'=>'Comité de Gestion','comite_controle'=>'Comité de Contrôle','secretariat_executif'=>'Secrétariat Exécutif','membre'=>'Membre'];
require_once 'includes/admin_header.php';
?>

<?php if (isset($_GET['saved'])): ?><div class="alert alert-success">Enregistré.</div><?php endif; ?>

<div class="admin-form" style="margin-bottom:2rem">
  <h2 style="margin-bottom:1.5rem;color:#1a6b3c"><?= $edit ? 'Modifier le membre' : 'Nouveau membre' ?></h2>
  <form method="POST" enctype="multipart/form-data">
    <?php if ($edit): ?><input type="hidden" name="id" value="<?= $edit['id'] ?>"><?php endif; ?>
    <div class="form-row">
      <div class="form-group"><label>Nom complet *</label><input type="text" name="full_name" class="form-control" required value="<?= e($edit['full_name']??'') ?>"></div>
      <div class="form-group"><label>Organe</label>
        <select name="organ" class="form-control">
          <?php foreach ($organs as $k=>$v): ?>
          <option value="<?= $k ?>" <?= ($edit['organ']??'membre')===$k?'selected':'' ?>><?= $v ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    <div class="form-row">
      <div class="form-group"><label>Position (FR)</label><input type="text" name="position_fr" class="form-control" value="<?= e($edit['position_fr']??'') ?>"></div>
      <div class="form-group"><label>Position (EN)</label><input type="text" name="position_en" class="form-control" value="<?= e($edit['position_en']??'') ?>"></div>
    </div>
    <div class="form-group"><label>Nafasi (SW)</label><input type="text" name="position_sw" class="form-control" value="<?= e($edit['position_sw']??'') ?>"></div>
    <div class="form-group"><label>Photo</label><input type="file" name="photo" class="form-control" accept="image/*"></div>
    <div class="form-group"><label>Bio (FR)</label><textarea name="bio_fr" class="form-control" rows="3"><?= e($edit['bio_fr']??'') ?></textarea></div>
    <div class="form-group"><label>Bio (EN)</label><textarea name="bio_en" class="form-control" rows="3"><?= e($edit['bio_en']??'') ?></textarea></div>
    <div class="form-group"><label>Wasifu (SW)</label><textarea name="bio_sw" class="form-control" rows="3"><?= e($edit['bio_sw']??'') ?></textarea></div>
    <div class="form-row">
      <div class="form-group"><label>Ordre d'affichage</label><input type="number" name="display_order" class="form-control" value="<?= (int)($edit['display_order']??0) ?>"></div>
      <div class="form-group" style="display:flex;align-items:flex-end;padding-bottom:.5rem"><label><input type="checkbox" name="is_active" <?= ($edit['is_active']??1)?'checked':'' ?>> Actif</label></div>
    </div>
    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Enregistrer</button>
    <?php if ($edit): ?><a href="members.php" class="btn btn-outline" style="margin-left:.5rem">Annuler</a><?php endif; ?>
  </form>
</div>

<div class="admin-table-wrap">
  <div class="admin-table-header"><h2>Membres (<?= count($members) ?>)</h2></div>
  <table>
    <thead><tr><th>Photo</th><th>Nom</th><th>Position</th><th>Organe</th><th>Statut</th><th>Actions</th></tr></thead>
    <tbody>
      <?php foreach ($members as $m): ?>
      <tr>
        <td><?php if ($m['photo']): ?><img src="<?= BASE_URL ?>/uploads/<?= e($m['photo']) ?>" class="img-thumb" alt=""><?php else: ?><div style="width:50px;height:50px;background:#eee;border-radius:6px;display:flex;align-items:center;justify-content:center"><i class="fas fa-user" style="color:#aaa"></i></div><?php endif; ?></td>
        <td><?= e($m['full_name']) ?></td>
        <td><?= e($m['position_fr']) ?></td>
        <td><?= e($organs[$m['organ']] ?? $m['organ']) ?></td>
        <td><span class="badge <?= $m['is_active']?'badge-green':'badge-red' ?>"><?= $m['is_active']?'Actif':'Inactif' ?></span></td>
        <td>
          <a href="?edit=<?= $m['id'] ?>" class="btn btn-sm btn-outline"><i class="fas fa-edit"></i></a>
          <a href="?delete=<?= $m['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer?')"><i class="fas fa-trash"></i></a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php require_once 'includes/admin_footer.php'; ?>
