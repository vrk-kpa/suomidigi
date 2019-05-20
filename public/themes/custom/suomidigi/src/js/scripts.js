(function($, Drupal) {
  var menu = $(".menu--main").mmenu({
    offCanvas: true,
    extensions: [
      "position-right",
      "position-front",
      "border-none"
    ]
  }).data("mmenu");
  Drupal.behaviors.mmenu = {
    close: function() {
      menu.close();
    }
  };
})(jQuery, Drupal);
