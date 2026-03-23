// CAPD ASBL — Main JS

document.addEventListener('DOMContentLoaded', () => {

  // ---- Mobile drawer ----
  const menuBtn    = document.getElementById('mobileMenuBtn');
  const drawer     = document.getElementById('mobileDrawer');
  const closeBtn   = document.getElementById('mobileMenuClose');
  const backdrop   = document.getElementById('drawerBackdrop');

  function openDrawer() {
    drawer?.classList.add('open');
    backdrop?.classList.add('visible');
    document.body.style.overflow = 'hidden';
    menuBtn?.setAttribute('aria-expanded', 'true');
    drawer?.setAttribute('aria-hidden', 'false');
  }

  function closeDrawer() {
    drawer?.classList.remove('open');
    backdrop?.classList.remove('visible');
    document.body.style.overflow = '';
    menuBtn?.setAttribute('aria-expanded', 'false');
    drawer?.setAttribute('aria-hidden', 'true');
  }

  menuBtn?.addEventListener('click', openDrawer);
  closeBtn?.addEventListener('click', closeDrawer);
  backdrop?.addEventListener('click', closeDrawer);

  // Close on Escape
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeDrawer();
  });

  // ---- Mobile accordion ----
  document.querySelectorAll('.mobile-nav-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const item = btn.closest('.mobile-nav-item');
      const isOpen = item.classList.contains('open');
      // Close all
      document.querySelectorAll('.mobile-nav-item').forEach(i => i.classList.remove('open'));
      // Toggle clicked
      if (!isOpen) {
        item.classList.add('open');
        btn.setAttribute('aria-expanded', 'true');
      } else {
        btn.setAttribute('aria-expanded', 'false');
      }
    });
  });

  // ---- Hero Slider ----
  const slides = document.querySelectorAll('.hero-slide');
  const dots   = document.querySelectorAll('.hero-dot');
  let current  = 0, timer;

  function goTo(n) {
    slides[current]?.classList.remove('active');
    dots[current]?.classList.remove('active');
    current = (n + slides.length) % slides.length;
    slides[current]?.classList.add('active');
    dots[current]?.classList.add('active');
  }

  function autoPlay() {
    timer = setInterval(() => goTo(current + 1), 5000);
  }

  if (slides.length > 0) {
    goTo(0);
    autoPlay();
    dots.forEach((dot, i) => dot.addEventListener('click', () => {
      clearInterval(timer); goTo(i); autoPlay();
    }));
  }

  // ---- Counter animation ----
  const counters = document.querySelectorAll('.stat-number[data-target]');
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (!entry.isIntersecting) return;
      const el = entry.target;
      const raw = el.dataset.target;
      const num = parseInt(raw.replace(/\D/g, ''));
      const suffix = raw.replace(/[\d]/g, '');
      let start = 0;
      const step = Math.ceil(num / 60);
      const interval = setInterval(() => {
        start = Math.min(start + step, num);
        el.textContent = start.toLocaleString() + suffix;
        if (start >= num) clearInterval(interval);
      }, 30);
      observer.unobserve(el);
    });
  }, { threshold: 0.5 });
  counters.forEach(c => observer.observe(c));

  // ---- Activity / Blog filter ----
  document.querySelectorAll('.filter-btn[data-filter]').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      const filter = btn.dataset.filter;
      document.querySelectorAll('.filterable').forEach(item => {
        item.style.display = (filter === 'all' || item.dataset.category === filter) ? '' : 'none';
      });
    });
  });

  // ---- Alert auto-dismiss ----
  document.querySelectorAll('.alert').forEach(alert => {
    setTimeout(() => { alert.style.transition = 'opacity .5s'; alert.style.opacity = '0'; }, 4000);
    setTimeout(() => alert.remove(), 4600);
  });

  // ---- Quick amount buttons (donate page) ----
  document.querySelectorAll('.quick-amt-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('.quick-amt-btn').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
    });
  });

});
