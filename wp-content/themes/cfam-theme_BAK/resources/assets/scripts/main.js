// Import our styles
import '../styles/main.css';

// Expose jQuery to window
var $ = jQuery;
window.$ = window.JQuery = $;

// Require plugins here
import LazyLoad from 'vanilla-lazyload';
const { detect } = require('detect-browser');
const browser = detect();

// Polyfills
require('es6-promise').polyfill();
var objectFitImages = require('object-fit-images');
objectFitImages('img:not(.lazy)');

// Dynamic code splitting
dynamicCodeSplit();

// Init dynamic code split
$(document).on('ready', function () {
    // Check to see if user is tabbing
    var keys_map = [9, 37, 39];

    function handleFirstTab(e) {
        if (keys_map.includes(e.keyCode)) {
            // the "I am a keyboard user" key
            document.body.classList.add('user-is-tabbing');
            window.removeEventListener('keydown', handleFirstTab);
        }
    }
    window.addEventListener('keydown', handleFirstTab);

    // Add browser name to body
    $('body').addClass(`browser-${browser.name}`);

    // Object fit images
    objectFitImages('img:not(.lazy)');

    // Lazy load images
    if (elementExist($('.lazy-container'))) {
        var myLazyLoad = new LazyLoad({
            elements_selector: '.lazy',
            callback_loaded: lazyLoaded,
        });
    }
});

/**
 * Webpack will bundle any module that gets imported in this function
 * in /dist/js/ directory. Add any functionality that is page specific here
 * to reduce the size of the main js file.
 */
function dynamicCodeSplit() {
    if (elementExist($('.sample'))) {
        import( /* webpackChunkName: "sample" */ './modules/sample').then((sample) => {
            sample.init();
        });
    }
    if (browser.name == 'ie') {
        import( /* webpackChunkName: "ie" */ './modules/ie').then((ie) => {
            ie.init();
        });
    }

    /* ACF Blocks */
    if (elementExist($('.team-grid'))) {
        import('./blocks/team-grid').then((teamGrid) => {
            teamGrid.init();
        });
    }

    if (elementExist($('.gallery'))) {
        import('./blocks/gallery-grid').then((gallery) => {
            gallery.init();
        });
    }

    if (elementExist($('.tab-post-list'))) {
        import('./blocks/tab-post-list').then((tabPostList) => {
            tabPostList.init();
        });
    }


    if (elementExist($('.doughnut-chart'))) {
        import('./blocks/doughnut-chart').then((doughnutChart) => {
            doughnutChart.init();
        });
    }

    if (elementExist($('.chart-container'))) {
        import('./blocks/bar-chart').then((barChart) => {
            barChart.init();
        });
    }

    if (elementExist($('.line-chart-container'))) {
        import('./blocks/line-chart').then((lineChart) => {
            lineChart.init();
        });
    }

    if (elementExist($('.tooltip'))) {
        import('./components/tooltip').then((tooltip) => {
            tooltip.init();
        });
    }

    if (elementExist($('.post-grid'))) {
        import('./blocks/post-grid').then((postGrid) => {
            postGrid.init();
        });
    }

    if (elementExist($('.performance-metrics'))) {
        import('./blocks/performance-metrics').then((performanceMetrics) => {
            performanceMetrics.init();
        });
    }

    if (elementExist($('.nav-section'))) {
        import('./blocks/nav').then((nav) => {
            nav.init();
        });
    }

    if (elementExist($('body.page-performance'))) {
        import('./pages/performance').then((performancePage) => {
            performancePage.init();
        });
    }
}

function elementExist(elementObject) {
    if (elementObject && elementObject.length) {
        return true;
    }
    return false;
}


// Custom scripts
if (!elementExist($('.tab-post-list'))) {
    $(window).scroll(function () {
        $('.site-header').toggleClass('scrolled', $(this).scrollTop() > 100);
    });

}


$(document).ready(function () {
    $('.dropdown .dropdown-trigger').click(function () {
        $(this).next('.dropdown-content').toggleClass('open');
    });

    $('nav.mainnav .toggle-menu').click(function () {
        $(this).toggleClass('open');
        $('.mainnav').toggleClass('open-menu');
        $('.mainnav-menu').slideToggle();
        $('.mainnav-menu ul ul').css('display', 'none');
    });

    $('.mainnav-menu ul li').click(function () {
        $('.mainnav-menu ul ul').slideUp();
        $(this).find('ul').slideToggle();
    });

    $('.outsideLinkModal-toggle').on('click', function (e) {
        e.preventDefault();
        $('.outsideLinkModal').toggleClass('is-visible');
        var button = $(this)
        var urltitle = button.attr('data-link')
        $('.outsideLinkModal .modal-body .outsideLinkModal-link').attr("href", urltitle);
    })

    $('.outsideLinkModal-toggle-footer a').on('click', function (e) {
        e.preventDefault();
        $('.outsideLinkModal').toggleClass('is-visible');
        var footerbutton = $(this)
        var footerurltitle = footerbutton.attr('href')
        console.log(footerurltitle);
        $('.outsideLinkModal .modal-body .outsideLinkModal-link').attr("href", footerurltitle);
    })

    $('.outsideLinkModal a').on('click', function (e) {

        $('.topbar-menu .dropdown-content').removeClass('open');
        $('.outsideLinkModal').removeClass('is-visible');

    })


    new WOW().init();

    $('.chart-pie').easyPieChart({
        size: 170,
        barColor: "#004B8D",
        scaleLength: 0,
        lineWidth: 15,
        trackColor: "#E5E5E5",
        lineCap: "circle",
        animate: 2000,
    });



    // custom tabs
    $('#tabs-nav li:first-child').addClass('active');
    $('.tab-content').hide();
    $('.tab-content:first').show();

    $('#tabs-nav li').click(function () {
        $('#tabs-nav li').removeClass('active');
        $(this).addClass('active');
        $('.tab-content').hide();

        var activeTab = $(this).find('a').attr('href');
        $(activeTab).fadeIn();
        return false;
    });
    
    $('#tabs-nav-select select').on('change', function () {
        $('.tab-container .tab-content').hide();
        var activeTabSelect = $(this).val();
        $(activeTabSelect).fadeIn();
        return false;
    });


    $('.btn-dropdown .btn-dropdown-toggle').click(function () {
        $(this).next('.btn-dropdown-menu').toggleClass('show');
    });

    $(".btn-dropdown-menu li a").click(function () {
        $(this).parents(".btn-dropdown").find('.btn-dropdown-menu').toggleClass('show');
        $(this).parents(".btn-dropdown").find('.btn-dropdown-toggle').html($(this).text() + " <span class='btn-label label-after'><i class='icon-angle-down'></i></span>");
        $('.tab-content').hide();

        var activeTabSelectButton = $(this).attr('href');;
        $(activeTabSelectButton).fadeIn();
        return false;
    });

    $(".accordion").on("click", function () {
        $(this).toggleClass("active");
        $(this).next().slideToggle(200);
    });


    // Vanilla JS version: https://codepen.io/megganeturner/pen/GRyqvvg

    $('a[href^="#"]').on('click', function (e) {
        var target = this.hash,
            $target = $(target);
        
        if (!elementExist($('.tab-post-list'))) {
            $('html, body').stop().animate({
                'scrollTop': $target.offset().top - 70
            }, 900, 'swing', function () {
                window.location.hash = target;
            });
        }
    });


});