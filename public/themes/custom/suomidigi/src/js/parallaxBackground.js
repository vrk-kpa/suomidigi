"use strict";

(function ($, Drupal) {
  Drupal.behaviors.parallaxBackground = {
    attach: function attach(context) {
      $(window).once().on('load', function () {
        var background = document.getElementsByClassName('page-main')[0];
        window.addEventListener('scroll', function () {
          var scrollTop = window.pageYOffset || window.scrollTop;
          var scrollPercent = scrollTop / window.innerHeight * 0.15 || 0;
          background.style.backgroundPosition = '50%' + '-' + scrollPercent * window.innerWidth + 'px';
        });
      });
    }
  };
})(jQuery, Drupal);
