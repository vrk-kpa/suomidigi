($ => {
  // Make sibling checkboxes disabled if one is enabled.
  function handleDisabling(element) {
    const id = element;
    const wrapper = ".is-disableable--wrapper";

    if ($(id).is(":checked")) {
      $(id)
        .closest(wrapper)
        .siblings(wrapper)
        .addClass("is-disabled")
        .find(".is-disableable")
        .attr("disabled", true);
    } else {
      $(id)
        .closest(wrapper)
        .siblings(wrapper)
        .removeClass("is-disabled")
        .find(".is-disableable")
        .removeAttr("disabled");
    }
  }

  // Check initial status and disable necessary checkboxes.
  function initializeCheckboxes() {
    const isDisableable = $("input.is-disableable");

    isDisableable.each((index, value) => {
      if ($(value).is(":checked")) {
        const id = `#${$(value).attr("id")}`;
        const wrapper = ".is-disableable--wrapper";
        const item = $(id)
          .closest(wrapper)
          .siblings(wrapper);

        if (!item.hasClass("is-disabled")) {
          item
            .addClass("is-disabled")
            .find(".is-disableable")
            .attr("disabled", true);
        }
      }
    });

    isDisableable.on("change", value => {
      handleDisabling(value.currentTarget);
    });
  }

  initializeCheckboxes();

  $(document).ajaxComplete(() => {
    initializeCheckboxes();
  });
})(jQuery);
