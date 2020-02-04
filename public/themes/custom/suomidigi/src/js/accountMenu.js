"use strict";

(function ($, Drupal) {
  Drupal.behaviors.accountMenu = {
    attach: function(context) {
      var accountMenuToggleButton = $('.menu--account__button', context);
      var accountMenuWrapper = $('.menu--account__dropdown', context);

      var outsideClickListener = function outsideClickListener(event) {
        var target = $(event.target);

        if (!target.closest('.menu--account__dropdown').length && $('.menu--account__dropdown').is(':visible')) {
          handleInteraction(event);
          removeClickListener();
        }
      };

      var removeClickListener = function removeClickListener() {
        document.removeEventListener('click', outsideClickListener);
      };

      function handleInteraction(e) {
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
        'click': function touchstartclick(e) {
          handleInteraction(e);
        },
        keydown: function keydown(e) {
          if (e.which === 27) {
            accountMenuWrapper.removeClass('is-active').attr('aria-hidden', 'true');
            accountMenuToggleButton.attr('aria-expanded', 'false');
            removeClickListener();
          }
        }
      });
    }
  }
})(jQuery, Drupal);
