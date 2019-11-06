/**
 * @file
 * Provides Fivestar voting effect.
 */

(function($, Drupal) {
  Drupal.behaviors.async_feed_loader = {
    attach(context, settings) {
      const $feedWrappers = $(".feed-wrapper").once();

      $feedWrappers.each(() => {
        const classes = $(this).attr("class");
        Drupal.ajax({ url: "/suopa_feed/load/1002" }).execute();
      });
    }
  };
})(jQuery, Drupal);
