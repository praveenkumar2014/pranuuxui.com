/* ============================================================
   PRAVEEN KUMAR K — MODERN GLASSY ENGINE (MULTI-PAGE)
   ============================================================ */

document.addEventListener('DOMContentLoaded', () => {
    // Initialize Lucide Icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
    
    // Initialize Theme
    initTheme();
    
    // Initialize Background Particles
    initParticles();
    
    // Initialize Scroll Reveals (Glassy Style)
    initScrollReveal();

    // Initialize Contact Form
    initContactForm();
});

// ── THEME ENGINE ──
function initTheme() {
    const btn = document.querySelector('.theme-control-btn');
    if (!btn) return;
    
    btn.addEventListener('click', () => {
        const body = document.body;
        const current = body.classList.contains('dark-theme') ? 'dark' : 'light';
        const next = current === 'dark' ? 'light' : 'dark';
        
        if (next === 'dark') {
            body.classList.add('dark-theme');
            body.classList.remove('light-theme');
            document.documentElement.setAttribute('data-theme', 'dark');
        } else {
            body.classList.add('light-theme');
            body.classList.remove('dark-theme');
            document.documentElement.setAttribute('data-theme', 'light');
        }
        
        // Save preference
        localStorage.setItem('praveen-theme', next);
    });
    
    // Load preference
    const saved = localStorage.getItem('praveen-theme');
    if (saved === 'light') {
        document.body.classList.add('light-theme');
        document.body.classList.remove('dark-theme');
        document.documentElement.setAttribute('data-theme', 'light');
    }
}

// ── PARTICLE ENGINE ──
function initParticles() {
    const container = document.getElementById('background-canvas');
    if (!container) return;
    
    const canvas = document.createElement('canvas');
    container.appendChild(canvas);
    const ctx = canvas.getContext('2d');
    
    let w, h, particles = [];
    
    function resize() {
        w = canvas.width = window.innerWidth;
        h = canvas.height = window.innerHeight;
    }
    
    window.addEventListener('resize', resize);
    resize();
    
    class Particle {
        constructor() {
            this.reset();
        }
        reset() {
            this.x = Math.random() * w;
            this.y = Math.random() * h;
            this.size = Math.random() * 2 + 0.5;
            this.vx = (Math.random() - 0.5) * 0.3;
            this.vy = (Math.random() - 0.5) * 0.3;
            this.life = Math.random() * 0.4 + 0.1;
        }
        update() {
            this.x += this.vx;
            this.y += this.vy;
            if (this.x < 0 || this.x > w || this.y < 0 || this.y > h) this.reset();
        }
        draw() {
            const isDark = document.body.classList.contains('dark-theme') || !document.body.classList.contains('light-theme');
            const color = isDark ? '71, 112, 255' : '59, 130, 246';
            ctx.fillStyle = `rgba(${color}, ${this.life})`;
            ctx.beginPath();
            ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
            ctx.fill();
        }
    }
    
    for (let i = 0; i < 60; i++) particles.push(new Particle());
    
    function animate() {
        ctx.clearRect(0, 0, w, h);
        particles.forEach(p => {
            p.update();
            p.draw();
        });
        requestAnimationFrame(animate);
    }
    animate();
}

// ── CONTACT FORM ENGINE ──
function initContactForm() {
    const form = document.querySelector('#contact-form') || document.querySelector('form');
    if (!form || !form.action.includes('mail.php')) return;
    
    form.addEventListener('submit', (e) => {
        e.preventDefault();
        const btn = form.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        btn.innerText = "Sending...";
        btn.disabled = true;
        
        const formData = new FormData(form);
        fetch('assets/mail.php', {
            method: 'POST',
            body: formData
        }).then(response => {
            showPopup("Thanks for reaching out! I will get back to you ASAP.");
            btn.innerHTML = originalText;
            btn.disabled = false;
            form.reset();
        }).catch(err => {
            showPopup("Oops! Something went wrong. Please try again.");
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
    });
}

function showPopup(message) {
    const modal = document.createElement('div');
    modal.className = 'glassy-modal';
    modal.innerHTML = `
        <div class="modal-content">
            <div class="modal-icon"><i data-lucide="check-circle"></i></div>
            <h3>Message Sent!</h3>
            <p>${message}</p>
            <button onclick="this.parentElement.parentElement.remove()">Great!</button>
        </div>
    `;
    document.body.appendChild(modal);
    lucide.createIcons();
    setTimeout(() => modal.classList.add('visible'), 10);
}

// ── SCROLL REVEAL (GLASSY) ──
function initScrollReveal() {
    const reveals = document.querySelectorAll('.card, .skill-card, .project-item, .section-title, .main-common-title, .skill-category');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, { threshold: 0.1 });
    
    reveals.forEach(el => {
        el.classList.add('reveal-up');
        observer.observe(el);
    });
}
