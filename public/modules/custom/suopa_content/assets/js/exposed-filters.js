(function ($) {

  /**
   * Set active class on Views AJAX filter
   * on selected category
   */
  Drupal.behaviors.exposedfilter_buttons = {
    attach: function(context, settings) {
      $('.filter-tab a').on('click', function(e) {
        e.preventDefault();

        let view = $('#views-exposed-form-profile-own-content-block-1');
        let id = $(e.target).attr('id');
        let filter = view.find('select[name="type"]');

        filter.val(id);

        $('.filter-tab a').removeClass('active');
        $(e.target).addClass('active');

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
      let filterId = $('#views-exposed-form-profile-own-content-block-1 select[name="type"]').find(":selected").val();
      $('.filter-tab a').removeClass('active');
      $('.filter-tab').find('#' + filterId).addClass('active');
    }
  });


})(jQuery);
