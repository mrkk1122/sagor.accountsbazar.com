/* ============================================================
   Sagor Photography — JavaScript
   ============================================================ */

(function () {
    'use strict';

    /* ---- Hamburger / Mobile Nav ---- */
    const hamburger = document.getElementById('hamburger');
    const mainNav   = document.getElementById('main-nav');

    if (hamburger && mainNav) {
        hamburger.addEventListener('click', () => {
            mainNav.classList.toggle('open');
            hamburger.setAttribute(
                'aria-expanded',
                mainNav.classList.contains('open') ? 'true' : 'false'
            );
        });

        // Close nav when a link is clicked
        mainNav.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => mainNav.classList.remove('open'));
        });
    }

    /* ---- Active nav link on scroll ---- */
    const sections = document.querySelectorAll('section[id], header[id]');
    const navLinks  = document.querySelectorAll('#main-nav a[href^="#"]');

    function setActiveLink() {
        let current = '';
        sections.forEach(sec => {
            if (window.scrollY >= sec.offsetTop - 90) {
                current = '#' + sec.id;
            }
        });
        navLinks.forEach(link => {
            link.classList.toggle('active', link.getAttribute('href') === current);
        });
    }

    window.addEventListener('scroll', setActiveLink, { passive: true });
    setActiveLink();

    /* ---- Back-to-top button ---- */
    const backTop = document.getElementById('back-top');
    if (backTop) {
        window.addEventListener('scroll', () => {
            backTop.classList.toggle('visible', window.scrollY > 400);
        }, { passive: true });

        backTop.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    /* ---- Animate sections on scroll (Intersection Observer) ---- */
    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.service-card, .day-card, .contact-card, .info-card, .time-info-card, .stat')
            .forEach(el => {
                el.classList.add('fade-in');
                observer.observe(el);
            });
    }

    /* ---- Date field: prevent selecting past dates ---- */
    const dateInput = document.getElementById('date');
    if (dateInput) {
        const today = new Date();
        const yyyy  = today.getFullYear();
        const mm    = String(today.getMonth() + 1).padStart(2, '0');
        const dd    = String(today.getDate()).padStart(2, '0');
        dateInput.setAttribute('min', `${yyyy}-${mm}-${dd}`);
    }

    /* ---- Time field: restrict to working hours 09:00 – 20:00 ---- */
    const timeInput = document.getElementById('time');
    if (timeInput) {
        timeInput.setAttribute('min', '09:00');
        timeInput.setAttribute('max', '20:00');
    }

    /* ---- Client-side form validation feedback ---- */
    const bookingForm = document.getElementById('booking-form');
    if (bookingForm) {
        bookingForm.addEventListener('submit', (e) => {
            const phone   = bookingForm.querySelector('#phone').value.trim();
            const pattern = /^01[3-9]\d{8}$/;
            if (!pattern.test(phone.replace(/\s/g, ''))) {
                e.preventDefault();
                alert('সঠিক বাংলাদেশি মোবাইল নাম্বার দিন। উদাহরণ: 01XXXXXXXXX');
                bookingForm.querySelector('#phone').focus();
            }
        });
    }

    /* ---- Smooth reveal: add CSS class ---- */
    const style = document.createElement('style');
    style.textContent = `
        .fade-in { opacity: 0; transform: translateY(24px); transition: opacity .55s ease, transform .55s ease; }
        .fade-in.visible { opacity: 1; transform: none; }
        #main-nav a.active { color: #d4af37; }
    `;
    document.head.appendChild(style);

})();
