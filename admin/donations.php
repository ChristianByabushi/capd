<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/auth.php';
requireLogin();
$adminTitle = 'Dons reçus';

if (isset($_GET['confirm'])) {
    query("UPDATE donations SET status='confirmed' WHERE id=?", [(int)$_GET['confirm']]);
    header('Location: donations.php'); exit;
}
if (isset($_GET['delete'])) {
    query("DELETE FROM donations WHERE id=?", [(int)$_GET['delete']]);
    header('Location: donations.php'); exit;
}

$donations = fetchAll("SELECT * FROM donations ORDER BY created_at DESC");
$total_confirmed = fetchOne("SELECT SUM(amount) s FROM donations WHERE status='confirmed' AND currency='CDF'")['s'] ?? 0;
$total_usd = fetchOne("SELECT SUM(amount) s FROM donations WHERE status='confirmed' AND currency='USD'")['s'] ?? 0;
require_once 'includes/admin_header.php';
?>

<div class="admin-cards" style="margin-bottom:2rem">
  <div class="admin-card">
    <div class="admin-card-icon" style="background:#1a6b3c"><i class="fas fa-donate"></i></div>
    <div><h3><?= count($donations) ?></h3><p>Total dons enregistrés</p></div>
  </div>
  <div class="admin-card">
    <div class="admin-card-icon" style="background:#f4a61d"><i class="fas fa-check-circle"></i></div>
    <div><h3><?= number_format((float)$total_confirmed) ?> CDF</h3><p>Confirmés (CDF)</p></div>
  </div>
  <?php if ($total_usd > 0): ?>
  <div class="admin-card">
    <div class="admin-card-icon" style="background:#17a2b8"><i class="fas fa-dollar-sign"></i></div>
    <div><h3>$<?= number_format((float)$total_usd, 2) ?></h3><p>Confirmés (USD)</p></div>
  </div>
  <?php endif; ?>
</div>

<div class="admin-table-wrap">
  <div class="admin-table-header"><h2>Liste des dons</h2></div>
  <table>
    <thead>
      <tr>
        <th>#</th><th>Donateur</th><th>Téléphone</th><th>Montant</th>
        <th>Référence</th><th>Motivation</th><th>Statut</th><th>Date</th><th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($donations as $d): ?>
      <tr>
        <td><?= $d['id'] ?></td>
        <td><?= $d['is_anonymous'] ? '<em style="color:#aaa">Anonyme</em>' : e($d['donor_name'] ?: '—') ?></td>
        <td><?= e($d['phone']) ?></td>
        <td><strong><?= number_format((float)$d['amount']) ?> <?= e($d['currency']) ?></strong></td>
        <td style="font-size:.8rem;color:#888"><?= e($d['transaction_ref'] ?: '—') ?></td>
        <td style="font-size:.82rem;max-width:180px"><?= e(truncate($d['motivation'] ?: '', 60)) ?></td>
        <td>
          <span class="badge <?= $d['status']==='confirmed'?'badge-green':($d['status']==='failed'?'badge-red':'badge-orange') ?>">
            <?= e($d['status']) ?>
          </span>
        </td>
        <td style="font-size:.8rem;color:#888"><?= date('d/m/Y H:i', strtotime($d['created_at'])) ?></td>
        <td>
          <?php if ($d['status']==='pending'): ?>
          <a href="?confirm=<?= $d['id'] ?>" class="btn btn-sm btn-accent" title="Confirmer"><i class="fas fa-check"></i></a>
          <?php endif; ?>
          <a href="?delete=<?= $d['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer?')"><i class="fas fa-trash"></i></a>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (!$donations): ?>
      <tr><td colspan="9" style="text-align:center;color:#888;padding:2rem">Aucun don enregistré</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php require_once 'includes/admin_footer.php'; ?>
