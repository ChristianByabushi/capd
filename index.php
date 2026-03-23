<?php
$pageTitle = 'CAPD ASBL — Paix, Développement & Jeunesse';
$pageDesc  = 'CAPD ASBL promeut la culture de la paix et du développement intégral à travers la jeunesse.';
require_once 'includes/header.php';

$slides   = fetchAll("SELECT * FROM hero_slides WHERE is_active=1 ORDER BY display_order ASC");
$stats    = fetchAll("SELECT * FROM stats ORDER BY display_order ASC");
$domains  = [
  ['icon'=>'fa-graduation-cap', 'key'=>'education'],
  ['icon'=>'fa-tools',          'key'=>'formations'],
  ['icon'=>'fa-venus-mars',     'key'=>'genre'],
  ['icon'=>'fa-lightbulb',      'key'=>'entrepreneuriat'],
  ['icon'=>'fa-hands-helping',  'key'=>'aide'],
  ['icon'=>'fa-dove',           'key'=>'paix'],
  ['icon'=>'fa-heartbeat',      'key'=>'sante'],
  ['icon'=>'fa-leaf',           'key'=>'environnement'],
];
$activities = fetchAll("SELECT * FROM activities WHERE is_featured=1 ORDER BY created_at DESC LIMIT 3");
$posts      = fetchAll("SELECT * FROM posts WHERE is_published=1 ORDER BY published_at DESC LIMIT 3");
$partners   = fetchAll("SELECT * FROM partners WHERE is_active=1 ORDER BY display_order ASC");
$feedbacks  = fetchAll("SELECT f.*, c.name_fr, c.name_en, c.name_sw FROM feedbacks f LEFT JOIN centres c ON f.centre_id=c.id WHERE f.is_approved=1 ORDER BY f.created_at DESC LIMIT 6");
?>

<!-- ===== HERO ===== -->
<section class="hero" aria-label="Bannière principale">
  <?php if ($slides): foreach ($slides as $s): ?>
  <div class="hero-slide" style="background-image:url('<?= BASE_URL ?>/uploads/<?= e($s['image']) ?>')">
    <div class="hero-overlay"></div>
  </div>
  <?php endforeach; else: ?>
  <div class="hero-slide active" style="background:linear-gradient(135deg,#1a6b3c,#124d2b)">
    <div class="hero-overlay"></div>
  </div>
  <?php endif; ?>

  <div class="hero-content container">
    <?php if ($slides): $s = $slides[0]; ?>
    <div class="hero-text">
      <h1><?= e(loc($s, 'title')) ?></h1>
      <p><?= e(loc($s, 'subtitle')) ?></p>
      <div class="hero-buttons">
        <?php if ($s['btn1_url']): ?>
        <a href="<?= e($s['btn1_url']) ?>" class="btn btn-accent"><?= e(loc($s, 'btn1_label')) ?></a>
        <?php endif; ?>
        <?php if ($s['btn2_url']): ?>
        <a href="<?= e($s['btn2_url']) ?>" class="btn btn-outline"><?= e(loc($s, 'btn2_label')) ?></a>
        <?php endif; ?>
      </div>
    </div>
    <?php else: ?>
    <div class="hero-text">
      <h1>Paix, Développement &amp; Jeunesse</h1>
      <p>CAPD ASBL promeut la culture de la paix et du développement intégral à travers une jeunesse consciente.</p>
      <div class="hero-buttons">
        <a href="about.php" class="btn btn-accent"><?= t('hero_btn_learn') ?></a>
        <a href="contact.php" class="btn btn-outline"><?= t('hero_btn_donate') ?></a>
      </div>
    </div>
    <?php endif; ?>
  </div>

  <?php if (count($slides) > 1): ?>
  <div class="hero-dots" role="tablist" aria-label="Slides">
    <?php foreach ($slides as $i => $s): ?>
    <button class="hero-dot <?= $i===0?'active':'' ?>" role="tab" aria-label="Slide <?= $i+1 ?>"></button>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</section>

<!-- ===== STATS ===== -->
<?php if ($stats): ?>
<section class="stats-bar" aria-label="<?= t('stats_title') ?>">
  <div class="container">
    <div class="stats-grid">
      <?php foreach ($stats as $stat): ?>
      <div class="stat-item">
        <i class="fas <?= e($stat['icon']) ?>" aria-hidden="true"></i>
        <span class="stat-number" data-target="<?= e($stat['value']) ?>">0</span>
        <div class="stat-label"><?= e(loc($stat, 'label')) ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ===== WHAT WE DO ===== -->
<section class="section" aria-labelledby="domains-title">
  <div class="container">
    <div class="section-title centered">
      <h2 id="domains-title"><?= t('domain_paix') ?></h2>
      <p>Nos domaines d'intervention pour un développement durable et une paix durable.</p>
    </div>
    <div class="domains-grid">
      <?php foreach ($domains as $d): ?>
      <div class="domain-card">
        <div class="domain-icon"><i class="fas <?= $d['icon'] ?>" aria-hidden="true"></i></div>
        <h3><?= t('domain_' . $d['key']) ?></h3>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ===== FEATURED ACTIVITIES ===== -->
<?php if ($activities): ?>
<section class="section section-alt" aria-labelledby="activities-title">
  <div class="container">
    <div class="section-title">
      <h2 id="activities-title"><?= t('activities_title') ?></h2>
    </div>
    <div class="cards-grid">
      <?php foreach ($activities as $act): ?>
      <article class="card">
        <div class="card-img">
          <?php if ($act['cover_image']): ?>
          <img src="<?= BASE_URL ?>/uploads/<?= e($act['cover_image']) ?>" alt="<?= e(loc($act,'title')) ?>" loading="lazy">
          <?php else: ?>
          <div style="height:220px;background:linear-gradient(135deg,var(--primary),var(--primary-light));display:flex;align-items:center;justify-content:center">
            <i class="fas fa-project-diagram" style="font-size:3rem;color:rgba(255,255,255,.4)"></i>
          </div>
          <?php endif; ?>
          <span class="card-badge"><?= e($act['status']) ?></span>
        </div>
        <div class="card-body">
          <div class="card-meta">
            <?php if ($act['date_start']): ?><span><i class="fas fa-calendar" aria-hidden="true"></i> <?= formatDate($act['date_start']) ?></span><?php endif; ?>
            <?php if ($act['department']): ?><span><i class="fas fa-tag" aria-hidden="true"></i> <?= e($act['department']) ?></span><?php endif; ?>
          </div>
          <h3><a href="activity.php?slug=<?= e($act['slug']) ?>"><?= e(loc($act,'title')) ?></a></h3>
          <p><?= e(truncate(loc($act,'description'), 120)) ?></p>
          <div class="card-footer-link">
            <a href="activity.php?slug=<?= e($act['slug']) ?>" class="btn btn-outline-primary btn-sm"><?= t('read_more') ?></a>
          </div>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
    <div class="text-center mt-3">
      <a href="activities.php" class="btn btn-primary"><?= t('view_all') ?></a>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ===== LATEST NEWS (dark MSF-style) ===== -->
<?php if ($posts): ?>
<section class="news-section" aria-labelledby="news-title">
  <div class="container">
    <div class="section-title" style="display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:2rem">
      <div>
        <h2 id="news-title" style="color:#fff;padding-bottom:1rem;position:relative">
          <?= t('blog_title') ?>
          <span style="position:absolute;bottom:0;left:0;width:50px;height:4px;background:var(--accent);display:block"></span>
        </h2>
      </div>
      <a href="blog.php" class="read-more-link" style="color:rgba(255,255,255,.6);white-space:nowrap;margin-bottom:.5rem"><?= t('view_all') ?> &rsaquo;</a>
    </div>

    <div class="news-layout">
      <!-- Featured (first post) -->
      <?php $featured = $posts[0]; ?>
      <a href="post.php?slug=<?= e($featured['slug']) ?>" class="news-featured">
        <?php if ($featured['cover_image']): ?>
        <img src="<?= BASE_URL ?>/uploads/<?= e($featured['cover_image']) ?>" alt="<?= e(loc($featured,'title')) ?>" loading="lazy">
        <?php else: ?>
        <div style="width:100%;height:100%;min-height:420px;background:linear-gradient(135deg,#1a6b3c,#124d2b)"></div>
        <?php endif; ?>
        <div class="news-featured-overlay"></div>
        <div class="news-featured-body">
          <div class="news-cat">
            <i class="fas fa-circle" style="font-size:.5rem"></i>
            <?= strtoupper(e($featured['category'])) ?>
          </div>
          <h3><?= e(loc($featured,'title')) ?></h3>
          <div class="news-date"><?= $featured['published_at'] ? formatDate($featured['published_at']) : '' ?></div>
        </div>
      </a>

      <!-- Sidebar posts -->
      <div class="news-sidebar">
        <?php foreach (array_slice($posts, 1) as $post): ?>
        <a href="post.php?slug=<?= e($post['slug']) ?>" class="news-sidebar-item">
          <?php if ($post['cover_image']): ?>
          <img src="<?= BASE_URL ?>/uploads/<?= e($post['cover_image']) ?>" class="news-sidebar-thumb" alt="<?= e(loc($post,'title')) ?>" loading="lazy">
          <?php else: ?>
          <div class="news-no-img"><i class="fas fa-newspaper" aria-hidden="true"></i></div>
          <?php endif; ?>
          <div class="news-sidebar-body">
            <div class="news-cat"><?= strtoupper(e($post['category'])) ?></div>
            <h4><?= e(loc($post,'title')) ?></h4>
            <div class="news-date"><?= $post['published_at'] ? formatDate($post['published_at']) : '' ?></div>
          </div>
        </a>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ===== HOW YOU CAN HELP ===== -->
<section class="section" aria-labelledby="help-title">
  <div class="container">
    <div class="section-title">
      <h2 id="help-title">Comment vous pouvez aider</h2>
      <p>Rejoignez notre mouvement pour la paix et le développement durable en RDC.</p>
    </div>
    <div class="help-grid">
      <div class="help-item">
        <div style="height:200px;background:linear-gradient(135deg,var(--primary),var(--primary-light));display:flex;align-items:center;justify-content:center;margin-bottom:1.25rem">
          <i class="fas fa-hands-helping" style="font-size:3.5rem;color:rgba(255,255,255,.5)"></i>
        </div>
        <h3>Devenir bénévole</h3>
        <p>Rejoignez notre équipe de volontaires et contribuez directement à nos programmes sur le terrain.</p>
        <a href="contact.php" class="read-more-link"><?= t('read_more') ?></a>
      </div>
      <div class="help-item">
        <div style="height:200px;background:linear-gradient(135deg,var(--accent),var(--accent-dark));display:flex;align-items:center;justify-content:center;margin-bottom:1.25rem">
          <i class="fas fa-calendar-check" style="font-size:3.5rem;color:rgba(255,255,255,.5)"></i>
        </div>
        <h3>Participer à nos activités</h3>
        <p>Nous organisons régulièrement des événements communautaires ouverts à tous.</p>
        <a href="activities.php" class="read-more-link"><?= t('read_more') ?></a>
      </div>
      <div class="help-item">
        <div style="height:200px;background:linear-gradient(135deg,#2a9d5c,#1a6b3c);display:flex;align-items:center;justify-content:center;margin-bottom:1.25rem">
          <i class="fas fa-donate" style="font-size:3.5rem;color:rgba(255,255,255,.5)"></i>
        </div>
        <h3>Faire un don</h3>
        <p>Votre soutien financier nous permet de maintenir nos programmes et d'en lancer de nouveaux.</p>
        <a href="contact.php#donate" class="read-more-link"><?= t('read_more') ?></a>
      </div>
    </div>
    <div style="margin-top:2.5rem">
      <a href="donate.php" class="btn btn-outline-dark">S'IMPLIQUER</a>
    </div>
  </div>
</section>

<!-- ===== TESTIMONIALS ===== -->
<?php if ($feedbacks): ?>
<section class="section section-alt" aria-labelledby="feedbacks-title">
  <div class="container">
    <div class="section-title">
      <h2 id="feedbacks-title"><?= t('feedbacks_title') ?></h2>
    </div>
    <div class="testimonials-grid">
      <?php foreach ($feedbacks as $fb): ?>
      <div class="testimonial-card">
        <div class="stars" aria-label="<?= $fb['rating'] ?> étoiles">
          <?php for ($i=0;$i<5;$i++): ?><i class="fas fa-star<?= $i>=$fb['rating']?'-o':'' ?>" aria-hidden="true"></i><?php endfor; ?>
        </div>
        <p>"<?= e($fb['feedback_text']) ?>"</p>
        <div class="testimonial-author">
          — <?= e($fb['learner_name']) ?>
          <?php if ($fb['name_fr']): ?><span style="color:var(--gray);font-weight:400"> · <?= e(loc($fb,'name')) ?></span><?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ===== PARTNERS ===== -->
<?php if ($partners): ?>
<section class="section" aria-labelledby="partners-title">
  <div class="container">
    <div class="section-title">
      <h2 id="partners-title"><?= t('partners_title') ?></h2>
    </div>
    <div class="partners-strip">
      <?php foreach ($partners as $p): ?>
      <a href="<?= e($p['website'] ?: '#') ?>" target="_blank" rel="noopener" class="partner-item" title="<?= e($p['name']) ?>">
        <?php if ($p['logo']): ?>
        <img src="<?= BASE_URL ?>/uploads/<?= e($p['logo']) ?>" alt="<?= e($p['name']) ?>" loading="lazy">
        <?php else: ?>
        <span style="font-weight:700;color:var(--gray)"><?= e($p['name']) ?></span>
        <?php endif; ?>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
