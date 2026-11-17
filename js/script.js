'use strict';
const $ = (s, r = document) => r.querySelector(s);
const $$ = (s, r = document) => [...r.querySelectorAll(s)];

function initThemeToggle() {
  $$('[data-theme-toggle]').forEach(btn => btn.addEventListener('click', () => {
    const next = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
    document.documentElement.setAttribute('data-theme', next);
    try { localStorage.setItem('theme', next); } catch (e) {}
  }));
}
function initMobileNav() {
  const btn = $('[data-hamburger]'), drawer = $('[data-mobile-drawer]');
  if (!btn || !drawer) return;
  btn.addEventListener('click', () => {
    const open = drawer.classList.toggle('open');
    btn.setAttribute('aria-expanded', String(open));
  });
}
function initScrollTopButton() {
  const b = $('[data-scroll-top]'); if (!b) return;
  addEventListener('scroll', () => b.classList.toggle('show', scrollY > 400), { passive: true });
  b.addEventListener('click', () => scrollTo({ top: 0, behavior: 'smooth' }));
}
function initTypingEffect() {
  const el = $('[data-typing]'); if (!el) return;
  let words; try { words = JSON.parse(el.dataset.typing); } catch (e) { return; }
  let w = 0, i = 0, del = false;
  (function tick() {
    const word = words[w];
    el.textContent = word.slice(0, i);
    if (!del && i === word.length) { del = true; return setTimeout(tick, 1500); }
    if (del && i === 0) { del = false; w = (w + 1) % words.length; }
    i += del ? -1 : 1;
    setTimeout(tick, del ? 50 : 100);
  })();
}
function initSkillBars() {
  const bars = $$('[data-skill-level]'); if (!bars.length) return;
  const io = new IntersectionObserver(es => es.forEach(en => {
    if (en.isIntersecting) { en.target.style.width = en.target.dataset.skillLevel + '%'; io.unobserve(en.target); }
  }), { threshold: 0.3 });
  bars.forEach(b => io.observe(b));
}
function initAccordion() {
  $$('[data-accordion-item]').forEach(item => {
    const t = $('[data-accordion-trigger]', item), p = $('[data-accordion-panel]', item);
    t.addEventListener('click', () => {
      const open = item.classList.toggle('open');
      t.setAttribute('aria-expanded', String(open));
      $('.accordion-icon', t).textContent = open ? '–' : '+';
      p.style.maxHeight = open ? p.scrollHeight + 'px' : null;
    });
  });
}
function initProjectFilter() {
  const btns = $$('[data-filter]'); if (!btns.length) return;
  btns.forEach(b => b.addEventListener('click', () => {
    btns.forEach(x => x.classList.toggle('is-active', x === b));
    $$('[data-project-tags]').forEach(c => {
      c.hidden = !(b.dataset.filter === 'all' || c.dataset.projectTags === b.dataset.filter);
    });
  }));
}
function initImageModal() {
  const ov = $('[data-modal-overlay]'); if (!ov) return;
  const img = $('[data-modal-image]', ov), cap = $('[data-modal-caption]', ov);
  const close = () => ov.classList.remove('open');
  $$('[data-modal-trigger]').forEach(t => t.addEventListener('click', () => {
    const src = $('img', t); if (!src) return;
    img.src = src.src; img.alt = src.alt; cap.textContent = t.dataset.caption || '';
    ov.classList.add('open');
  }));
  $('[data-modal-close]', ov).addEventListener('click', close);
  ov.addEventListener('click', e => { if (e.target === ov) close(); });
  addEventListener('keydown', e => { if (e.key === 'Escape') close(); });
}
function initContactForm() {
  const form = $('[data-contact-form]'); if (!form) return;
  const msg = $('#message'), count = $('[data-char-count]');
  const rules = {
    name: v => v.length >= 2 || 'Enter your name (2+ characters).',
    email: v => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v) || 'Enter a valid email address.',
    subject: v => v.length >= 3 || 'Subject must be at least 3 characters.',
    message: v => v.length >= 20 || 'Message must be at least 20 characters.'
  };
  const check = f => {
    const msgText = rules[f.name](f.value.trim());
    f.parentElement.querySelector('.field-error').textContent = msgText === true ? '' : msgText;
    return msgText === true;
  };
  Object.keys(rules).forEach(n => form.elements[n].addEventListener('blur', e => check(e.target)));
  msg.addEventListener('input', () => count.textContent = msg.value.length + ' characters');
  count.textContent = msg.value.length + ' characters';
  // Client checks are only for convenience; the server re-validates everything.
  form.addEventListener('submit', e => {
    const ok = Object.keys(rules).map(n => check(form.elements[n])).every(Boolean);
    if (!ok) e.preventDefault();
  });
}
function initConfirm() {
  document.addEventListener('click', e => {
    const b = e.target.closest('[data-confirm]');
    if (b && !confirm(b.dataset.confirm)) e.preventDefault();
  });
}
[initThemeToggle, initMobileNav, initScrollTopButton, initTypingEffect, initSkillBars,
 initAccordion, initProjectFilter, initImageModal, initContactForm, initConfirm].forEach(f => f());
