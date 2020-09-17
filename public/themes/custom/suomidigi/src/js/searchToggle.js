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
          searchToggleButton.removeClass('is-active');
        }
        else {
          searchFormParentElement.addClass('is-open');
          searchToggleButton.addClass('is-active');
          searchFormInput.focus();
        }
      }

      searchToggleButton.on({
        click: function(e){
          handleInteraction(e);
        }
      });
    }
  }
})(jQuery, Drupal);
