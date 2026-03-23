<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/auth.php';
requireLogin();
$adminTitle = 'Messages de contact';

if (isset($_GET['delete'])) {
    query("DELETE FROM contact_messages WHERE id=?", [(int)$_GET['delete']]);
    header('Location: messages.php'); exit;
}
if (isset($_GET['read'])) {
    query("UPDATE contact_messages SET is_read=1 WHERE id=?", [(int)$_GET['read']]);
}

$messages = fetchAll("SELECT * FROM contact_messages ORDER BY created_at DESC");
require_once 'includes/admin_header.php';
?>

<div class="admin-table-wrap">
  <div class="admin-table-header"><h2>Messages (<?= count($messages) ?>)</h2></div>
  <table>
    <thead><tr><th>Nom</th><th>Email</th><th>Sujet</th><th>Message</th><th>Date</th><th>Lu</th><th>Actions</th></tr></thead>
    <tbody>
      <?php foreach ($messages as $m): ?>
      <tr style="<?= !$m['is_read']?'font-weight:600':'' ?>">
        <td><?= e($m['sender_name']) ?></td>
        <td><a href="mailto:<?= e($m['sender_email']) ?>"><?= e($m['sender_email']) ?></a></td>
        <td><?= e($m['subject']) ?></td>
        <td><?= e(truncate($m['message'], 80)) ?></td>
        <td style="font-size:.8rem;color:#888"><?= date('d/m/Y H:i', strtotime($m['created_at'])) ?></td>
        <td><?= $m['is_read']?'<span class="badge badge-green">Lu</span>':'<a href="?read='.$m['id'].'" class="badge badge-orange">Non lu</a>' ?></td>
        <td><a href="?delete=<?= $m['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer?')"><i class="fas fa-trash"></i></a></td>
      </tr>
      <?php endforeach; ?>
      <?php if (!$messages): ?><tr><td colspan="7" style="text-align:center;color:#888;padding:2rem">Aucun message</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>

<?php require_once 'includes/admin_footer.php'; ?>
