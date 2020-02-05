(($, Drupal) => {
  /**
   * Trigger view refresh when flag is clicked.
   */
  Drupal.AjaxCommands.prototype.updateCustomView = function (ajax, response, status) {
    if (status === 'success') {
      $.each(Drupal.views.instances, (i, view) => {
        if (view.settings.view_name === response.view) {
          let selector = $('.js-view-dom-id-' + view.settings.view_dom_id);
          selector.once('customViewTriggered').triggerHandler('RefreshView');
          selector.unbind();
        }
      });
    }
  }
})(jQuery, Drupal);
