/* ============================================================
   PRAVEEN KUMAR K — PORTFOLIO ANIMATIONS ENGINE
   Smoke particles + GSAP scroll reveals + micro-interactions
   ============================================================ */

(function () {
  'use strict';

  /* ── 1. SMOKE / PARTICLE BACKGROUND ─────────────────────── */
  function initParticles() {
    var canvas = document.createElement('canvas');
    canvas.id = 'particles-canvas';
    document.body.insertBefore(canvas, document.body.firstChild);

    var ctx = canvas.getContext('2d');
    var W, H, particles = [];
    var isDark = document.body.classList.contains('dark-theme') ||
                 document.documentElement.getAttribute('data-theme') === 'dark';

    function resize() {
      W = canvas.width  = window.innerWidth;
      H = canvas.height = window.innerHeight;
    }
    resize();
    window.addEventListener('resize', resize);

    function Particle() {
      this.reset();
    }
    Particle.prototype.reset = function () {
      this.x    = Math.random() * W;
      this.y    = H + Math.random() * 100;
      this.r    = Math.random() * 60 + 20;
      this.vx   = (Math.random() - 0.5) * 0.4;
      this.vy   = -(Math.random() * 0.4 + 0.15);
      this.life = 0;
      this.maxL = Math.random() * 220 + 180;
      this.hue  = Math.random() > 0.5 ? 230 : 270; // blue or purple
    };

    for (var i = 0; i < 28; i++) {
      var p = new Particle();
      p.y = Math.random() * H; // spread initial positions
      p.life = Math.random() * p.maxL;
      particles.push(p);
    }

    function draw() {
      ctx.clearRect(0, 0, W, H);
      particles.forEach(function (p) {
        p.x   += p.vx;
        p.y   += p.vy;
        p.life += 1;
        p.r   += 0.08;

        var progress = p.life / p.maxL;
        var alpha    = Math.sin(Math.PI * progress) * 0.07;

        var grad = ctx.createRadialGradient(p.x, p.y, 0, p.x, p.y, p.r);
        grad.addColorStop(0, 'hsla(' + p.hue + ', 70%, 65%, ' + alpha + ')');
        grad.addColorStop(1, 'hsla(' + p.hue + ', 70%, 65%, 0)');

        ctx.beginPath();
        ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
        ctx.fillStyle = grad;
        ctx.fill();

        if (p.life >= p.maxL) p.reset();
      });
      requestAnimationFrame(draw);
    }
    draw();
  }

  /* ── 2. CURSOR GLOW ──────────────────────────────────────── */
  function initCursorGlow() {
    if (window.innerWidth < 992) return; // desktop only
    var glow = document.createElement('div');
    glow.id = 'cursor-glow';
    document.body.appendChild(glow);
    document.addEventListener('mousemove', function (e) {
      glow.style.left = e.clientX + 'px';
      glow.style.top  = e.clientY + 'px';
    });
  }

  /* ── 3. SCROLL REVEAL ────────────────────────────────────── */
  function initScrollReveal() {
    var targets = document.querySelectorAll(
      '.card, .expertise-item, .services-item, .work-experiance-main li, ' +
      '.project-item, .stat-item, .accordion-item, .skill-category, .skill-card'
    );

    targets.forEach(function (el, i) {
      el.classList.add('reveal-up');
      var staggerIdx = (i % 8) + 1;
      el.classList.add('stagger-' + staggerIdx);
    });

    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

    targets.forEach(function (el) { observer.observe(el); });

    // Also reveal .card on page load immediately (above fold)
    setTimeout(function () {
      document.querySelectorAll('.card, .profile-card').forEach(function (el) {
        el.classList.add('visible');
      });
    }, 100);
  }

  /* ── 4. COUNTER ANIMATION ────────────────────────────────── */
  function animateCounter(el, target, duration) {
    var start   = 0;
    var startTs = null;
    function step(ts) {
      if (!startTs) startTs = ts;
      var progress = Math.min((ts - startTs) / duration, 1);
      var eased    = 1 - Math.pow(1 - progress, 3); // ease out cubic
      el.textContent = Math.floor(eased * target) + (el.dataset.suffix || '');
      if (progress < 1) requestAnimationFrame(step);
    }
    requestAnimationFrame(step);
  }

  function initCounters() {
    var counters = document.querySelectorAll('[data-counter]');
    if (!counters.length) return;

    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          var el     = entry.target;
          var target = parseInt(el.dataset.counter, 10);
          animateCounter(el, target, 1800);
          observer.unobserve(el);
        }
      });
    }, { threshold: 0.5 });

    counters.forEach(function (el) { observer.observe(el); });
  }

  /* ── 5. BOOKING SLOT PICKER ──────────────────────────────── */
  function initBookingSlots() {
    var slots = document.querySelectorAll('.slot-btn');
    slots.forEach(function (btn) {
      btn.addEventListener('click', function () {
        slots.forEach(function (b) { b.classList.remove('selected'); });
        btn.classList.add('selected');
      });
    });

    var confirmBtn = document.querySelector('.slot-confirm-btn');
    if (!confirmBtn) return;
    confirmBtn.addEventListener('click', function () {
      var selected = document.querySelector('.slot-btn.selected');
      if (!selected) {
        alert('Please select a time slot first.');
        return;
      }
      var slot = selected.textContent;
      var subject = encodeURIComponent('Booking Request — ' + slot);
      var body    = encodeURIComponent(
        'Hi Praveen,\n\nI would like to book a call on: ' + slot +
        '\n\nPlease confirm the appointment.\n\nBest regards,'
      );
      window.location.href =
        'mailto:praveenkumar.kanneganti@gmail.com?subject=' + subject + '&body=' + body;
    });
  }

  /* ── 6. CONTACT FORM ENHANCED SUBMIT ─────────────────────── */
  function initFormSubmit() {
    var form = document.getElementById('contact-form');
    if (!form) return;

    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var btn = form.querySelector('.submit-btn');
      if (btn) {
        btn.textContent = 'Sending…';
        btn.disabled    = true;
      }

      var data = new FormData(form);
      fetch(form.action, { method: 'POST', body: data })
        .then(function (res) { return res.json(); })
        .then(function (payload) {
          if (payload && payload.success) {
            showFormSuccess(form, payload.success);
          } else {
            var msg = (payload && payload.error) ? payload.error : 'Sorry, something went wrong. Please try again.';
            showFormError(form, msg);
          }
        })
        .catch(function (err) {
          console.error('Contact submit failed:', err);
          showFormError(form, 'Network error. Please try again in a moment.');
        });
    });
  }

  function showFormSuccess(form, message) {
    if (window.UXLightbox && typeof window.UXLightbox.show === 'function') {
      window.UXLightbox.show({
        title: 'Successfully sent',
        message: message || "Thanks — I’ll get back to you ASAP.",
        variant: 'success'
      });
    }
    form.reset();
    var btn = form.querySelector('.submit-btn');
    if (btn) {
      btn.textContent = 'Send Message';
      btn.disabled = false;
    }
  }

  function showFormError(form, message) {
    if (window.UXLightbox && typeof window.UXLightbox.show === 'function') {
      window.UXLightbox.show({
        title: 'Could not send',
        message: message || 'Please try again.',
        variant: 'error'
      });
    } else {
      alert(message || 'Please try again.');
    }
    var btn = form.querySelector('.submit-btn');
    if (btn) {
      btn.textContent = 'Send Message';
      btn.disabled = false;
    }
  }

  /* ── 7. HEADER SCROLL EFFECT ─────────────────────────────── */
  function initHeaderScroll() {
    var header = document.querySelector('.header-area');
    if (!header) return;
    var scrolled = false;
    window.addEventListener('scroll', function () {
      var now = window.scrollY > 30;
      if (now !== scrolled) {
        scrolled = now;
        header.style.backdropFilter = scrolled ? 'blur(16px)' : '';
        header.style.boxShadow      = scrolled
          ? '0 2px 24px rgba(0,0,0,0.08)'
          : '';
      }
    }, { passive: true });
  }

  /* ── 8. ACTIVE NAV HIGHLIGHT ─────────────────────────────── */
  function initActiveNav() {
    var path = (window.location.pathname || '/').replace(/\/+$/, '');
    var parts = path.split('/').filter(Boolean); // ["admin","dashboard"] etc.
    var page = (parts[parts.length - 1] || 'index').replace('.php', '').replace('.html', '');
    var section = parts[0] || 'index';

    document.querySelectorAll('.nav-link').forEach(function (a) {
      var rawHref = (a.getAttribute('href') || '').replace(/\/+$/, '');
      var href = rawHref.replace('.php', '').replace('.html', '');

      // Clean home handling
      if ((page === '' || page === 'index') && href === 'index') {
        a.classList.add('active');
        return;
      }

      // Admin section should keep Admin highlighted for any /admin/*
      if (section === 'admin' && (href === 'admin' || href.indexOf('admin/') === 0)) {
        a.classList.add('active');
        return;
      }

      if (href === page) {
        a.classList.add('active');
      }
    });
  }

  /* ── BACK TO TOP ────────────────────────────────────────── */
  function initBackToTop() {
    const btn = document.getElementById('back-to-top');
    if (!btn) return;

    window.addEventListener('scroll', function () {
      if (window.scrollY > 500) {
        btn.classList.add('show');
      } else {
        btn.classList.remove('show');
      }
    });

    btn.addEventListener('click', function () {
      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      });
    });
  }


  /* ── PORTFOLIO FILTER ────────────────────────────────────── */
  function initPortfolioFilter() {
    const filterBtns = document.querySelectorAll('.filter-btn');
    const items = document.querySelectorAll('.portfolio-item-wrapper');

    if (!filterBtns.length || !items.length) return;

    filterBtns.forEach(btn => {
      btn.addEventListener('click', function () {
        filterBtns.forEach(b => b.classList.remove('active'));
        this.classList.add('active');

        const filter = this.getAttribute('data-filter');

        items.forEach(item => {
          if (filter === 'all') {
            item.style.display = 'block';
            item.style.opacity = '1';
          } else {
            const text = item.innerText.toLowerCase();
            let show = false;
            if (filter === 'ux-ui' && (text.includes('ui') || text.includes('ux'))) show = true;
            if (filter === 'product' && text.includes('product')) show = true;
            if (filter === 'mobile' && (text.includes('mobile') || text.includes('app'))) show = true;
            if (filter === 'research' && (text.includes('research') || text.includes('testing'))) show = true;

            if (show) {
              item.style.display = 'block';
              item.style.opacity = '1';
            } else {
              item.style.display = 'none';
              item.style.opacity = '0';
            }
          }
        });
      });
    });
  }

  /* ── INIT ALL ────────────────────────────────────────────── */
  document.addEventListener('DOMContentLoaded', function () {
    initParticles();
    initCursorGlow();
    initScrollReveal();
    initCounters();
    initBookingSlots();
    initFormSubmit();
    initHeaderScroll();
    initActiveNav();
    initBackToTop();
    initPortfolioFilter();

    // Expose for inline use
    window.PraveenAnimations = {
      showFormSuccess: showFormSuccess,
      showFormError: showFormError
    };
  });

})();

/* ── SKILLS LIGHTBOX ─────────────────────────────────────── */
(function() {
  document.addEventListener('DOMContentLoaded', function() {
    // Create lightbox element
    var lb = document.createElement('div');
    lb.id = 'skill-lightbox';
    lb.className = 'skill-lightbox';
    lb.innerHTML = '<button class="skill-lightbox-close" id="lb-close">&times;</button><img id="lb-img" src="" alt=""><h3 id="lb-name"></h3>';
    document.body.appendChild(lb);

    document.getElementById('lb-close').addEventListener('click', function() {
      lb.classList.remove('active');
    });
    lb.addEventListener('click', function(e) {
      if (e.target === lb) lb.classList.remove('active');
    });
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') lb.classList.remove('active');
    });

    // Attach click to skill cards
    document.querySelectorAll('.skill-card').forEach(function(card) {
      card.addEventListener('click', function() {
        var img = card.querySelector('img');
        var name = card.querySelector('span') ? card.querySelector('span').textContent : '';
        if (img) {
          document.getElementById('lb-img').src = img.src;
          document.getElementById('lb-img').alt = img.alt;
        } else {
          document.getElementById('lb-img').src = '';
        }
        document.getElementById('lb-name').textContent = name;
        lb.classList.add('active');
      });
    });

    // Lazy load IntersectionObserver for images not yet loaded
    if ('IntersectionObserver' in window) {
      var lazyImgs = document.querySelectorAll('img[loading="lazy"]');
      var imgObs = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
          if (entry.isIntersecting) {
            var img = entry.target;
            if (img.dataset.src) { img.src = img.dataset.src; }
            imgObs.unobserve(img);
          }
        });
      }, { rootMargin: '200px' });
      lazyImgs.forEach(function(img) { imgObs.observe(img); });
    }
  });
})();
