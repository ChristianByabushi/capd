<?php
require_once 'helpers.php';
require_once 'db.php';
$pageTitle = 'Faire un don — CAPD ASBL';
$pageDesc  = 'Soutenez CAPD ASBL via Airtel Money et contribuez à la paix et au développement en RDC.';

$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrfVerify();
    $phone   = strip_tags(trim($_POST['phone'] ?? ''));
    $amount  = (float)($_POST['amount'] ?? 0);
    $currency = in_array($_POST['currency'] ?? '', ['CDF','USD']) ? $_POST['currency'] : 'CDF';
    $name    = strip_tags(trim($_POST['donor_name'] ?? ''));
    $email   = trim($_POST['donor_email'] ?? '');
    $ref     = strip_tags(trim($_POST['transaction_ref'] ?? ''));
    $motiv   = strip_tags(trim($_POST['motivation'] ?? ''));
    $anon    = isset($_POST['is_anonymous']) ? 1 : 0;

    if ($phone && $amount > 0) {
        query(
            "INSERT INTO donations (donor_name,donor_email,phone,amount,currency,transaction_ref,motivation,is_anonymous) VALUES (?,?,?,?,?,?,?,?)",
            'ssdsssi',
            [$name ?: null, $email ?: null, $phone, $amount, $currency, $ref ?: null, $motiv ?: null, $anon]
        );
        $success = true;
    } else {
        $error = 'Veuillez entrer votre numéro Airtel Money et le montant.';
    }
}

$airtelNumber = getSetting('airtel_money_number') ?: '+243 000 000 000';
$airtelName   = getSetting('airtel_money_name') ?: 'CAPD ASBL';
$facebook     = getSetting('facebook');
$twitter      = getSetting('twitter');
$youtube      = getSetting('youtube');
$instagram    = getSetting('instagram');
$linkedin     = getSetting('linkedin');
$tiktok       = getSetting('tiktok');
$phone_s      = getSetting('phone');

require_once 'includes/header.php';
?>

<div class="page-hero">
  <div class="container">
    <h1>Faire un don</h1>
    <p>Votre soutien fait la différence pour la paix et le développement en RDC.</p>
    <nav class="breadcrumb" aria-label="Fil d'Ariane">
      <a href="<?= BASE_URL ?>">Accueil</a>
      <span>/</span><span>Faire un don</span>
    </nav>
  </div>
</div>

<section class="section">
  <div class="container">
    <div class="donate-layout">

      <!-- LEFT: Airtel Money instructions + form -->
      <div class="donate-main">

        <?php if ($success): ?>
        <div class="donate-success">
          <div class="donate-success-icon"><i class="fas fa-check-circle"></i></div>
          <h2>Merci pour votre don !</h2>
          <p>Votre contribution a été enregistrée. Nous vous remercions sincèrement pour votre soutien à la mission de CAPD ASBL.</p>
          <a href="<?= BASE_URL ?>" class="btn btn-primary" style="margin-top:1rem">Retour à l'accueil</a>
        </div>
        <?php else: ?>

        <!-- How to send via Airtel Money -->
        <div class="airtel-steps">
          <div class="airtel-header">
            <div class="airtel-logo-wrap">
              <i class="fas fa-mobile-alt"></i>
            </div>
            <div>
              <h2>Envoyer via Airtel Money</h2>
              <p>Transfert rapide, sécurisé et sans frais supplémentaires</p>
            </div>
          </div>

          <div class="airtel-number-box">
            <span class="airtel-number-label">Numéro Airtel Money</span>
            <span class="airtel-number"><?= e($airtelNumber) ?></span>
            <span class="airtel-number-name"><?= e($airtelName) ?></span>
          </div>

          <ol class="airtel-steps-list">
            <li>
              <span class="step-num">1</span>
              <div>
                <strong>Composez *500#</strong>
                <span>Ouvrez le menu Airtel Money sur votre téléphone</span>
              </div>
            </li>
            <li>
              <span class="step-num">2</span>
              <div>
                <strong>Sélectionnez "Envoyer de l'argent"</strong>
                <span>Choisissez l'option de transfert vers un autre numéro</span>
              </div>
            </li>
            <li>
              <span class="step-num">3</span>
              <div>
                <strong>Entrez le numéro <?= e($airtelNumber) ?></strong>
                <span>Nom du bénéficiaire : <?= e($airtelName) ?></span>
              </div>
            </li>
            <li>
              <span class="step-num">4</span>
              <div>
                <strong>Entrez le montant et confirmez</strong>
                <span>Notez la référence de transaction affichée</span>
              </div>
            </li>
            <li>
              <span class="step-num">5</span>
              <div>
                <strong>Remplissez le formulaire ci-dessous</strong>
                <span>Pour confirmer votre don et recevoir un accusé de réception</span>
              </div>
            </li>
          </ol>
        </div>

        <!-- Confirmation form -->
        <div class="donate-form-wrap">
          <h3>Confirmer votre don</h3>
          <p style="color:var(--gray);font-size:.9rem;margin-bottom:1.5rem">Tous les champs sont optionnels sauf le numéro et le montant.</p>

          <?php if ($error): ?>
          <div class="alert alert-error"><?= e($error) ?></div>
          <?php endif; ?>

          <form method="POST" action="donate.php" novalidate>
            <div class="form-row-2">
              <div class="form-group">
                <label for="phone">Numéro Airtel Money *</label>
                <input type="tel" id="phone" name="phone" class="form-control"
                       placeholder="+243 0XX XXX XXXX" required
                       value="<?= e($_POST['phone'] ?? '') ?>">
              </div>
              <div class="form-group">
                <label for="amount">Montant *</label>
                <div class="amount-input-wrap">
                  <input type="number" id="amount" name="amount" class="form-control"
                         placeholder="Ex: 5000" min="1" required
                         value="<?= e($_POST['amount'] ?? '') ?>">
                  <select name="currency" class="currency-select">
                    <option value="CDF" <?= ($_POST['currency']??'CDF')==='CDF'?'selected':'' ?>>CDF</option>
                    <option value="USD" <?= ($_POST['currency']??'')==='USD'?'selected':'' ?>>USD</option>
                  </select>
                </div>
              </div>
            </div>

            <!-- Quick amount buttons -->
            <div class="quick-amounts">
              <span style="font-size:.85rem;color:var(--gray);font-weight:600">Montants suggérés :</span>
              <?php foreach (['1000','2500','5000','10000','25000'] as $amt): ?>
              <button type="button" class="quick-amt-btn" onclick="document.getElementById('amount').value='<?= $amt ?>'">
                <?= number_format((int)$amt) ?> CDF
              </button>
              <?php endforeach; ?>
            </div>

            <div class="form-group">
              <label for="transaction_ref">Référence de transaction (optionnel)</label>
              <input type="text" id="transaction_ref" name="transaction_ref" class="form-control"
                     placeholder="Ex: TXN123456789"
                     value="<?= e($_POST['transaction_ref'] ?? '') ?>">
            </div>

            <div class="form-row-2">
              <div class="form-group">
                <label for="donor_name">Votre nom (optionnel)</label>
                <input type="text" id="donor_name" name="donor_name" class="form-control"
                       placeholder="Nom complet"
                       value="<?= e($_POST['donor_name'] ?? '') ?>">
              </div>
              <div class="form-group">
                <label for="donor_email">Email (optionnel)</label>
                <input type="email" id="donor_email" name="donor_email" class="form-control"
                       placeholder="Pour accusé de réception"
                       value="<?= e($_POST['donor_email'] ?? '') ?>">
              </div>
            </div>

            <div class="form-group">
              <label for="motivation">Motivation / Message (optionnel)</label>
              <textarea id="motivation" name="motivation" class="form-control" rows="3"
                        placeholder="Pourquoi soutenez-vous CAPD ASBL ?"><?= e($_POST['motivation'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
              <label class="checkbox-label">
                <input type="checkbox" name="is_anonymous" <?= isset($_POST['is_anonymous'])?'checked':'' ?>>
                <span>Rester anonyme (votre nom ne sera pas affiché publiquement)</span>
              </label>
            </div>

            <button type="submit" class="btn btn-accent" style="width:100%;justify-content:center;font-size:1rem;padding:1rem">
              <i class="fas fa-paper-plane"></i> Confirmer mon don
            </button>
            <?= csrfField() ?>
          </form>
        </div>

        <?php endif; ?>
      </div>

      <!-- RIGHT: Impact + social -->
      <aside class="donate-sidebar">

        <!-- Impact -->
        <div class="donate-impact">
          <h3>Votre impact</h3>
          <div class="impact-items">
            <div class="impact-item">
              <span class="impact-amount">1 000 CDF</span>
              <span class="impact-desc">Fournitures scolaires pour un enfant</span>
            </div>
            <div class="impact-item">
              <span class="impact-amount">5 000 CDF</span>
              <span class="impact-desc">Formation d'un jeune entrepreneur pendant une journée</span>
            </div>
            <div class="impact-item">
              <span class="impact-amount">10 000 CDF</span>
              <span class="impact-desc">Atelier de sensibilisation à la paix pour 10 personnes</span>
            </div>
            <div class="impact-item">
              <span class="impact-amount">25 000 CDF</span>
              <span class="impact-desc">Soutien à une activité communautaire complète</span>
            </div>
          </div>
        </div>

        <!-- Social media -->
        <div class="donate-social">
          <h3>Partagez notre cause</h3>
          <p style="font-size:.875rem;color:var(--gray);margin-bottom:1.25rem">
            Parlez de CAPD ASBL autour de vous et aidez-nous à toucher plus de personnes.
          </p>
          <div class="social-links-grid">
            <?php if ($phone_s): ?>
            <a href="https://wa.me/<?= preg_replace('/\D/','',$phone_s) ?>?text=<?= urlencode('Soutenez CAPD ASBL — '.BASE_URL.'/donate.php') ?>"
               target="_blank" rel="noopener" class="social-link whatsapp">
              <i class="fab fa-whatsapp"></i><span>WhatsApp</span>
            </a>
            <?php endif; ?>
            <?php if ($facebook): ?>
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(BASE_URL.'/donate.php') ?>"
               target="_blank" rel="noopener" class="social-link facebook">
              <i class="fab fa-facebook-f"></i><span>Facebook</span>
            </a>
            <?php endif; ?>
            <?php if ($twitter): ?>
            <a href="https://twitter.com/intent/tweet?text=<?= urlencode('Je soutiens CAPD ASBL pour la paix et le développement en RDC') ?>&url=<?= urlencode(BASE_URL.'/donate.php') ?>"
               target="_blank" rel="noopener" class="social-link twitter">
              <i class="fab fa-twitter"></i><span>Twitter</span>
            </a>
            <?php endif; ?>
            <?php if ($instagram): ?>
            <a href="<?= e($instagram) ?>" target="_blank" rel="noopener" class="social-link instagram">
              <i class="fab fa-instagram"></i><span>Instagram</span>
            </a>
            <?php endif; ?>
            <?php if ($linkedin): ?>
            <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= urlencode(BASE_URL.'/donate.php') ?>"
               target="_blank" rel="noopener" class="social-link linkedin">
              <i class="fab fa-linkedin-in"></i><span>LinkedIn</span>
            </a>
            <?php endif; ?>
            <?php if ($tiktok): ?>
            <a href="<?= e($tiktok) ?>" target="_blank" rel="noopener" class="social-link tiktok">
              <i class="fab fa-tiktok"></i><span>TikTok</span>
            </a>
            <?php endif; ?>
            <!-- Always show these share links regardless of settings -->
            <?php if (!$facebook): ?>
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(BASE_URL.'/donate.php') ?>"
               target="_blank" rel="noopener" class="social-link facebook">
              <i class="fab fa-facebook-f"></i><span>Facebook</span>
            </a>
            <?php endif; ?>
            <?php if (!$twitter): ?>
            <a href="https://twitter.com/intent/tweet?text=<?= urlencode('Je soutiens CAPD ASBL') ?>&url=<?= urlencode(BASE_URL.'/donate.php') ?>"
               target="_blank" rel="noopener" class="social-link twitter">
              <i class="fab fa-twitter"></i><span>Twitter</span>
            </a>
            <?php endif; ?>
          </div>
        </div>

        <!-- Trust -->
        <div class="donate-trust">
          <i class="fas fa-shield-alt"></i>
          <div>
            <strong>Don sécurisé</strong>
            <span>Vos informations sont protégées et utilisées uniquement pour confirmer votre don.</span>
          </div>
        </div>

      </aside>
    </div>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
