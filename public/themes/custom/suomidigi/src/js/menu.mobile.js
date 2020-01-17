/**
 * @file Mobile menu.
 */
(function($, Drupal, drupalSettings) {
  "use strict";

  var siteName = drupalSettings.siteName;
  var siteSlogan = drupalSettings.siteSlogan;
  var iconPath = drupalSettings.iconsPath;
  var mobyMenu = '';
  var subMenuIconOpen = '<svg class="icon"><title>' + Drupal.t("Close menu") + '</title><use xlink:href="' + iconPath +'#chevron-down" /></svg>';
  var subMenuIconClose = '<svg class="icon"><title>' + Drupal.t("Close menu") + '</title><use xlink:href="' + iconPath +'#chevron-up" /></svg>';

  Drupal.behaviors.mobileMainMenu = {
    attach: function (context, settings) {
      $(window).once().on('load', function () {
        var template =
          `${'<header class="page-header__mobile"><a href="/" class="logo logo--header" title="' + Drupal.t('Home') + '" rel="home">' +
          '  <span class="logo--text">'}${siteName}</span></a>` +
          `  <p class="site-slogan">${siteSlogan}</p>` +
          `</header>` +
          `<div id="moby-menu" class="moby-menu"></div>`;

        mobyMenu = new Moby({
          breakpoint: 768,
          enableEscape: true,
          menu: $("#menu--mobile"),
          menuClass: 'left-side',
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
      });
    },
    close() {
      mobyMenu.closeMoby();
    }
  };
})(jQuery, Drupal, drupalSettings);
