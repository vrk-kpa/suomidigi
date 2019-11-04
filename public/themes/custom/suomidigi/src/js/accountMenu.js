(function ($, Drupal) {
  Drupal.behaviors.accountMenu = {
    attach: function(context) {
      let accountMenuToggleButton = $('.menu--account__button', context);
      let accountMenuWrapper = $('.menu--account__dropdown', context);

      const outsideClickListener = (event) => {
        let target = $(event.target);
        if (!target.closest('.menu--account__dropdown').length && $('.menu--account__dropdown').is(':visible')) {
          handleInteraction(event);
          removeClickListener();
        }
      };

      const removeClickListener = () => {
        document.removeEventListener('click', outsideClickListener);
      };

      function handleInteraction(e) {
        e.stopImmediatePropagation();

        if (accountMenuWrapper.hasClass('is-active')) {
          console.log('on classi');
          accountMenuWrapper.removeClass('is-active').attr('aria-hidden', 'true');
          accountMenuToggleButton.attr('aria-expanded', 'false');
        }
        else {
          console.log('ei oo mut laitetaan');
          accountMenuWrapper.addClass('is-active').attr('aria-hidden', 'false');
          accountMenuToggleButton.attr('aria-expanded', 'true');
          document.addEventListener('click', outsideClickListener);
        }
      }

      accountMenuToggleButton.on({
        touchstart: function(e) {
          handleInteraction(e);
        },
        click: function(e) {
          handleInteraction(e);
        },
        keydown: function (e) {
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
