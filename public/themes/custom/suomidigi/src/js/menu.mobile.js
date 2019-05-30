/**
 * @file Main menu dropdown on hover.
 */
(function ($, Drupal) {
  "use strict";

  Drupal.behaviors.mobileMainMenu = {
    attach: function (context, settings) {
      $(window).once().on('load', function () {
        var mobyMenu = new Moby({
          breakpoint		 : 768,
          enableEscape	 : true,
          menu: $("#menu--mobile"),
          menuClass		 : 'left-side',
          mobyTrigger: $("#menu-trigger"),
          onClose          : false,
          onOpen           : false,
          overlay			 : true,
          overlayClass 	 : 'dark',
          subMenuOpenIcon  : '<span>&#x25BC;</span>',
          subMenuCloseIcon : '<span>&#x25B2;</span>',
          template         : '<div class="moby-wrap"><div class="moby-close"><span class="moby-close-icon"></span> Close Menu</div><div class="moby-menu"></div></div>'
        });
      });
    }
  };
})(jQuery, Drupal);
