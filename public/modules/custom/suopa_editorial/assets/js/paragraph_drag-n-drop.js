(($) => {
  /**
   * Disable submit button
   */
  function disableNodeSubmit() {
    if ($(("input.field-dragdrop-mode-submit.form-submit:not(.paragraphs-dropdown-action)"))[0]){
      $("input#edit-submit").attr("disabled", true);
    } else {
      $("input#edit-submit").attr("disabled", false);
    }
  }

  disableNodeSubmit();

  $(document).ajaxComplete(() => {
    disableNodeSubmit();
  });
})(jQuery);
