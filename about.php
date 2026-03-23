<?php
require_once 'helpers.php';
require_once 'db.php';
$pageTitle = t('about_title') . ' — CAPD ASBL';
require_once 'includes/header.php';
$members = fetchAll("SELECT * FROM members WHERE is_active=1 ORDER BY display_order ASC");
$organs = ['conseil_administration','comite_gestion','comite_controle','secretariat_executif'];
$organKeys = ['conseil_administration'=>'organ_ca','comite_gestion'=>'organ_cg','comite_controle'=>'organ_cc','secretariat_executif'=>'organ_se'];
?>

<div class="page-hero">
  <div class="container">
    <h1><?= t('about_title') ?></h1>
    <nav class="breadcrumb" aria-label="Fil d'Ariane">
      <a href="<?= BASE_URL ?>"><?= t('nav_home') ?></a>
      <span>/</span><span><?= t('about_title') ?></span>
    </nav>
  </div>
</div>

<!-- Vision & Mission -->
<section class="section">
  <div class="container">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:3rem;align-items:center">
      <div>
        <h2 style="color:var(--primary)"><?= t('about_vision') ?></h2>
        <p>La vision du CAPD est de créer un monde où règnent la paix perpétuelle et le développement durable à travers une jeunesse consciente et soucieuse de l'épanouissement.</p>
        <h2 style="color:var(--primary);margin-top:2rem"><?= t('about_mission') ?></h2>
        <p>Promouvoir la culture de la paix et du développement intégral à travers des actions concrètes en faveur de la jeunesse et des communautés.</p>
      </div>
      <div style="background:linear-gradient(135deg,var(--primary),var(--primary-light));border-radius:var(--radius);padding:2.5rem;color:white">
        <h3 style="color:white;margin-bottom:1.5rem">Objectifs spécifiques</h3>
        <ul style="list-style:none;display:flex;flex-direction:column;gap:.75rem">
          <?php
          $objectives = [
            ['icon'=>'fa-graduation-cap', 'text'=>t('domain_education')],
            ['icon'=>'fa-tools',          'text'=>t('domain_formations')],
            ['icon'=>'fa-venus-mars',     'text'=>t('domain_genre')],
            ['icon'=>'fa-lightbulb',      'text'=>t('domain_entrepreneuriat')],
            ['icon'=>'fa-hands-helping',  'text'=>t('domain_aide')],
            ['icon'=>'fa-dove',           'text'=>t('domain_paix')],
          ];
          foreach ($objectives as $obj): ?>
          <li style="display:flex;gap:.75rem;align-items:center">
            <i class="fas <?= $obj['icon'] ?>" style="color:var(--accent);width:20px" aria-hidden="true"></i>
            <span><?= $obj['text'] ?></span>
          </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  </div>
</section>

<!-- Members summary -->
<section class="section section-alt">
  <div class="container">
    <div class="section-title">
      <h2><?= t('about_organs') ?></h2>
      <p><?= t('about_members_summary') ?></p>
    </div>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1.5rem;margin-bottom:3rem">
      <?php
      $organCards = [
        ['icon'=>'fa-landmark',    'key'=>'organ_ca', 'count'=>6],
        ['icon'=>'fa-cogs',        'key'=>'organ_cg', 'count'=>null],
        ['icon'=>'fa-search',      'key'=>'organ_cc', 'count'=>null],
        ['icon'=>'fa-pen-nib',     'key'=>'organ_se', 'count'=>null],
      ];
      foreach ($organCards as $oc): ?>
      <div class="domain-card">
        <div class="domain-icon"><i class="fas <?= $oc['icon'] ?>" aria-hidden="true"></i></div>
        <h3><?= t($oc['key']) ?></h3>
        <?php if ($oc['count']): ?><p><?= $oc['count'] ?> membres</p><?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Leadership members -->
<?php if ($members): ?>
<section class="section" aria-labelledby="team-title">
  <div class="container">
    <div class="section-title">
      <h2 id="team-title"><?= t('members_title') ?></h2>
    </div>
    <?php foreach ($organs as $organ): 
      $group = array_filter($members, fn($m) => $m['organ'] === $organ);
      if (!$group) continue;
    ?>
    <h3 style="color:var(--primary);margin-bottom:1.5rem;padding-bottom:.5rem;border-bottom:3px solid var(--accent)">
      <?= t($organKeys[$organ]) ?>
    </h3>
    <div class="members-grid" style="margin-bottom:3rem">
      <?php foreach ($group as $m): ?>
      <div class="member-card">
        <?php if ($m['photo']): ?>
        <div class="member-photo"><img src="<?= BASE_URL ?>/uploads/<?= e($m['photo']) ?>" alt="<?= e($m['full_name']) ?>" loading="lazy"></div>
        <?php else: ?>
        <div class="member-photo-placeholder"><i class="fas fa-user" aria-hidden="true"></i></div>
        <?php endif; ?>
        <div class="member-info">
          <h3><?= e($m['full_name']) ?></h3>
          <span><?= e(loc($m,'position')) ?></span>
          <?php if (loc($m,'bio')): ?>
          <p style="font-size:.85rem;color:var(--gray);margin-top:.5rem"><?= e(truncate(loc($m,'bio'),100)) ?></p>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
