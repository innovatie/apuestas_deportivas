/*-----------------------------------------------------------------------------------*/
/*  Main menu
/*-----------------------------------------------------------------------------------*/
const goapostasPrimaryMenu = {
    menuOpen: false,
    subMenuIsOpen: false,
    submenuMaxH: 0,
    boxshadowH: 3,
    minDesktopMenuScrnWidth: 1024,
    minimumSpaceAroundImg: 30,

    setup() {
        jQuery('.main-navigation .menu-item-has-children > a').each((i, el) => {
            const $this = jQuery(el),
                $submenu = $this.next().find('.sub-menu'),
                $associatedImage = $this.find('.goapostas-menu-image');

            $this.attr('role', 'button')
                .addClass('mainnav-show-submenu-btn')
                .attr('aria-has-popup', 'true')
                .attr('aria-expanded', 'false');

            $submenu.prepend('<button class="menu-return" aria-label="Return to main menu">Menu</button>');

            // Fix issue with some screenreaders where a tags with role button still
            // execute default on click (restricted to a tags because of wordpress menu system)
            if ($this.attr('href') === '#' || $this.attr('href') === undefined) {
                $this.attr('href', '');
            }
        });

        goapostasPrimaryMenu.setEvents();
    },

    /*----------------------------------------------------------*/
    /*  Events
    /*----------------------------------------------------------*/
    setEvents() {
        // General Events
        jQuery('.mainnav-show-submenu-btn').click((e) => {
            goapostasPrimaryMenu.topLevelClick(jQuery(e.target), e);
        });

        jQuery('.menu-return').click(goapostasPrimaryMenu.closeAllSubmenu);
        jQuery('#goapostas-nav-cover').click(goapostasPrimaryMenu.menuToggleClick);
        jQuery('#goapostas-menu-toggle').click(goapostasPrimaryMenu.menuToggleClick);

        // Keyboard specific events
        jQuery('body').keydown((e) => {
            if (e.keyCode === 27 && goapostasPrimaryMenu.menuOpen === true) { // escape key
                goapostasPrimaryMenu.menuToggleClick();
            }
        });

        jQuery('.mainnav-show-submenu-btn').keydown((e) => {
            if ((window.goapostasJS.goapostasUtility.isDesktop === false && e.keyCode === 32)
                || (window.goapostasJS.goapostasUtility.isDesktop === false && e.keyCode === 13)
               ) {
                // focus return button
                setTimeout(() => {
                    jQuery(e.target).next().find('.menu-return').focus();
                }, 300);
            }
        });

        jQuery('#primary-menu > .menu-item a').focus(function menuItemFocus() {
            goapostasPrimaryMenu.closePrevNextSubmenu(jQuery(this));
        });
    },

    /*----------------------------------------------------------*/
    /*  Top level click
    /*----------------------------------------------------------*/
    topLevelClick(target, event) {
        const $thisLi = target.parent();

        if (target.attr('aria-has-popup') === 'true') {
            event.preventDefault();

            if ($thisLi.hasClass('submenu-show')) {
                goapostasPrimaryMenu.closeAllSubmenu();
            } else {
                goapostasPrimaryMenu.subMenuOpen($thisLi);
            }
        }
    },

    /*----------------------------------------------------------*/
    /*  Open sub menu
    /*----------------------------------------------------------*/
    subMenuOpen(targetLi) {
        jQuery('#header-outer').addClass('scrolled');
        const linkedAnchor = targetLi.find('a');
        let posLeft = '';

        if (window.goapostasJS.goapostasUtility.isDesktop) {
            posLeft = linkedAnchor.position().left + parseInt(linkedAnchor.css('padding-left'), 10);
        }

        linkedAnchor.attr('aria-expanded', 'true');

        goapostasPrimaryMenu.subMenuIsOpen = true;
        jQuery('#primary-menu').children('li').removeClass('submenu-show').removeClass('submenu-anim-show');
        window.goapostasJS.goapostasUtility.cssAnimIn([targetLi], 'submenu-show', 'submenu-anim-show');

        // Desktop: Calculate margin for li elem to align with parent (Mobile - reset to '')
        // targetLi.find('.menu-col-wrap').css('margin-left', posLeft);

        // allow body click closing
        setTimeout(() => {
            jQuery('body').on('click.menuBodyClosing', (e) => {
                goapostasPrimaryMenu.bodyClickClosing(e);
            });
        }, 0);

        if (window.goapostasJS.goapostasUtility.isDesktop === false) {
            goapostasPrimaryMenu.subMenuToggleMobile('open');
        }
    },

    /*----------------------------------------------------------*/
    /*  Open/close sub menu (mobile)
    /*----------------------------------------------------------*/
    subMenuToggleMobile(openClose) {
        const $mNav = jQuery('.main-navigation'),
            $mUl = $mNav.children('ul');

        // Mobile: Handle overflow while animating, and hiding when complete.
        // (Overflow hidden must not be permanent to allow y-scroll on safari)
        $mNav.addClass('hide-overflow');

        if (openClose === 'open') {
            $mUl.addClass('toplevel-left');
        } else {
            $mUl.removeClass('toplevel-hide').removeClass('toplevel-left');
        }

        setTimeout(() => {
            if (openClose === 'open') {
                $mUl.addClass('toplevel-hide');
            }

            $mNav.removeClass('hide-overflow');
        }, 300);
    },

    /*----------------------------------------------------------*/
    /*  Close all submenus
    /*----------------------------------------------------------*/
    closeAllSubmenu() {
        const topLevelLists = jQuery('.main-navigation').children('ul').children('li'),
            topLevelListArray = [];

        topLevelLists.each((i, list) => {
            const $list = jQuery(list);

            if ($list.hasClass('submenu-show')) {
                topLevelListArray.push($list);
                $list.children('a').attr('aria-expanded', 'false');
            }
        });

        window.goapostasJS.goapostasUtility.cssAnimOut(topLevelListArray, 'submenu-show', 'submenu-anim-show', 300);
        goapostasPrimaryMenu.subMenuIsOpen = false;

        if (window.goapostasJS.goapostasUtility.isDesktop === false) {
            goapostasPrimaryMenu.subMenuToggleMobile('close');
        }
    },

    /*----------------------------------------------------------*/
    /*  Close previous / next submenu (when tabbing - keyboard only)
    /*----------------------------------------------------------*/
    closePrevNextSubmenu(targetLi) {
        const prevLi = targetLi.parent().prev(),
            nextLi = targetLi.parent().next();

        if (prevLi.length > 0 && prevLi.hasClass('submenu-show')) {
            prevLi.find('a').click();
        } else if (nextLi.length > 0 && nextLi.hasClass('submenu-show')) {
            nextLi.find('a').click();
        }
    },

    /*----------------------------------------------------------*/
    /*  Toggle menu visibility (mobile)
    /*----------------------------------------------------------*/
    menuToggleClick() {
        jQuery('#header-outer').addClass('scrolled');
        const $toggleBtn = jQuery('#goapostas-menu-toggle');

        if ($toggleBtn.attr('aria-expanded') === 'false') {
            goapostasPrimaryMenu.menuOpen = true;
            $toggleBtn.attr('aria-expanded', 'true');
            window.goapostasJS.goapostasUtility.cssAnimIn([$toggleBtn], 'opened', 'showX', 200);
            window.goapostasJS.goapostasUtility.cssAnimIn([jQuery('.main-navigation'), jQuery('#goapostas-nav-cover')], 'open', 'open-anim');
        } else {
            goapostasPrimaryMenu.menuOpen = false;
            $toggleBtn.attr('aria-expanded', 'false');
            window.goapostasJS.goapostasUtility.cssAnimOut([$toggleBtn], 'opened', 'showX', 200);
            window.goapostasJS.goapostasUtility.cssAnimOut([jQuery('.main-navigation'), jQuery('#goapostas-nav-cover')], 'open', 'open-anim', 300);
        }
    },

    bodyClickClosing(e) {
        if (window.goapostasJS.goapostasUtility.isDesktop) {
            // A click outside of menu is handled by the content cover on mobile
            if (jQuery(e.target).parents('.main-navigation').length === 0) {
                e.preventDefault();
                goapostasPrimaryMenu.closeAllSubmenu();
                jQuery('body').off('click.menuBodyClosing');
            }
        } else {
            jQuery('body').off('click.menuBodyClosing');
        }
    },
};

module.exports = goapostasPrimaryMenu;
