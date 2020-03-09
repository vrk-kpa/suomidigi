($ => {
  // Check initial status and disable necessary checkboxes.
  let select = $('select[name="moderation_state[0][state]"]');
  select.once('init').each(function () {
    $("input#edit-submit").attr("disabled", true);
  });

  select.on('change', value => {
    let selected = $(this).children("option:selected").val();
    if (selected === 'a_select') {
      $("input#edit-submit").attr("disabled", true);
    }
    else {
      $("input#edit-submit").removeAttr('disabled');
    }
  });

})(jQuery);
