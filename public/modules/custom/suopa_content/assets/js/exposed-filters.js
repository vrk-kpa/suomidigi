(function ($) {

  /**
   * Set active class on Views AJAX filter
   * on selected category
   */
  Drupal.behaviors.exposedfilter_buttons = {
    attach: function(context, settings) {
      let view = $('#views-exposed-form-profile-own-content-block-1');

      view.find('.button a').on('click', function(e) {
        e.preventDefault();

        let id = $(e.target).attr('id');
        let filter = view.find('select[name="type"]');

        filter.val(id);

        $('.button a').removeClass('is-active');
        $(e.target).addClass('is-active');

        view.find('select[name="type"]').trigger('change');
        view.find('input.form-submit').trigger('click');

      });
    }
  };

  $(document).ajaxComplete(function(event, xhr, settings) {
    if (
      settings.extraData !== undefined &&
      settings.extraData.view_name === 'profile_own_content'
    ) {
      let view = $('#views-exposed-form-profile-own-content-block-1');
      let filterId = view.find('select[name="type"]').find(":selected").val();
      view.find('.button a').removeClass('is-active');
      view.find('.button').find('#' + filterId).addClass('is-active');
    }
  });


})(jQuery);
