($ => {
  let title = $('.field--name-field-title');
  let oembed = $('.field--name-field-media-oembed-video input');
  title.hide();

  if (typeof oembed.val() !== "undefined") {
    if (oembed.val().match(/(dreambroker)/)) {
      title.show();
    }

    oembed.on('change', function() {
      if ($(this).val().match(/(dreambroker)/)) {
        title.show();
      } else {
        title.hide();
      }
    });
  }
})(jQuery);
