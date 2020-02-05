(function ($) {

  /**
   * Set active class on Views AJAX filter
   * on selected category
   */
  Drupal.behaviors.exposedfilter_buttons = {
    attach: function(context, settings) {
      let ownContent = $('#views-exposed-form-profile-own-content-block-1');

      ownContent.find('.button a').on('click', function(e) {
        e.preventDefault();

        let id = $(e.target).attr('id');
        let filter = ownContent.find('select[name="type"]');

        filter.val(id);

        $('.button a').removeClass('is-active');
        $(e.target).addClass('is-active');

        ownContent.find('select[name="type"]').trigger('change');
        ownContent.find('input.form-submit').trigger('click');
      });

      let myBookmarks = $('#views-exposed-form-profile-my-bookmarks-block-1');

      myBookmarks.find('.button a').on('click', function(e) {
        e.preventDefault();

        let id = $(e.target).attr('id');
        let filter = myBookmarks.find('select[name="type"]');

        filter.val(id);

        $('.button a').removeClass('is-active');
        $(e.target).addClass('is-active');

        myBookmarks.find('select[name="type"]').trigger('change');
        myBookmarks.find('input.form-submit').trigger('click');
      });
    }
  };

  $(document).ajaxComplete(function(event, xhr, settings) {
    if (
      settings.extraData !== undefined &&
      settings.extraData.view_name === 'profile_own_content'
    ) {
      let ownContent = $('#views-exposed-form-profile-own-content-block-1');
      let filterId = ownContent.find('select[name="type"]').find(":selected").val();
      ownContent.find('.button a').removeClass('is-active');
      ownContent.find('.button').find('#' + filterId).addClass('is-active');
    }

    if (
      settings.extraData !== undefined &&
      settings.extraData.view_name === 'profile_my_bookmarks'
    ) {
      let myBookmarks = $('#views-exposed-form-profile-my-bookmarks-block-1');
      let filterId = myBookmarks.find('select[name="type"]').find(":selected").val();
      myBookmarks.find('.button a').removeClass('is-active');
      myBookmarks.find('.button').find('#' + filterId).addClass('is-active');
    }
  });


})(jQuery);
