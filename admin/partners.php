<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/auth.php';
requireLogin();
$adminTitle = 'Partenaires';

if (isset($_GET['delete'])) { query("DELETE FROM partners WHERE id=?", [(int)$_GET['delete']]); header('Location: partners.php?saved=1'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $website = trim($_POST['website'] ?? '');
    $order = (int)($_POST['display_order'] ?? 0);
    $active = isset($_POST['is_active']) ? 1 : 0;
    $logo = uploadFile('logo', 'partners');
    if ($id) {
        $logoSql = $logo ? ",logo=?" : "";
        $p = [$name, $website, $order, $active];
        if ($logo) $p[] = $logo;
        $p[] = $id;
        query("UPDATE partners SET name=?,website=?,display_order=?,is_active=?$logoSql WHERE id=?", $p);
    } else {
        $p = [$name, $website, $order, $active];
        if ($logo) $p[] = $logo;
        query("INSERT INTO partners (name,website,display_order,is_active".($logo?",logo":"").") VALUES (?,?,?,?".($logo?",?":"").")", $p);
    }
    header('Location: partners.php?saved=1'); exit;
}

$edit = isset($_GET['edit']) ? fetchOne("SELECT * FROM partners WHERE id=?", [(int)$_GET['edit']]) : null;
$partners = fetchAll("SELECT * FROM partners ORDER BY display_order ASC");
require_once 'includes/admin_header.php';
?>
<?php if (isset($_GET['saved'])): ?><div class="alert alert-success">Enregistré.</div><?php endif; ?>
<div class="admin-form" style="margin-bottom:2rem">
  <h2 style="margin-bottom:1.5rem;color:#1a6b3c"><?= $edit?'Modifier':'Nouveau partenaire' ?></h2>
  <form method="POST" enctype="multipart/form-data">
    <?php if ($edit): ?><input type="hidden" name="id" value="<?= $edit['id'] ?>"><?php endif; ?>
    <div class="form-row">
      <div class="form-group"><label>Nom *</label><input type="text" name="name" class="form-control" required value="<?= e($edit['name']??'') ?>"></div>
      <div class="form-group"><label>Site web</label><input type="url" name="website" class="form-control" value="<?= e($edit['website']??'') ?>"></div>
    </div>
    <div class="form-row">
      <div class="form-group"><label>Logo</label><input type="file" name="logo" class="form-control" accept="image/*"></div>
      <div class="form-group"><label>Ordre</label><input type="number" name="display_order" class="form-control" value="<?= (int)($edit['display_order']??0) ?>"></div>
    </div>
    <div class="form-group"><label><input type="checkbox" name="is_active" <?= ($edit['is_active']??1)?'checked':'' ?>> Actif</label></div>
    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Enregistrer</button>
    <?php if ($edit): ?><a href="partners.php" class="btn btn-outline" style="margin-left:.5rem">Annuler</a><?php endif; ?>
  </form>
</div>
<div class="admin-table-wrap">
  <div class="admin-table-header"><h2>Partenaires (<?= count($partners) ?>)</h2></div>
  <table>
    <thead><tr><th>Logo</th><th>Nom</th><th>Site</th><th>Statut</th><th>Actions</th></tr></thead>
    <tbody>
      <?php foreach ($partners as $p): ?>
      <tr>
        <td><?php if ($p['logo']): ?><img src="<?= BASE_URL ?>/uploads/<?= e($p['logo']) ?>" class="img-thumb" alt=""><?php endif; ?></td>
        <td><?= e($p['name']) ?></td>
        <td><?php if ($p['website']): ?><a href="<?= e($p['website']) ?>" target="_blank"><?= e($p['website']) ?></a><?php endif; ?></td>
        <td><span class="badge <?= $p['is_active']?'badge-green':'badge-red' ?>"><?= $p['is_active']?'Actif':'Inactif' ?></span></td>
        <td>
          <a href="?edit=<?= $p['id'] ?>" class="btn btn-sm btn-outline"><i class="fas fa-edit"></i></a>
          <a href="?delete=<?= $p['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer?')"><i class="fas fa-trash"></i></a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php require_once 'includes/admin_footer.php'; ?>
