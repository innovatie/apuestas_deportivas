const goapostasDebounce = require('./debounce.js');

const goapostasUtility = {
    //-------------------------------------------------
    // Standard fade in/out adding and removing of classes
    //-------------------------------------------------
    cssAnimIn(elements, showClass, animClass, animTime = 20) {
        elements.forEach((el) => {
            el.addClass(showClass);
        });

        setTimeout(() => {
            elements.forEach((el) => {
                el.addClass(animClass);
            });
        }, animTime);
    },

    cssAnimOut(elements, showClass, animClass, animTime) {
        elements.forEach((el) => {
            el.removeClass(animClass);
        });

        setTimeout(() => {
            elements.forEach((el) => {
                el.removeClass(showClass);
            });
        }, animTime);
    },

    //-------------------------------------------------
    // Search
    //-------------------------------------------------

    toggleSearch() {
        const searchForm = jQuery('#goapostas-search-holder'),
            toggleBtn = jQuery('#goapostas-search-btn'),
            logo = jQuery('#header-secondary-outer #logo');

        if (searchForm.hasClass('search-opened')) {
            goapostasUtility.cssAnimOut(
                [searchForm, toggleBtn, logo],
                'search-opened',
                'search-open-anim',
                300,
            );

            jQuery('#goapostas-search-btn').attr('aria-expanded', 'false');
            jQuery('#primary-menu a').attr('tabindex', 0).attr('aria-hidden', 'false');
        } else {

            goapostasUtility.cssAnimIn(
                [searchForm, toggleBtn, logo],
                'search-opened', 'search-open-anim',
            );
            jQuery('#goapostas-search-btn').attr('aria-expanded', 'true');
            jQuery('#primary-menu a').attr('tabindex', -1).attr('aria-hidden', 'true');

            setTimeout(() => {
                searchForm.find('input').focus();
            }, 0);
        }
    },

    //-------------------------------------------------
    // Check desktop / non
    //-------------------------------------------------
    isDesktop: null,

    checkDesktop: goapostasDebounce(() => {
        goapostasUtility.isDesktop = jQuery(window).width() > 1023;
    }, 50),
};

module.exports = goapostasUtility;
