(function ($, Drupal) {
  Drupal.behaviors.languageSwitcher = {
    attach: function(context) {
      let languageSwitchToggleButton = $('.language-switch__button', context);
      let languageSwitchWrapper = $('.language-switch__wrapper', context);

      const outsideClickListener = (event) => {
        let target = $(event.target);
        if (!target.closest('.language-switch__wrapper').length && $('.language-switch__wrapper').is(':visible')) {
          handleInteraction(event);
          removeClickListener();
        }
      };

      const removeClickListener = () => {
        document.removeEventListener('click', outsideClickListener);
      };

      function handleInteraction(e) {
        e.preventDefault();

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
        touchstart: function(e) {
          handleInteraction(e);
        },
        click: function(e) {
          handleInteraction(e);
        },
        keydown: function (e) {
          if (e.which === 27) {
            languageSwitchWrapper.removeClass('is-active').attr('aria-hidden', 'true');
            languageSwitchToggleButton.attr('aria-expanded', 'false');
            removeClickListener();
          }
        }
      });
    }
  }
})(jQuery, Drupal);
