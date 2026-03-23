<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/auth.php';
requireLogin();
$adminTitle = 'Blog & Communiqués';

$msg = '';

// Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    query("DELETE FROM posts WHERE id=?", [$id]);
    $msg = 'Article supprimé.';
}

// Toggle publish
if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $p = fetchOne("SELECT is_published FROM posts WHERE id=?", [$id]);
    if ($p) {
        $pub = $p['is_published'] ? 0 : 1;
        $pubDate = $pub ? date('Y-m-d H:i:s') : null;
        query("UPDATE posts SET is_published=?, published_at=? WHERE id=?", [$pub, $pubDate, $id]);
    }
    header('Location: blog.php'); exit;
}

// Save (add/edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id       = (int)($_POST['id'] ?? 0);
    $title_fr = trim($_POST['title_fr'] ?? '');
    $title_en = trim($_POST['title_en'] ?? '');
    $title_sw = trim($_POST['title_sw'] ?? '');
    $slug     = slugify($title_fr ?: $title_en);
    $content_fr = $_POST['content_fr'] ?? '';
    $content_en = $_POST['content_en'] ?? '';
    $content_sw = $_POST['content_sw'] ?? '';
    $excerpt_fr = trim($_POST['excerpt_fr'] ?? '');
    $excerpt_en = trim($_POST['excerpt_en'] ?? '');
    $excerpt_sw = trim($_POST['excerpt_sw'] ?? '');
    $category   = $_POST['category'] ?? 'news';
    $is_pub     = isset($_POST['is_published']) ? 1 : 0;
    $pub_date   = $is_pub ? date('Y-m-d H:i:s') : null;
    $youtube    = trim($_POST['youtube_url'] ?? '');

    $cover = uploadFile('cover_image', 'posts');

    if ($id) {
        $coverSql = $cover ? ", cover_image=?" : "";
        $params = [$title_fr,$title_en,$title_sw,$content_fr,$content_en,$content_sw,$excerpt_fr,$excerpt_en,$excerpt_sw,$category,$is_pub,$youtube];
        if ($cover) $params[] = $cover;
        $params[] = $id;
        query("UPDATE posts SET title_fr=?,title_en=?,title_sw=?,content_fr=?,content_en=?,content_sw=?,excerpt_fr=?,excerpt_en=?,excerpt_sw=?,category=?,is_published=?,youtube_url=?$coverSql WHERE id=?", $params);
    } else {
        $base = $slug; $i = 1;
        while (fetchOne("SELECT id FROM posts WHERE slug=?", [$slug])) { $slug = $base . '-' . $i++; }
        $params = [$slug,$title_fr,$title_en,$title_sw,$content_fr,$content_en,$content_sw,$excerpt_fr,$excerpt_en,$excerpt_sw,$category,$is_pub,$youtube];
        if ($cover) $params[] = $cover;
        query("INSERT INTO posts (slug,title_fr,title_en,title_sw,content_fr,content_en,content_sw,excerpt_fr,excerpt_en,excerpt_sw,category,is_published,youtube_url" . ($cover?",cover_image":"") . ") VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?" . ($cover?",?":"") . ")", $params);
        $msg = 'Article créé.';
    }
    header('Location: blog.php?saved=1'); exit;
}

$edit = isset($_GET['edit']) ? fetchOne("SELECT * FROM posts WHERE id=?", [(int)$_GET['edit']]) : null;
$posts = fetchAll("SELECT * FROM posts ORDER BY created_at DESC");
require_once 'includes/admin_header.php';
?>

<?php if ($msg || isset($_GET['saved'])): ?>
<div class="alert alert-success"><?= e($msg ?: 'Enregistré.') ?></div>
<?php endif; ?>

<!-- Form -->
<div class="admin-form" style="margin-bottom:2rem">
  <h2 style="margin-bottom:1.5rem;color:#1a6b3c"><?= $edit ? 'Modifier l\'article' : 'Nouvel article' ?></h2>
  <form method="POST" enctype="multipart/form-data">
    <?php if ($edit): ?><input type="hidden" name="id" value="<?= $edit['id'] ?>"><?php endif; ?>
    <div class="form-row">
      <div class="form-group"><label>Titre (FR)</label><input type="text" name="title_fr" class="form-control" value="<?= e($edit['title_fr'] ?? '') ?>"></div>
      <div class="form-group"><label>Title (EN)</label><input type="text" name="title_en" class="form-control" value="<?= e($edit['title_en'] ?? '') ?>"></div>
    </div>
    <div class="form-group"><label>Kichwa (SW)</label><input type="text" name="title_sw" class="form-control" value="<?= e($edit['title_sw'] ?? '') ?>"></div>
    <div class="form-row">
      <div class="form-group"><label>Catégorie</label>
        <select name="category" class="form-control">
          <?php foreach (['news','communique','report'] as $c): ?>
          <option value="<?= $c ?>" <?= ($edit['category']??'news')===$c?'selected':'' ?>><?= $c ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group"><label>Image de couverture</label><input type="file" name="cover_image" class="form-control" accept="image/*"></div>
    </div>
    <div class="form-group">
      <label><i class="fab fa-youtube" style="color:#FF0000"></i> URL YouTube (optionnel)</label>
      <input type="text" name="youtube_url" class="form-control" value="<?= e($edit['youtube_url'] ?? '') ?>" placeholder="https://www.youtube.com/watch?v=...">
      <small style="color:#888">Collez un lien YouTube — il sera intégré dans l'article.</small>
    </div>
    <div class="form-group"><label>Extrait (FR)</label><textarea name="excerpt_fr" class="form-control" rows="2"><?= e($edit['excerpt_fr'] ?? '') ?></textarea></div>
    <div class="form-group"><label>Contenu (FR)</label><textarea name="content_fr" class="form-control" rows="6"><?= e($edit['content_fr'] ?? '') ?></textarea></div>
    <div class="form-group"><label>Content (EN)</label><textarea name="content_en" class="form-control" rows="6"><?= e($edit['content_en'] ?? '') ?></textarea></div>
    <div class="form-group"><label>Maudhui (SW)</label><textarea name="content_sw" class="form-control" rows="6"><?= e($edit['content_sw'] ?? '') ?></textarea></div>
    <div class="form-group">
      <label><input type="checkbox" name="is_published" <?= ($edit['is_published']??0)?'checked':'' ?>> Publier immédiatement</label>
    </div>
    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Enregistrer</button>
    <?php if ($edit): ?><a href="blog.php" class="btn btn-outline" style="margin-left:.5rem">Annuler</a><?php endif; ?>
  </form>
</div>

<!-- List -->
<div class="admin-table-wrap">
  <div class="admin-table-header"><h2>Articles (<?= count($posts) ?>)</h2></div>
  <table>
    <thead><tr><th>Image</th><th>Titre</th><th>Catégorie</th><th>Statut</th><th>Date</th><th>Actions</th></tr></thead>
    <tbody>
      <?php foreach ($posts as $p): ?>
      <tr>
        <td><?php if ($p['cover_image']): ?><img src="<?= BASE_URL ?>/uploads/<?= e($p['cover_image']) ?>" class="img-thumb" alt=""><?php endif; ?></td>
        <td><?= e(truncate($p['title_fr'] ?: $p['title_en'], 50)) ?></td>
        <td><span class="badge badge-blue"><?= e($p['category']) ?></span></td>
        <td><span class="badge <?= $p['is_published']?'badge-green':'badge-orange' ?>"><?= $p['is_published']?'Publié':'Brouillon' ?></span></td>
        <td style="font-size:.8rem;color:#888"><?= date('d/m/Y', strtotime($p['created_at'])) ?></td>
        <td>
          <a href="?edit=<?= $p['id'] ?>" class="btn btn-sm btn-outline"><i class="fas fa-edit"></i></a>
          <a href="?toggle=<?= $p['id'] ?>" class="btn btn-sm btn-accent" title="Basculer publication"><i class="fas fa-eye<?= $p['is_published']?'-slash':'' ?>"></i></a>
          <a href="?delete=<?= $p['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer?')"><i class="fas fa-trash"></i></a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php require_once 'includes/admin_footer.php'; ?>
