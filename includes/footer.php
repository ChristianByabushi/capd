<?php
$facebook  = getSetting('facebook');
$twitter   = getSetting('twitter');
$youtube   = getSetting('youtube');
$instagram = getSetting('instagram');
$linkedin  = getSetting('linkedin');
$tiktok    = getSetting('tiktok');
$phone     = getSetting('phone');
$email_f   = getSetting('site_email');
$address   = getSetting('address_' . getLang()) ?: getSetting('address_fr');
?>
<footer class="footer" role="contentinfo">
  <div class="container">
    <div class="footer-grid">

      <!-- Brand -->
      <div class="footer-brand">
        <img src="<?= BASE_URL ?>/images/logo-capd-300x292.png" alt="CAPD ASBL">
        <p><?= t('about_members_summary') ?></p>
        <div class="footer-social">
          <?php if ($facebook): ?><a href="<?= e($facebook) ?>" target="_blank" rel="noopener" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a><?php endif; ?>
          <?php if ($twitter): ?><a href="<?= e($twitter) ?>" target="_blank" rel="noopener" aria-label="Twitter"><i class="fab fa-twitter"></i></a><?php endif; ?>
          <?php if ($instagram): ?><a href="<?= e($instagram) ?>" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram"></i></a><?php endif; ?>
          <?php if ($youtube): ?><a href="<?= e($youtube) ?>" target="_blank" rel="noopener" aria-label="YouTube"><i class="fab fa-youtube"></i></a><?php endif; ?>
          <?php if ($linkedin): ?><a href="<?= e($linkedin) ?>" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a><?php endif; ?>
          <?php if ($tiktok): ?><a href="<?= e($tiktok) ?>" target="_blank" rel="noopener" aria-label="TikTok"><i class="fab fa-tiktok"></i></a><?php endif; ?>
          <?php if ($phone): ?><a href="https://wa.me/<?= preg_replace('/\D/', '', $phone) ?>" target="_blank" rel="noopener" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a><?php endif; ?>
        </div>
      </div>

      <!-- Quick links -->
      <div>
        <h4><?= t('nav_about') ?></h4>
        <ul>
          <li><a href="<?= BASE_URL ?>/about.php"><?= t('nav_about') ?></a></li>
          <li><a href="<?= BASE_URL ?>/members.php"><?= t('nav_members') ?></a></li>
          <li><a href="<?= BASE_URL ?>/activities.php"><?= t('nav_activities') ?></a></li>
          <li><a href="<?= BASE_URL ?>/formations.php"><?= t('nav_formations') ?></a></li>
          <li><a href="<?= BASE_URL ?>/blog.php"><?= t('nav_blog') ?></a></li>
        </ul>
      </div>

      <!-- Domains -->
      <div>
        <h4><?= t('domain_paix') ?></h4>
        <ul>
          <li><a href="#"><?= t('domain_education') ?></a></li>
          <li><a href="#"><?= t('domain_formations') ?></a></li>
          <li><a href="#"><?= t('domain_genre') ?></a></li>
          <li><a href="#"><?= t('domain_entrepreneuriat') ?></a></li>
          <li><a href="#"><?= t('domain_sante') ?></a></li>
          <li><a href="#"><?= t('domain_environnement') ?></a></li>
        </ul>
      </div>

      <!-- Contact -->
      <div>
        <h4><?= t('contact_title') ?></h4>
        <ul>
          <?php if ($address): ?>
          <li><i class="fas fa-map-marker-alt" style="color:var(--accent);margin-right:.5rem"></i><?= e($address) ?></li>
          <?php endif; ?>
          <?php if ($phone): ?>
          <li><i class="fas fa-phone" style="color:var(--accent);margin-right:.5rem"></i><a href="tel:<?= e($phone) ?>"><?= e($phone) ?></a></li>
          <?php endif; ?>
          <?php if ($email_f): ?>
          <li><i class="fas fa-envelope" style="color:var(--accent);margin-right:.5rem"></i><a href="mailto:<?= e($email_f) ?>"><?= e($email_f) ?></a></li>
          <?php endif; ?>
        </ul>
      </div>

    </div>
  </div>

  <div class="footer-bottom">
    <div class="container">
      &copy; <?= date('Y') ?> <?= e($siteName) ?> — <?= t('footer_rights') ?>
    </div>
  </div>
</footer>

<script src="<?= BASE_URL ?>/assets/js/main.js"></script>
<?= $extraScripts ?? '' ?>
</body>
</html>
