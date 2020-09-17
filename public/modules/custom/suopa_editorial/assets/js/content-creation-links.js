"use strict";

(function ($, Drupal) {
  Drupal.behaviors.contentCreationLinks = {
    attach: function attach(context) {
      var contentCreationLinksToggleButton = $('.menu--content-creation__button', context);
      var contentCreationLinksWrapper = $('.menu--content-creation__dropdown', context);

      var outsideClickListener = function outsideClickListener(event) {
        var target = $(event.target);
        if (!target.closest('.menu--content-creation__dropdown').length && $('.menu--content-creation__dropdown').is(':visible')) {
          handleInteraction(event);
          removeClickListener();
        }
      };

      var removeClickListener = function removeClickListener() {
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
        touch: function(e) {
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
