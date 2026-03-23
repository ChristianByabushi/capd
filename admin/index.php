<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/auth.php';
requireLogin();
$adminTitle = 'Tableau de bord';

$counts = [
    'activities' => fetchOne("SELECT COUNT(*) c FROM activities")['c'],
    'posts'      => fetchOne("SELECT COUNT(*) c FROM posts WHERE is_published=1")['c'],
    'members'    => fetchOne("SELECT COUNT(*) c FROM members WHERE is_active=1")['c'],
    'messages'   => fetchOne("SELECT COUNT(*) c FROM contact_messages WHERE is_read=0")['c'],
    'partners'   => fetchOne("SELECT COUNT(*) c FROM partners WHERE is_active=1")['c'],
    'feedbacks'  => fetchOne("SELECT COUNT(*) c FROM feedbacks WHERE is_approved=0")['c'],
];
$recentMessages = fetchAll("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 5");
$recentPosts    = fetchAll("SELECT * FROM posts ORDER BY created_at DESC LIMIT 5");

require_once 'includes/admin_header.php';
?>

<div class="admin-cards">
  <div class="admin-card">
    <div class="admin-card-icon" style="background:#1a6b3c"><i class="fas fa-project-diagram"></i></div>
    <div><h3><?= $counts['activities'] ?></h3><p>Activités</p></div>
  </div>
  <div class="admin-card">
    <div class="admin-card-icon" style="background:#f4a61d"><i class="fas fa-newspaper"></i></div>
    <div><h3><?= $counts['posts'] ?></h3><p>Articles publiés</p></div>
  </div>
  <div class="admin-card">
    <div class="admin-card-icon" style="background:#17a2b8"><i class="fas fa-users"></i></div>
    <div><h3><?= $counts['members'] ?></h3><p>Membres actifs</p></div>
  </div>
  <div class="admin-card">
    <div class="admin-card-icon" style="background:#dc3545"><i class="fas fa-envelope"></i></div>
    <div><h3><?= $counts['messages'] ?></h3><p>Messages non lus</p></div>
  </div>
  <div class="admin-card">
    <div class="admin-card-icon" style="background:#6f42c1"><i class="fas fa-handshake"></i></div>
    <div><h3><?= $counts['partners'] ?></h3><p>Partenaires</p></div>
  </div>
  <div class="admin-card">
    <div class="admin-card-icon" style="background:#fd7e14"><i class="fas fa-star"></i></div>
    <div><h3><?= $counts['feedbacks'] ?></h3><p>Témoignages en attente</p></div>
  </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem">

  <!-- Recent messages -->
  <div class="admin-table-wrap">
    <div class="admin-table-header">
      <h2>Messages récents</h2>
      <a href="messages.php" class="btn btn-sm btn-outline">Voir tout</a>
    </div>
    <table>
      <thead><tr><th>Nom</th><th>Sujet</th><th>Date</th></tr></thead>
      <tbody>
        <?php foreach ($recentMessages as $msg): ?>
        <tr>
          <td><?= e($msg['sender_name']) ?></td>
          <td><?= e(truncate($msg['subject'] ?: $msg['message'], 40)) ?></td>
          <td style="color:#888;font-size:.8rem"><?= date('d/m/Y', strtotime($msg['created_at'])) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$recentMessages): ?><tr><td colspan="3" style="text-align:center;color:#888;padding:1.5rem">Aucun message</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Recent posts -->
  <div class="admin-table-wrap">
    <div class="admin-table-header">
      <h2>Articles récents</h2>
      <a href="blog.php" class="btn btn-sm btn-outline">Voir tout</a>
    </div>
    <table>
      <thead><tr><th>Titre</th><th>Statut</th><th>Date</th></tr></thead>
      <tbody>
        <?php foreach ($recentPosts as $p): ?>
        <tr>
          <td><?= e(truncate($p['title_fr'] ?: $p['title_en'], 40)) ?></td>
          <td><span class="badge <?= $p['is_published']?'badge-green':'badge-orange' ?>"><?= $p['is_published']?'Publié':'Brouillon' ?></span></td>
          <td style="color:#888;font-size:.8rem"><?= date('d/m/Y', strtotime($p['created_at'])) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$recentPosts): ?><tr><td colspan="3" style="text-align:center;color:#888;padding:1.5rem">Aucun article</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>

</div>

<?php require_once 'includes/admin_footer.php'; ?>
