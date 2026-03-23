<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/auth.php';
requireRole('superadmin', 'admin');
$adminTitle = 'Gestion des utilisateurs';
$msg = $err = '';
$generatedPass = null;

// ── Delete user ───────────────────────────────────────────────
if (isset($_GET['delete'])) {
    if (!can('delete_user')) { $err = 'Action non autorisée.'; }
    else {
        $target = fetchOne("SELECT * FROM admin_users WHERE id=?", [(int)$_GET['delete']]);
        if ($target && $target['id'] !== (int)$_SESSION['admin_id']) {
            query("DELETE FROM admin_users WHERE id=?", [(int)$_GET['delete']]);
            header('Location: users.php?saved=1'); exit;
        } else {
            $err = 'Impossible de supprimer cet utilisateur.';
        }
    }
}

// ── Toggle active ─────────────────────────────────────────────
if (isset($_GET['toggle'])) {
    $target = fetchOne("SELECT * FROM admin_users WHERE id=?", [(int)$_GET['toggle']]);
    if ($target && $target['role'] === 'superadmin' && !isSuperAdmin()) {
        $err = 'Action non autorisée.';
    } elseif ($target && $target['id'] !== (int)$_SESSION['admin_id']) {
        $newState = $target['is_active'] ? 0 : 1;
        query("UPDATE admin_users SET is_active=? WHERE id=?", [$newState, $target['id']]);
        header('Location: users.php'); exit;
    }
}

// ── Reset password ────────────────────────────────────────────
if (isset($_GET['reset'])) {
    $target = fetchOne("SELECT * FROM admin_users WHERE id=?", [(int)$_GET['reset']]);
    if (!$target) { $err = 'Utilisateur introuvable.'; }
    elseif ($target['role'] === 'superadmin' && !isSuperAdmin()) { $err = 'Action non autorisée.'; }
    elseif ($target['role'] === 'admin' && !isSuperAdmin()) { $err = 'Seul le superadmin peut réinitialiser un admin.'; }
    else {
        $newPass = generatePassword();
        query("UPDATE admin_users SET password=? WHERE id=?", [hashPassword($newPass), $target['id']]);
        $generatedPass = ['name' => $target['full_name'], 'pass' => $newPass];
    }
}

// ── Save (create / edit) ──────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrfVerify();
    $id        = (int)($_POST['id'] ?? 0);
    $username  = trim($_POST['username'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $role      = $_POST['role'] ?? 'editor';
    $active    = isset($_POST['is_active']) ? 1 : 0;

    // Role permission guard
    if ($role === 'superadmin' && !isSuperAdmin()) { $err = 'Vous ne pouvez pas créer un superadmin.'; }
    elseif ($role === 'admin' && !isSuperAdmin()) { $err = 'Seul le superadmin peut créer un admin.'; }
    elseif (!$username || !$full_name) { $err = 'Nom d\'utilisateur et nom complet requis.'; }
    else {
        if ($id) {
            // Edit existing — cannot change role of superadmin unless you are superadmin
            $existing = fetchOne("SELECT * FROM admin_users WHERE id=?", [$id]);
            if ($existing['role'] === 'superadmin' && !isSuperAdmin()) {
                $err = 'Action non autorisée.';
            } else {
                query("UPDATE admin_users SET username=?,full_name=?,role=?,is_active=? WHERE id=?",
                    [$username, $full_name, $role, $active, $id]);
                $msg = 'Utilisateur mis à jour.';
            }
        } else {
            // Create new — generate password
            $plain = generatePassword();
            query("INSERT INTO admin_users (username,password,full_name,role,is_active,created_by) VALUES (?,?,?,?,?,?)",
                [$username, hashPassword($plain), $full_name, $role, 1, $_SESSION['admin_id']]);
            $generatedPass = ['name' => $full_name, 'pass' => $plain];
            $msg = 'Utilisateur créé.';
        }
        if (!$err) { header('Location: users.php?saved=1'); exit; }
    }
}

// ── Load data ─────────────────────────────────────────────────
$edit = isset($_GET['edit']) ? fetchOne("SELECT * FROM admin_users WHERE id=?", [(int)$_GET['edit']]) : null;

// Superadmin sees all; admin sees only editors + themselves
if (isSuperAdmin()) {
    $users = fetchAll("SELECT u.*, c.full_name AS creator FROM admin_users u LEFT JOIN admin_users c ON u.created_by=c.id ORDER BY FIELD(u.role,'superadmin','admin','editor'), u.full_name");
} else {
    $users = fetchAll("SELECT u.*, c.full_name AS creator FROM admin_users u LEFT JOIN admin_users c ON u.created_by=c.id WHERE u.role='editor' OR u.id=? ORDER BY u.full_name", [$_SESSION['admin_id']]);
}

require_once 'includes/admin_header.php';
?>

<?php if ($generatedPass): ?>
<div class="alert alert-success" style="font-size:1rem">
  <i class="fas fa-key"></i>
  Mot de passe généré pour <strong><?= e($generatedPass['name']) ?></strong> :
  <code style="background:#fff;padding:.2rem .6rem;border-radius:4px;font-size:1rem;letter-spacing:.05em;margin:0 .5rem"><?= e($generatedPass['pass']) ?></code>
  <strong>Notez-le maintenant — il ne sera plus affiché.</strong>
</div>
<?php endif; ?>
<?php if (isset($_GET['saved']) && !$generatedPass): ?><div class="alert alert-success">Enregistré.</div><?php endif; ?>
<?php if ($err): ?><div class="alert alert-error"><?= e($err) ?></div><?php endif; ?>

<!-- Form -->
<div class="admin-form" style="margin-bottom:2rem">
  <h2 style="margin-bottom:1.5rem;color:#1a6b3c"><?= $edit ? 'Modifier l\'utilisateur' : 'Nouvel utilisateur' ?></h2>
  <form method="POST">
    <?= csrfField() ?>
    <?php if ($edit): ?><input type="hidden" name="id" value="<?= $edit['id'] ?>"><?php endif; ?>
    <div class="form-row">
      <div class="form-group">
        <label>Nom d'utilisateur *</label>
        <input type="text" name="username" class="form-control" required
               value="<?= e($edit['username'] ?? '') ?>" autocomplete="off">
      </div>
      <div class="form-group">
        <label>Nom complet *</label>
        <input type="text" name="full_name" class="form-control" required
               value="<?= e($edit['full_name'] ?? '') ?>">
      </div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label>Rôle</label>
        <select name="role" class="form-control">
          <?php if (isSuperAdmin()): ?>
          <option value="superadmin" <?= ($edit['role']??'')==='superadmin'?'selected':'' ?>>Superadmin</option>
          <option value="admin" <?= ($edit['role']??'')==='admin'?'selected':'' ?>>Admin</option>
          <?php endif; ?>
          <option value="editor" <?= ($edit['role']??'editor')==='editor'?'selected':'' ?>>Éditeur</option>
        </select>
        <small style="color:#888">
          Superadmin : accès total &nbsp;|&nbsp;
          Admin : contenu + gestion éditeurs &nbsp;|&nbsp;
          Éditeur : activités, blog, formations uniquement
        </small>
      </div>
      <div class="form-group" style="display:flex;align-items:flex-end;padding-bottom:1.25rem">
        <label><input type="checkbox" name="is_active" <?= ($edit['is_active']??1)?'checked':'' ?>> Compte actif</label>
      </div>
    </div>
    <?php if (!$edit): ?>
    <p style="color:#888;font-size:.875rem;margin-bottom:1rem">
      <i class="fas fa-info-circle" style="color:#f4a61d"></i>
      Un mot de passe fort sera généré automatiquement et affiché une seule fois après la création.
    </p>
    <?php endif; ?>
    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Enregistrer</button>
    <?php if ($edit): ?><a href="users.php" class="btn btn-outline" style="margin-left:.5rem">Annuler</a><?php endif; ?>
  </form>
</div>

<!-- User list -->
<div class="admin-table-wrap">
  <div class="admin-table-header"><h2>Utilisateurs (<?= count($users) ?>)</h2></div>
  <table>
    <thead>
      <tr>
        <th>Nom</th><th>Identifiant</th><th>Rôle</th><th>Statut</th>
        <th>Créé par</th><th>Dernière connexion</th><th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($users as $u):
        $isSelf = $u['id'] === (int)$_SESSION['admin_id'];
        $canEdit   = isSuperAdmin() || ($u['role'] === 'editor');
        $canDelete = can('delete_user') && !$isSelf;
        $canReset  = (isSuperAdmin()) || ($u['role'] === 'editor' && can('reset_editor_pass'));
      ?>
      <tr>
        <td>
          <?= e($u['full_name']) ?>
          <?php if ($isSelf): ?><span class="badge badge-blue" style="margin-left:.4rem">Vous</span><?php endif; ?>
        </td>
        <td><code><?= e($u['username']) ?></code></td>
        <td>
          <?php
            $roleColors = ['superadmin'=>'badge-red','admin'=>'badge-orange','editor'=>'badge-blue'];
            $roleLabels = ['superadmin'=>'Superadmin','admin'=>'Admin','editor'=>'Éditeur'];
          ?>
          <span class="badge <?= $roleColors[$u['role']] ?? 'badge-blue' ?>"><?= $roleLabels[$u['role']] ?? $u['role'] ?></span>
        </td>
        <td><span class="badge <?= $u['is_active']?'badge-green':'badge-red' ?>"><?= $u['is_active']?'Actif':'Inactif' ?></span></td>
        <td style="font-size:.82rem;color:#888"><?= e($u['creator'] ?? '—') ?></td>
        <td style="font-size:.82rem;color:#888"><?= $u['last_login'] ? date('d/m/Y H:i', strtotime($u['last_login'])) : '—' ?></td>
        <td style="white-space:nowrap">
          <?php if ($canEdit): ?>
          <a href="?edit=<?= $u['id'] ?>" class="btn btn-sm btn-outline" title="Modifier"><i class="fas fa-edit"></i></a>
          <?php endif; ?>
          <?php if ($canReset && !$isSelf): ?>
          <a href="?reset=<?= $u['id'] ?>" class="btn btn-sm btn-accent"
             onclick="return confirm('Réinitialiser le mot de passe de <?= e($u['full_name']) ?> ?')"
             title="Réinitialiser mot de passe"><i class="fas fa-key"></i></a>
          <?php endif; ?>
          <?php if (!$isSelf && isAdmin()): ?>
          <a href="?toggle=<?= $u['id'] ?>" class="btn btn-sm <?= $u['is_active']?'btn-outline':'btn-accent' ?>"
             title="<?= $u['is_active']?'Désactiver':'Activer' ?>">
            <i class="fas fa-<?= $u['is_active']?'ban':'check' ?>"></i>
          </a>
          <?php endif; ?>
          <?php if ($canDelete): ?>
          <a href="?delete=<?= $u['id'] ?>" class="btn btn-sm btn-danger"
             onclick="return confirm('Supprimer définitivement <?= e($u['full_name']) ?> ?')"
             title="Supprimer"><i class="fas fa-trash"></i></a>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php require_once 'includes/admin_footer.php'; ?>
