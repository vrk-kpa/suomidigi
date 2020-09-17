/**
 * @file Mobile menu.
 */
(function($, Drupal, drupalSettings) {
  "use strict";

  var iconPath = drupalSettings.iconsPath;
  var mobyMenu = '';
  var subMenuIconOpen = '<svg class="icon"><title>' + Drupal.t("Close menu") + '</title><use xlink:href="' + iconPath +'#chevron-down" /></svg>';
  var subMenuIconClose = '<svg class="icon"><title>' + Drupal.t("Close menu") + '</title><use xlink:href="' + iconPath +'#chevron-up" /></svg>';

  Drupal.behaviors.mobileMainMenu = {
    attach: function (context) {
      $(window).once().on('load', function () {
        var template =
          `${'<header class="page-header__mobile"></header>'}` +
          `<div id="moby-menu-profile" class="menu--account moby-menu__profile"></div>` +
          `<div id="moby-menu" class="moby-menu"></div>`;

        mobyMenu = new Moby({
          breakpoint: 768,
          enableEscape: true,
          menu: $("#menu--mobile"),
          menuClass: 'right-side',
          mobyTrigger: $("#menu-trigger"),
          onOpen: function () {
            $('.moby-menu ul.menu--is-lvl-1').slideUp(Moby.slideTransition);
            $('.moby-menu .moby-expand').removeClass('moby-submenu-open').html(this.subMenuOpenIcon);
            $('.moby-menu .menu-item--active-trail > ul').slideDown(Moby.slideTransition);
            $('.moby-menu .menu-item--active-trail > a .moby-expand').addClass('moby-submenu-open').html(this.subMenuCloseIcon);
          },
          onClose: false,
          overlay: true,
          overlayClass: 'dark',
          subMenuOpenIcon: subMenuIconOpen,
          subMenuCloseIcon: subMenuIconClose,
          template: template
        });

        $('#block-languageswitcher').contents().clone().appendTo('#moby-menu');
        $('#block-languageswitcher ul#menu-account-dropdown').attr('id', 'moby-menu-account-dropdown');

        var profileMenu = $('#block-account-menu').contents().clone();
        $('#moby-menu-profile').html(profileMenu);

        // Switch duplicated IDs.
        $('#moby-menu-profile ul#menu-account-dropdown').attr('id', 'moby-menu-account-dropdown');
        $('#moby-menu-profile #block-account-menu-menu').attr('id', 'block-account-menu-moby');
        $('#moby-menu #language-switch-dropdown').attr('id', 'moby-language-switch-dropdown');

        var accountMenuToggleButton = $('.moby-menu__profile .menu--account__button', context);
        var accountMenuWrapper = $('.moby-menu__profile .menu--account__dropdown', context);

        var outsideClickListener = (event) => {
          var target = $(event.target);
          if (!target.closest('.moby-menu__profile .menu--account__dropdown').length && $('.menu--account__dropdown').is(':visible')) {
            handleMobyInteraction(event);
            removeClickListener();
          }
        };

        var removeClickListener = () => {
          document.removeEventListener('click', outsideClickListener);
        };

        function handleMobyInteraction(e) {
          e.stopImmediatePropagation();

          if (accountMenuWrapper.hasClass('is-active')) {
            accountMenuWrapper.removeClass('is-active').attr('aria-hidden', 'true');
            accountMenuToggleButton.attr('aria-expanded', 'false');
          }
          else {
            accountMenuWrapper.addClass('is-active').attr('aria-hidden', 'false');
            accountMenuToggleButton.attr('aria-expanded', 'true');
            document.addEventListener('click', outsideClickListener);
          }
        }

        accountMenuToggleButton.on({
          'click touch': function(e) {
            handleMobyInteraction(e);
          },
          keydown: function (e) {
            if (e.which === 27) {
              accountMenuWrapper.removeClass('is-active').attr('aria-hidden', 'true');
              accountMenuToggleButton.attr('aria-expanded', 'false');
              removeClickListener();
            }
          }
        });
      });
    },
    close() {
      mobyMenu.closeMoby();
    }
  };
})(jQuery, Drupal, drupalSettings);
