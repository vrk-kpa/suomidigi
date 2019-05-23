(function($, Drupal) {
  Drupal.behaviors.mmenu = {
    attach(context) {
      $(".menu--mobile", context).mmenu({
        slidingSubmenus: false,
        extensions: ["position-left", "border-none"],
        offCanvas: true
      });
    }
  };
})(jQuery, Drupal);
