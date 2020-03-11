(function ($, Drupal) {

  'use strict';

  Drupal.suopaMenu = {};

  Drupal.behaviors.menuMoreItems = {
    attach: function attach(context) {
      var $window = $(window);
      var $document = $(document);

      // Be sure to run only once per window document.
      if ($document.once('menu-more-items').length === 0) {
        return;
      }

      // Recalculate dialog size on window resize.
      $window.resize(function () {
        Drupal.suopaMenu.reCalculateMenu();
      });

      // Load only once per window load.
      $(window).once().on('load', function () {
        Drupal.suopaMenu.reCalculateMenu();
      });
    }
  };

  /**
   * Recalculates size of the modal.
   */
  Drupal.suopaMenu.reCalculateMenu = function () {
    var menuDesktop = $('.menu--desktop');
    var menuItem = $('.menu--desktop .menu > li:not(.menu--leftover):not(.is-hidden--desktop)');
    var menuWidth = 0;
    var buttonWidth = $('.menu--desktop .menu--leftover__button').outerWidth();
    var menuLeftOver = $('.menu--desktop .menu--leftover');
    var lastItemMargin = 15;
    menuItem.each(function() {
      menuWidth += $(this).outerWidth(true);
    });

    var availablespace = menuDesktop.width() - buttonWidth;

    if ((menuWidth - lastItemMargin) > availablespace) {
      var lastItem = menuItem.last();
      lastItem.attr('data-width', lastItem.outerWidth(true));
      lastItem.prependTo(menuLeftOver.children('.menu--leftover__wrapper'));
      Drupal.suopaMenu.determineButtonVisibility(menuLeftOver);
      Drupal.suopaMenu.reCalculateMenu();
    } else {
      var firstMoreElement = menuLeftOver.find('li').first();

      if (menuLeftOver.find('li').length === 1 && menuWidth + firstMoreElement.data('width') < availablespace + lastItemMargin + buttonWidth) {
        firstMoreElement.insertBefore(menuLeftOver);
      } else if (menuWidth + firstMoreElement.data('width') < availablespace + lastItemMargin) {
        firstMoreElement.insertBefore(menuLeftOver);
      }
    }

    Drupal.suopaMenu.determineButtonVisibility(menuLeftOver);
  };

  Drupal.suopaMenu.determineButtonVisibility = function (menuLeftOver) {
    if (menuLeftOver.find('li').length > 0) {
      menuLeftOver.removeClass('is-hidden');
    } else {
      menuLeftOver.addClass('is-hidden');
    }
  };

  Drupal.behaviors.menuLeftOver = {
    attach: function attach(context) {
      var menuLeftOverToggleButton = $('.menu--desktop .menu--leftover__button', context);
      var menuLeftOverWrapper = $('.menu--desktop .menu--leftover .menu--leftover__wrapper', context);

      var outsideClickListener = function outsideClickListener(event) {
        var target = $(event.target);

        if (!target.closest('.menu--desktop .menu--leftover .menu--leftover__wrapper').length && menuLeftOverWrapper.is(':visible')) {
          handleInteraction(event);
          removeClickListener();
        }
      };

      var removeClickListener = function removeClickListener() {
        document.removeEventListener('click', outsideClickListener);
      };

      function handleInteraction(e) {
        e.stopImmediatePropagation();

        if (menuLeftOverWrapper.hasClass('is-active')) {
          menuLeftOverWrapper.removeClass('is-active').attr('aria-hidden', 'true');
          menuLeftOverToggleButton.attr('aria-expanded', 'false');
        }
        else {
          menuLeftOverWrapper.addClass('is-active').attr('aria-hidden', 'false');
          menuLeftOverToggleButton.attr('aria-expanded', 'true');
          document.addEventListener('click', outsideClickListener);
        }
      }

      menuLeftOverToggleButton.on({
        'click': function touchstartclick(e) {
          handleInteraction(e);
        },
        keydown: function keydown(e) {
          if (e.which === 27) {
            menuLeftOverWrapper.removeClass('is-active').attr('aria-hidden', 'true');
            menuLeftOverToggleButton.attr('aria-expanded', 'false');
            removeClickListener();
          }
        }
      });
    }
  };
})(jQuery, Drupal);
