"use strict";

(function ($, Drupal) {
  Drupal.behaviors.languageSwitcher = {
    attach: function attach(context) {
      var languageSwitchToggleButton = $('.language-switch__button', context);
      var languageSwitchWrapper = $('.language-switch__dropdown', context);

      var outsideClickListener = function outsideClickListener(event) {
        var target = $(event.target);

        if (!target.closest('.language-switch__dropdown').length && $('.language-switch__dropdown').is(':visible')) {
          handleInteraction(event);
          removeClickListener();
        }
      };

      var removeClickListener = function removeClickListener() {
        document.removeEventListener('click', outsideClickListener);
      };

      function handleInteraction(e) {
        e.stopImmediatePropagation();

        if (languageSwitchWrapper.hasClass('is-active')) {
          languageSwitchWrapper.removeClass('is-active').attr('aria-hidden', 'true');
          languageSwitchToggleButton.attr('aria-expanded', 'false');
        }
        else {
          languageSwitchWrapper.addClass('is-active').attr('aria-hidden', 'false');
          languageSwitchToggleButton.attr('aria-expanded', 'true');
          document.addEventListener('click', outsideClickListener);
        }
      }

      languageSwitchToggleButton.on({
        'click': function touchstartclick(e) {
          handleInteraction(e);
        },
        keydown: function keydown(e) {
          if (e.which === 27) {
            languageSwitchWrapper.removeClass('is-active').attr('aria-hidden', 'true');
            languageSwitchToggleButton.attr('aria-expanded', 'false');
            removeClickListener();
          }
        }
      });
    }
  };
})(jQuery, Drupal);
