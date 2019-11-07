(function ($, Drupal) {
  Drupal.behaviors.contentCreationLinks = {
    attach: function(context) {
      let contentCreationLinksToggleButton = $('.menu--content-creation__button', context);
      let contentCreationLinksWrapper = $('.menu--content-creation__dropdown', context);

      const outsideClickListener = (event) => {
        let target = $(event.target);
        if (!target.closest('.menu--content-creation__dropdown').length && $('.menu--content-creation__dropdown').is(':visible')) {
          handleInteraction(event);
          removeClickListener();
        }
      };

      const removeClickListener = () => {
        document.removeEventListener('click', outsideClickListener);
      };

      function handleInteraction(e) {
        e.stopImmediatePropagation();

        if (contentCreationLinksWrapper.hasClass('is-active')) {
          contentCreationLinksWrapper.removeClass('is-active').attr('aria-hidden', 'true');
          contentCreationLinksToggleButton.attr('aria-expanded', 'false');
        }
        else {
          contentCreationLinksWrapper.addClass('is-active').attr('aria-hidden', 'false');
          contentCreationLinksToggleButton.attr('aria-expanded', 'true');
          document.addEventListener('click', outsideClickListener);
        }
      }

      contentCreationLinksToggleButton.on({
        touchstart: function(e) {
          handleInteraction(e);
        },
        click: function(e) {
          handleInteraction(e);
        },
        keydown: function (e) {
          if (e.which === 27) {
            contentCreationLinksWrapper.removeClass('is-active').attr('aria-hidden', 'true');
            contentCreationLinksToggleButton.attr('aria-expanded', 'false');
            removeClickListener();
          }
        }
      });
    }
  }
})(jQuery, Drupal);
