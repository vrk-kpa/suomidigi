(function ($, Drupal) {
  Drupal.behaviors.articleTeaserContentHeight = {
    attach: function(context, settings) {
      const articleTeaserImage = document.querySelectorAll('.article-teaser__image');

      [...articleTeaserImage].map(e => {
        e.nextElementSibling.style.height = `calc(100% - ${e.offsetHeight}px)`;

        window.addEventListener('resize', () => {
          e.nextElementSibling.style.height = `calc(100% - ${e.offsetHeight}px)`;
        });
      });
    }
  }
})(jQuery, Drupal);
