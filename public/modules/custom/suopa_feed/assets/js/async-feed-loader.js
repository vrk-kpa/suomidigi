/**
 * @file
 * Provides automatic async loading for feed paragraphs.
 */

(function($, Drupal) {
  Drupal.behaviors.async_feed_loader = {
    attach(context, settings) {
      const $feedWrappers = $(".suopa-feed-wrapper").once();

      // Fetch each feed asynchronously.
      $feedWrappers.each((i, wrapper) => {
        if (typeof wrapper.dataset.feedId !== "undefined") {
          Drupal.ajax({
            url: `/suopa_feed/load/${wrapper.dataset.feedId}`
          }).execute();
        }
      });
    }
  };
})(jQuery, Drupal);
