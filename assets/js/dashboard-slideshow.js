/**
 * Auto-slideshow Dashboard: Konten A (statistik) ↔ Konten B (sambutan).
 */
(function () {
    'use strict';

    var SLIDE_INTERVAL_MS = 12000;
    var FADE_DURATION_MS = 800;

    var slideshow = document.getElementById('dashboardSlideshow');
    if (!slideshow) {
        return;
    }

    var viewport = slideshow.querySelector('.dashboard-slideshow-viewport');
    var slides = slideshow.querySelectorAll('.dashboard-slide');
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

        var tabs = slideshow.querySelectorAll('.dashboard-slideshow-tab');
        tabs.forEach(function (tab, i) {
            tab.classList.toggle('is-active', i === index);
            tab.setAttribute('aria-selected', i === index ? 'true' : 'false');
        });

        setTimeout(setViewportHeight, FADE_DURATION_MS + 40);
    }

    function nextSlide() {
        showSlide((currentIndex + 1) % slides.length);
    }

    function startTimer() {
        if (timerId) {
            clearInterval(timerId);
        }
        timerId = setInterval(nextSlide, SLIDE_INTERVAL_MS);
    }

    slideshow.style.setProperty('--slide-fade-ms', FADE_DURATION_MS + 'ms');

    setViewportHeight();
    startTimer();

    window.addEventListener('resize', setViewportHeight);

    slideshow.querySelectorAll('.dashboard-slideshow-tab').forEach(function (tab) {
        tab.addEventListener('click', function () {
            var idx = parseInt(tab.getAttribute('data-slide-index'), 10);
            if (!isNaN(idx)) {
                showSlide(idx);
                startTimer();
            }
        });
    });
})();
