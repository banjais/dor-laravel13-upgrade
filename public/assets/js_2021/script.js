jQuery(document).ready(function($) {
    'use strict';
    // main elements
    var $win = $(window);

    // sticky header setup
    $win.scroll(function() {
        if ($(this).scrollTop() > 30) {
            $('.main-header-wrap').addClass("sticky-nav");
        } else {
            $('.main-header-wrap').removeClass("sticky-nav");
        }
    });
    //news ticker
    $('.news-ticker-list').AcmeTicker({
        type: 'marquee',
        direction: 'left',
        speed: 0.025,
        pauseOnHover: true,
        pauseOnFocus: true
    });

    //single slider - home page slider
    $('.slider-img-holder').each(function(elm) {
        var img = document.createElement("img");
        img.src = $(this).data('image');
        $(this).html(img);
    });
    $('#single-slider').slick({
        dots: true,
        infinite: true,
        speed: 500,
        slidesToShow: 1,
        adaptiveHeight: false,
        arrows: true,
        autoplay: true,
        autoplaySpeed: 5000,
        pauseOnHover: true,
        pauseOnFocus: true,
        pauseOnDotsHover: true,
        swipe: true,
        touchMove: true,
        touchThreshold: 8,
        accessibility: true,
    });
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Flash Box slider: controlled by Flash Box Settings
    const flashBoxSlider = $('#single-slider-live');

    if (flashBoxSlider.length) {
        const refreshFlashBoxSlider = function () {
            const slider = $('#single-slider-live');

            if (!slider.length) {
                return;
            }

            const animationEnabled = slider.attr('data-animation-enabled') === '1';
            const mobileAnimationEnabled = slider.attr('data-mobile-animation-enabled') === '1';
            const isMobile = window.matchMedia('(max-width: 767px)').matches;

            const slideCount = slider.hasClass('slick-initialized')
                ? slider.slick('getSlick').slideCount
                : slider.children().length;

            const shouldAnimate = animationEnabled &&
                slideCount > 1 &&
                (!isMobile || mobileAnimationEnabled);

            if (!shouldAnimate) {
                if (slider.hasClass('slick-initialized')) {
                    slider.slick('unslick');
                }
                return;
            }

            if (slider.hasClass('slick-initialized')) {
                return;
            }

            slider.slick({
                dots: slider.attr('data-dots-enabled') === '1',
                infinite: true,
                speed: 300,
                slidesToShow: 1,
                adaptiveHeight: false,
                arrows: slider.attr('data-arrows-enabled') === '1',
                autoplay: slider.attr('data-autoplay-enabled') === '1',
                autoplaySpeed: parseInt(slider.attr('data-autoplay-speed'), 10) || 4000,
                pauseOnHover: slider.attr('data-pause-on-hover') === '1',
                pauseOnFocus: true,
                fade: slider.attr('data-effect') === 'fade',
            });
        };

        refreshFlashBoxSlider();

        let flashBoxResizeTimer;
        $(window).on('resize', function () {
            clearTimeout(flashBoxResizeTimer);
            flashBoxResizeTimer = setTimeout(refreshFlashBoxSlider, 200);
        });
    }
});