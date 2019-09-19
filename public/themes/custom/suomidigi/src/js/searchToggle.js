(function ($, Drupal) {
  Drupal.behaviors.searchToggle = {
    attach: function(context) {
      let searchToggleButton = $('#search-trigger', context);
      let searchFormParentElement = $('.search-form-container', context);

      function handleInteraction(e) {
        e.preventDefault();
        let searchFormInput = $('#search', context);
        if (searchFormParentElement.hasClass('is-open')) {
          searchFormParentElement.removeClass('is-open');
        }
        else {
          searchFormParentElement.addClass('is-open');
          searchFormInput.focus();
        }
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
