/**
 * @file
 * Provides Fivestar voting effect.
 */

(function($, Drupal) {

  'use strict';

  Drupal.behaviors.async_feed_loader = {
    attach: function(context, settings) {
      Drupal.ajax({url: "/suopa_feed/load/998"}).execute();
      const $feedWrappers = $(".feed-wrapper").once();

      $feedWrappers.each(function (){
        Drupal.ajax({url: "/suopa_feed/load/998"}).execute();
      });
    }
  };
})(jQuery, Drupal);
