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
            `${'<header class="page-header__mobile"><a href="/" class="logo logo--header" title="Home" rel="home">' +
              '  <svg viewBox="0 0 55 55" width="55px" height="55px" aria-labelledby="title" id="suomidigi_flag" role="img" focusable="false">' +
              '    <title id="title">'}${siteName} logo</title>` +
            `      <desc id="desc">suomidigi.fi</desc>` +
            `      <g class="icon--flag">` +
            `        <path fill="#003479" d="M53,0H2C0.9,0,0,0.9,0,2v51c0,1.1,0.9,2,2,2h51c1.1,0,2-0.9,2-2V2C55,0.9,54.1,0,53,0z"></path>` +
            `        <path fill="#FFFFFF" d="M14,20v-5c0-1.1,0.9-2,2-2h5v7"></path>` +
            `        <path fill="#FFFFFF" d="M14,27h7v14c0,0.5-0.4,1-1,1h-5c-0.6,0-1-0.5-1-1"></path>` +
            `        <path fill="#FFFFFF" d="M28,13h13c0.5,0,1,0.4,1,1v6H28"></path>` +
            `        <path fill="#FFFFFF" d="M41,34H28v-7h14v6C42,33.6,41.6,34,41,34z"></path>` +
            `      </g>` +
            `  </svg>` +
            `  <span class="logo--text">${siteName}</span></a>` +
            `  <p class="site-slogan">${siteSlogan}</p>` +
            `</header>` +
            `<div class="moby-menu"></div>`;

          mobyMenu = new Moby({
            breakpoint: 768,
            enableEscape: true,
            menu: $("#menu--mobile"),
            menuClass: 'left-side',
            mobyTrigger: $("#menu-trigger"),
            onOpen: function () {
              $('.moby-menu ul > li.menu-item--active-trail > ul').slideDown(Moby.slideTransition);
              $('.moby-menu ul > li.menu-item--active-trail .moby-expand').addClass('moby-submenu-open').html(this.subMenuCloseIcon);
            },
            onClose: false,
            overlay: true,
            overlayClass: 'dark',
            subMenuOpenIcon: subMenuIconOpen,
            subMenuCloseIcon: subMenuIconClose,
            template: template
          });
        });
    },
    close() {
      mobyMenu.closeMoby();
    }
  };
})(jQuery, Drupal, drupalSettings);
