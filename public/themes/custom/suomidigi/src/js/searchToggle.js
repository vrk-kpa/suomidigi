(function ($, Drupal) {
  Drupal.behaviors.searchToggle = {
    attach: function(context) {
      let searchToggleButton = $('#search-trigger', context);
      let searchFormParentElement = $('.search-form-container', context);

      if (window.innerWidth > 769) {
        searchFormParentElement.removeClass('is-hidden');
      } else {
        searchFormParentElement.addClass('is-hidden');
      }

      window.addEventListener('resize', () => {
        if (window.innerWidth > 769) {
          searchFormParentElement.removeClass('is-hidden');
        } else {
          searchFormParentElement.addClass('is-hidden');
        }
      });

      function handleInteraction(e) {
        e.preventDefault();
        let searchFormInput = $('#search', context);
        searchFormParentElement.toggleClass('is-hidden');
        searchFormInput.focus();
      }

      searchToggleButton.on({
        touchstart: function(e){
          handleInteraction(e);
        },
        click: function(e){
          handleInteraction(e);
        }
      });
    }
  }
})(jQuery, Drupal);
