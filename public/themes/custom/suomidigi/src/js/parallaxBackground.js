"use strict";

(function ($, Drupal) {
  Drupal.behaviors.parallaxBackground = {
    attach: function attach(context) {
      $(window, context).on('load', function () {
        var background = document.getElementsByClassName('has-background')[0];
        window.addEventListener('scroll', function () {
          var scrollTop = window.pageYOffset || window.scrollTop;
          var scrollPercent = scrollTop / window.innerHeight * 0.15 || 0;
          background.style.backgroundPositionY = '-' + scrollPercent * window.innerWidth + 'px';
        });
      });
    }
  };
})(jQuery, Drupal);
