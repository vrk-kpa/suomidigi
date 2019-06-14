(($) => {
  /**
   * Hide Blog Tag Select List
   */
  function hideBlogTagSelectList() {
    if ($('#edit-field-blog-tags-wrapper')) {
      $('#edit-field-blog-tags-wrapper').hide();

      if ($('#edit-field-article-type').val() == 13) {
        $('#edit-field-blog-tags-wrapper').show();
      } else {
        $('#edit-field-blog-tags-wrapper').hide();
      }


      $('#edit-field-article-type').on('change', function() {
        if (this.value == 13) {
          $('#edit-field-blog-tags-wrapper').show();
        } else {
          $('#edit-field-blog-tags-wrapper').hide();
        }
      });
    }
  }

  hideBlogTagSelectList();

  $(document).ajaxComplete(() => {
    hideBlogTagSelectList();
  });
})(jQuery);
