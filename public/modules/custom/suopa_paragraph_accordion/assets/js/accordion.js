/* eslint-env jquery */

(($) => {
  function addAccordionClasses() {
    const containers = document.querySelectorAll('[id^="field-paragraphs"]');

    for (let i = 0; i < containers.length; i++) {
      const panels = containers[i].getElementsByClassName('paragraph-accordion-panel');
      if (panels.length > 0 && containers[i].id.indexOf('-item-wrapper') >= 0) {
        containers[i].parentElement.classList.add('paragraph-accordion');
      }
    }
  }

  function mapToPanel(e) {
    // Only proceed if srcElement is not child of the accordion panel
    if ($(e.target).parents('.paragraph-accordion-panel').length === 0) {
      const $paragraphButtons = $('.paragraph-accordion');
      const $paragraphPanels = $('.paragraph-accordion-panel');

      const index = $paragraphButtons.index(this);

      // Toggle visibility of corresponding panel element
      $paragraphPanels.eq(index).toggle();
    }
  }

  /**
   * Map paragraphs
   * Add appropriate classes to elements in accordion
   */
  function mapParagraphs() {
    addAccordionClasses();

    const panels = document.getElementsByClassName('paragraph-accordion');

    $('.paragraph-accordion').each((index) => {
      panels[index].removeEventListener('click', mapToPanel, true);
      panels[index].addEventListener('click', mapToPanel);
    });
  }

  mapParagraphs();

  $(document).ajaxComplete(() => {
    mapParagraphs();
  });

  // remap paragraphs only when mouse clicks
  $('body').on({
    mouseup: () => {
      // "instant" timeout to wait for the event loop to clear
      // https://stackoverflow.com/a/4575011/8857465
      setTimeout(mapParagraphs, 0);
    },
  });
})(jQuery);
