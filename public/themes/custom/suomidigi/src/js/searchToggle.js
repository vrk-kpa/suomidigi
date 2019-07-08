(function ($, Drupal) {
  Drupal.behaviors.searchToggle = {
    attach: function(context, settings) {
      const searchToggleButton = document.querySelector("#search-trigger");

      searchToggleButton.addEventListener("click", () => {
        const searchFormParentElement = document.querySelector(".search-form-container");
        const searchFormInput = document.getElementById("search");
        searchFormParentElement.classList.toggle("is-hidden");
        searchFormInput.focus();
      });
    }
  }
})(jQuery, Drupal);
