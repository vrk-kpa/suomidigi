(($, Drupal) => {
  var headers = $('.legislation-collection__item .legislation-card__header');
  var expandLink = $('.legislation-collection__toggle-button');
  var current, siblings, siblingsOpen;

  // React on header click.
  headers.click(function() {
    $(this).toggleClass('is-active');
    $(this).parents('.legislation-card').toggleClass('is-open');

    // Get the amount of sibling elements.
    current = $(this).parents('.legislation-card').hasClass('is-open');
    siblings = $(this).parents('.legislation-card').siblings('.legislation-card').length;
    siblingsOpen = $(this).parents('.legislation-card').siblings('.legislation-card.is-open').length;

    // Change the text for toggle button based on the amount of open cards.
    if (current && siblings === siblingsOpen) {
      $(this)
        .parents('.legislation-collection__item')
        .children('.legislation-collection__toggle-button')
        .text(Drupal.t('Collapse all'))
        .addClass('is-all-open');
    } else {
      $(this)
        .parents('.legislation-collection__item')
        .children('.legislation-collection__toggle-button')
        .text(Drupal.t('Expand all'))
        .removeClass('is-all-open');
    }

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

