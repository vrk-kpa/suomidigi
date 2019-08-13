(($, Drupal) => {
  var headers = $('.legislation-collection__item .legislation-card__header');
  var expandLink = $('.legislation-collection__toggle-button');

  // React on header click.
  headers.click(function() {
    $(this).toggleClass('is-active');
    $(this).parents('.legislation-card').toggleClass('is-open');
    return false;
  });

  // Hook up the expand/collapse all
  expandLink.click(function(){
    var items = $(this).parent('.legislation-collection__item');

    if (!$(this).hasClass('is-all-open')) {
      $(this).text(Drupal.t('Collapse all')).addClass('is-all-open');
      items.find('.legislation-card').addClass('is-open');
      headers.addClass('is-active');
    } else {
      $(this).text(Drupal.t('Expand all')).removeClass('is-all-open');
      items.find('.legislation-card').removeClass('is-open');
      headers.removeClass('is-active');
    }
  });
})(jQuery, Drupal);
