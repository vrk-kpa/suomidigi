(function($, Drupal) {
  Drupal.behaviors.stickyHeader = {
    attach: function(context) {
      var body = $('body');
      var stickyMenu = $('#header');
      var lastScrollTop = $(window).scrollTop();
      var scrollAmount = 0;
      var scrollStart = 0;
      var upScroll;
      var downScroll;
      var currentScrollTop;
      var stickyMenuPosition = $('#header')[0].getBoundingClientRect().bottom - $('#header')[0].getBoundingClientRect().top;

      $(document.body, context).once('resizeHeader', function() {
        resizeHeader();
      });

      $(window).on("orientationchange", function(event) {
        resizeHeader();
        stickyMenuPosition = stickyMenu.offset().top;
      });

      function resizeHeader() {
        stickyMenu.parent().removeAttr('style').outerHeight(stickyMenu.parent()[0].getBoundingClientRect().bottom - stickyMenu.parent()[0].getBoundingClientRect().top);
        stickyMenuPosition = stickyMenu.offset().top;
      }

      stickyMenu.once('sticky', function () {
        var scrollHandler = $(window).scroll(function () {
          stickyMenuPosition = $('#header')[0].getBoundingClientRect().bottom - $('#header')[0].getBoundingClientRect().top;
          currentScrollTop = $(window).scrollTop();

          if ($(window).scrollTop() > 0) {
            body.addClass('is-scrolled');
          } else {
            body.removeClass('is-scrolled');
          }

          if ($(window).scrollTop() > stickyMenuPosition + stickyMenu.outerHeight()) {
            stickyMenu.addClass('sticky-active');
            body.addClass('is-sticky');

            if (currentScrollTop > lastScrollTop) {
              // downscroll code
              downScroll = true;

              if (upScroll == true) {
                upScroll = false;
                scrollStart = currentScrollTop;
              }

              scrollAmount = Math.abs(scrollStart - currentScrollTop);

              if (scrollAmount > 25) {
                if (stickyMenu.hasClass('sticky-visible')) {
                  stickyMenu.removeClass('sticky-visible').addClass('sticky-hidden');
                }
              }
            } else {
              // upscroll code
              upScroll = true;

              if (downScroll == true) {
                downScroll = false;
                scrollStart = currentScrollTop;
              }

              scrollAmount = Math.abs(scrollStart - currentScrollTop);

              if (scrollAmount > 200) {
                stickyMenu.addClass('sticky-visible');
                stickyMenu.removeClass('sticky-hidden')
              }
            }
            // console.log(window.pageYOffset);
            lastScrollTop = currentScrollTop;
          }
          else if ($(window).scrollTop() <= stickyMenuPosition) {
            body.removeClass('is-sticky');
            stickyMenu.removeClass('sticky-active');
            stickyMenu.removeClass('sticky-visible');
            stickyMenu.removeClass('sticky-hidden');
            lastScrollTop = 0;
          }
        });
      });


      // $(document).ready(function () {
      //   var c, currentScrollTop = 0, navbar = $('#header');
      //   stickyMenuPosition = $('#header')[0].getBoundingClientRect().bottom - $('#header')[0].getBoundingClientRect().top;
      //   currentScrollTop = $(window).scrollTop();
      //
      //   $(window).scroll(function () {
      //     var a = $(window).scrollTop();
      //     var b = navbar.height();
      //     navbar.addClass("sticky-active");
      //     currentScrollTop = a;
      //
      //     if (c < currentScrollTop && a > b + b) {
      //       navbar.removeClass("sticky-visible");
      //       navbar.addClass("sticky-hidden");
      //     } else if (c > currentScrollTop && !(a <= b)) {
      //       navbar.removeClass("sticky-hidden");
      //       navbar.addClass("sticky-visible");
      //     } else if (currentScrollTop == 0) {
      //       navbar.removeClass("sticky-hidden");
      //       navbar.removeClass("sticky-visible");
      //       navbar.removeClass("sticky-active");
      //     }
      //     c = currentScrollTop;
      //   });
      // });
    }
  }
})(jQuery, Drupal);
