(function ($, Drupal) {
  Drupal.behaviors.searchToggle = {
    attach: function(context, settings) {
      const searchToggleButton = document.querySelector("#search-trigger");
      const searchFormParentElement = document.querySelector(".search-form-container");

      if (window.innerWidth > 769) {
        searchFormParentElement.classList.remove("is-hidden");
      } else {
        searchFormParentElement.classList.add("is-hidden");
      }

      window.addEventListener("resize", () => {
        if (window.innerWidth > 769) {
          searchFormParentElement.classList.remove("is-hidden");
        } else {
          searchFormParentElement.classList.add("is-hidden");
        }
      });

      function handleInteraction(e) {
        e.preventDefault();
        const searchFormInput = document.getElementById("search");
        searchFormParentElement.classList.toggle("is-hidden");
        searchFormInput.focus();
      }

      searchToggleButton.addEventListener('touchstart', handleInteraction);
      searchToggleButton.addEventListener('click', handleInteraction);
    }
  }
})(jQuery, Drupal);
