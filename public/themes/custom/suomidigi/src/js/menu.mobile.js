(function($, Drupal, drupalSettings) {
  const siteName = drupalSettings.siteName;
  const siteSlogan = drupalSettings.siteSlogan;

  const menu = $("#menu--mobile")
    .mmenu(
      {
        slidingSubmenus: false,
        extensions: [
          "pagedim-black",
          "shadow-panels",
          "position-left",
          "border-none"
        ],
        pageScroll: true,
        navbar: false,
        navbars: [
          {
            height: "134px",
            position: "top",
            content: [
              `${'<header class="page-header__mobile"><a href="/" class="logo logo--header" title="Home" rel="home">\n' +
                '    <svg viewBox="0 0 55 55" width="55px" height="55px" aria-labelledby="title" id="suomidigi_flag" role="img" focusable="false">\n' +
                '      <title id="title">'}${siteName} logo</title>\n` +
                `      <desc id="desc">suomidigi.fi</desc>\n` +
                `      <g class="icon--flag">\n` +
                `        <path fill="#003479" d="M53,0H2C0.9,0,0,0.9,0,2v51c0,1.1,0.9,2,2,2h51c1.1,0,2-0.9,2-2V2C55,0.9,54.1,0,53,0z"></path>\n` +
                `        <path fill="#FFFFFF" d="M14,20v-5c0-1.1,0.9-2,2-2h5v7"></path>\n` +
                `        <path fill="#FFFFFF" d="M14,27h7v14c0,0.5-0.4,1-1,1h-5c-0.6,0-1-0.5-1-1"></path>\n` +
                `        <path fill="#FFFFFF" d="M28,13h13c0.5,0,1,0.4,1,1v6H28"></path>\n` +
                `        <path fill="#FFFFFF" d="M41,34H28v-7h14v6C42,33.6,41.6,34,41,34z"></path>\n` +
                `      </g>\n` +
                `    </svg>\n` +
                `    <span class="logo--text">${siteName}</span>\n` +
                `  </a>` +
                `<p class"site-slogan">${siteSlogan}</p></header>`
            ]
          }
        ]
      },
      {
        // configuration
        classNames: {
          selected: "is-active",
          vertical: "expand"
        }
      }
    )
    .data("mmenu");

  Drupal.behaviors.mmenu = {
    attach(context, settings) {
      $("#header").mhead();
    },
    close() {
      menu.close();
    }
  };
})(jQuery, Drupal, drupalSettings);
