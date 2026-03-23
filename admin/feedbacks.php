<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/auth.php';
requireLogin();
$adminTitle = 'Témoignages';

if (isset($_GET['delete'])) { query("DELETE FROM feedbacks WHERE id=?", [(int)$_GET['delete']]); header('Location: feedbacks.php'); exit; }
if (isset($_GET['approve'])) { query("UPDATE feedbacks SET is_approved=1 WHERE id=?", [(int)$_GET['approve']]); header('Location: feedbacks.php'); exit; }

$feedbacks = fetchAll("SELECT f.*, c.name_fr FROM feedbacks f LEFT JOIN centres c ON f.centre_id=c.id ORDER BY f.created_at DESC");
require_once 'includes/admin_header.php';
?>
<div class="admin-table-wrap">
  <div class="admin-table-header"><h2>Témoignages (<?= count($feedbacks) ?>)</h2></div>
  <table>
    <thead><tr><th>Apprenant</th><th>Centre</th><th>Témoignage</th><th>Note</th><th>Statut</th><th>Actions</th></tr></thead>
    <tbody>
      <?php foreach ($feedbacks as $f): ?>
      <tr>
        <td><?= e($f['learner_name']) ?></td>
        <td><?= e($f['name_fr'] ?? '—') ?></td>
        <td><?= e(truncate($f['feedback_text'], 80)) ?></td>
        <td><?php for($i=0;$i<5;$i++) echo '<i class="fas fa-star" style="color:'.($i<$f['rating']?'#f4a61d':'#ddd').'"></i>'; ?></td>
        <td><span class="badge <?= $f['is_approved']?'badge-green':'badge-orange' ?>"><?= $f['is_approved']?'Approuvé':'En attente' ?></span></td>
        <td>
          <?php if (!$f['is_approved']): ?><a href="?approve=<?= $f['id'] ?>" class="btn btn-sm btn-accent"><i class="fas fa-check"></i></a><?php endif; ?>
          <a href="?delete=<?= $f['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer?')"><i class="fas fa-trash"></i></a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php require_once 'includes/admin_footer.php'; ?>
