/**
 * Slideshow halaman login: Info SPMB (alur + jadwal) ↔ Sambutan Kepala Sekolah.
 */
(function () {
    'use strict';
    try {
        console.log('[login-slideshow] init');

        var FADE_DURATION_MS = 800;

        var slideshow = document.getElementById('loginSlideshow');
        if (!slideshow) {
            console.log('[login-slideshow] no slideshow element');
            return;
        }

    var viewport = slideshow.querySelector('.login-slideshow-viewport');
    var slides = slideshow.querySelectorAll('.login-slide');
    if (!viewport || slides.length < 2) {
        return;
    }

    var currentIndex = 0;
    var timerId = null;

    function setViewportHeight() {
        var maxHeight = 0;
        var savedIndex = currentIndex;

        slides.forEach(function (slide) {
            slide.classList.remove('is-active');
            slide.classList.add('is-measuring');
        });

        slides.forEach(function (slide) {
            maxHeight = Math.max(maxHeight, slide.offsetHeight);
        });

        slides.forEach(function (slide) {
            slide.classList.remove('is-measuring');
        });

        slides.forEach(function (slide, i) {
            var active = i === savedIndex;
            slide.classList.toggle('is-active', active);
            slide.setAttribute('aria-hidden', active ? 'false' : 'true');
        });

        if (maxHeight > 0) {
            viewport.style.minHeight = maxHeight + 'px';
        }
    }

    function showSlide(index) {
        currentIndex = index;

        slides.forEach(function (slide, i) {
            var active = i === index;
            slide.classList.toggle('is-active', active);
            slide.setAttribute('aria-hidden', active ? 'false' : 'true');
        });

        var tabs = slideshow.querySelectorAll('.login-slideshow-tab');
        tabs.forEach(function (tab, i) {
            tab.classList.toggle('is-active', i === index);
            tab.setAttribute('aria-selected', i === index ? 'true' : 'false');
        });

        setTimeout(setViewportHeight, FADE_DURATION_MS + 40);
    }

    // Auto-rotation disabled by request; slides change only when user clicks tabs

    slideshow.style.setProperty('--login-slide-fade-ms', FADE_DURATION_MS + 'ms');

    setViewportHeight();

    window.addEventListener('resize', setViewportHeight);

    slideshow.querySelectorAll('.login-slideshow-tab').forEach(function (tab) {
        tab.addEventListener('click', function () {
            var idx = parseInt(tab.getAttribute('data-slide-index'), 10);
            if (!isNaN(idx)) {
                showSlide(idx);
            }
        });
    });
    } catch (err) {
        console.error('[login-slideshow] error', err);
    }
})();
