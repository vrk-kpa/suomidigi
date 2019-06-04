/* eslint-env jquery */

(($) => {
  function addAccordionClasses() {
    const containers = document.querySelectorAll('[id^="field-paragraphs"]');

    // console.log(childParagraph);

    for (let i = 0; i < containers.length; i++) {
      const panels = containers[i].getElementsByClassName('paragraph-accordion-panel');
      if (panels.length > 0 && containers[i].id.indexOf('-item-wrapper') >= 0) {
        containers[i].parentElement.classList.add('paragraph-accordion');

        const childParagraphs = document.querySelectorAll('.paragraph-accordion .field-multiple-table');

        [...childParagraphs].map(e => {
          const childDiv = e.querySelectorAll('td > div');

          [...childDiv].map(j => {
            if (j.id.indexOf('-item-wrapper') >= 0) {
              j.parentElement.classList.add('paragraph-accordion');
            }
          });
        });
      }
    }
  }

  function mapToPanel(e) {
    // Only proceed if srcElement is related to the clicked target.
    if ($(e.target).parent().siblings('.paragraph-accordion-panel').length === 1) {
      const $paragraphButtons = $('.paragraph-accordion');
      const $paragraphPanels = $('.paragraph-accordion-panel');

      const index = $paragraphButtons.index(this);

      if (index > 0) {
        e.stopPropagation();
      }

      // Toggle visibility of corresponding panel element.
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
