/**
 * Compiler for styles, JS, images and fonts.
 */

// ---------------
// General plugins
// ---------------
const gulp = require("gulp");
const log = require("fancy-log");
const noop = require("gulp-noop");
const rename = require("gulp-rename");
const clean = require("del");

// ------------
// Sass plugins
// ------------
const sass = require("gulp-sass");
const sassGlob = require("gulp-sass-glob");
const autoprefix = require("gulp-autoprefixer");
const cleanCss = require("gulp-clean-css");

// ----------------------------
// Javascript plugins
// ----------------------------
const uglify = require("gulp-uglify-es").default;

// ------
// Config
// ------
const basePath = {
  src: "./src/",
  dist: "./dist/",
  templates: "./templates/"
};

const path = {
  fonts: {
    src: `${basePath.src}fonts/`,
    dist: `${basePath.dist}fonts/`
  },
  styles: {
    src: `${basePath.src}scss/`,
    dist: `${basePath.dist}css/`
  },
  scripts: {
    src: `${basePath.src}js/`,
    dist: `${basePath.dist}js/`
  },
  images: {
    src: `${basePath.src}images/`,
    dist: `${basePath.dist}images/`
  },
  templates: {
    dist: basePath.templates
  },
  node_modules: "node_modules/"
};

const sassConfig = {
  outputStyle: "expanded",
  includePaths: [
    "node_modules/node-normalize-scss/",
    "node_modules/breakpoint-sass/stylesheets/"
  ]
};

// -------------
// Compile SASS.
// -------------
function compileSASS() {
  return gulp
    .src(`${path.styles.src}**/*.scss`)
    .pipe(sassGlob())
    .pipe(
      path.env === "development"
        ? sass(sassConfig).on("error", function(err) {
            const chalk = require("chalk");
            log.error(
              chalk.black.bgRed(
                " SASS ERROR",
                chalk.white.bgBlack(` ${err.message.split("  ")[2]} `)
              )
            );
            log.error(
              chalk.black.bgRed(
                " FILE:",
                chalk.white.bgBlack(` ${err.message.split("\n")[0]} `)
              )
            );
            log.error(
              chalk.black.bgRed(" LINE:", chalk.white.bgBlack(` ${err.line} `))
            );
            log.error(
              chalk.black.bgRed(
                " COLUMN:",
                chalk.white.bgBlack(` ${err.column} `)
              )
            );
            log.error(
              chalk.black.bgRed(
                " ERROR:",
                chalk.white.bgBlack(` ${err.formatted.split("\n")[0]} `)
              )
            );
            return this.emit("end");
          })
        : sass(sassConfig)
    )
    .pipe(autoprefix())
    .pipe(path.env === "production" ? cleanCss() : noop())
    .pipe(gulp.dest(path.styles.dist));
}

// ---------------------------------------------------
// Copy fonts from src to dist if necessary.
// ---------------------------------------------------
function copyFonts() {
  return gulp.src(`${path.fonts.src}**/*`).pipe(gulp.dest(path.fonts.dist));
}

// ---------------------------------------------------
// Copy js from src to dist and uglify if necessary.
// ---------------------------------------------------
function copyScripts() {
  return gulp
    .src(`${path.scripts.src}**/*.js`)
    .pipe(path.env === "production" ? uglify() : noop())
    .pipe(
      rename({
        suffix: ".min"
      })
    )
    .pipe(gulp.dest(path.scripts.dist));
}

// ---------------------------------------------------
// Copy images from src to dist if necessary.
// ---------------------------------------------------
function copyImages() {
  return gulp.src(`${path.images.src}**/*`).pipe(gulp.dest(path.images.dist));
}

// ----------------------
// Function to run watch.
// ----------------------
function runWatch() {
  gulp.watch(`${path.styles.src}**/*.scss`, compileSASS);
  gulp.watch(`${path.scripts.src}**/*.js`, copyScripts);
}

// -----------
// Watch task.
// -----------
gulp.task("watch", gulp.series(runWatch));

// -----------------------
// Function to clean dist.
// -----------------------
function cleandist() {
  console.log("Clean all files in dist folder");
  return clean(["./dist/"]);
}

// ------------------------------------------
// Helper function for selecting environment.
// ------------------------------------------
function environment(env) {
  console.log(`Running tasks in ${env} mode.`);
  return (path.env = env);
}

// --------------------------------------
// Set environment variable via dev task.
// --------------------------------------
gulp.task("dev", done => {
  environment("development");
  done();
});

// ---------------------------------------
// Set environment variable via prod task.
// ---------------------------------------
gulp.task("prod", done => {
  environment("production");
  done();
});

// -------------------------------------
// Build development css & scripts task.
// -------------------------------------
gulp.task(
  "development",
  gulp.series("dev", cleandist, compileSASS, copyFonts, copyScripts),
  done => {
    done();
  }
);

// -------------
// Default task.
// -------------
gulp.task("default", gulp.series("dev", gulp.parallel("watch")), done => {
  done();
});

// ----------------
// Production task.
// ----------------
gulp.task(
  "production",
  gulp.series(
    "prod",
    cleandist,
    compileSASS,
    gulp.parallel(copyFonts, copyScripts)
  ),
  done => {
    done();
  }
);

// -----------
// SVG sprites
// -----------
const svgSprite = require("gulp-svg-sprite");
const plumber = require("gulp-plumber");
const filenames = require("gulp-filenames-to-json");

const svgBaseDir = "icons/svg";
const svgGlob = "**/*.svg";
const svgOutDir = "icons";

const svgSpriteConfig = {
  log: "info",
  shape: {
    // Set maximum dimensions
    dimension: {
      maxWidth: 64,
      maxHeight: 64
    },
    // Add padding
    spacing: {
      padding: 0
    }
  },
  mode: {
    symbol: {
      render: {
        css: false,
        scss: {
          dest: "icons.scss"
        }
      },
      example: true,
      dest: "./",
      prefix: ".icon--%s",
      sprite: "icons.svg"
    }
  },
  svg: {
    xmlDeclaration: false, // strip out the XML attribute
    doctypeDeclaration: false // don't include the !DOCTYPE declaration
  }
};

// ---------------------------------
// Copy Suomi.fi icons to icons src.
// ---------------------------------
function copySuomiFiIcons() {
  return gulp
    .src(`${svgOutDir}/suomifi-icons/src/icons/**/*`)
    .pipe(rename((opt) => {
        opt.basename = opt.basename.replace(/^icon-/, "");
        return opt;
      })
    )
    .pipe(gulp.dest(`${svgOutDir}/src/`));
}

// ----------------------------------------
// Copy Suomi.fi static icons to icons src.
// ----------------------------------------
function copySuomiFiStaticIcons() {
  return gulp
    .src(`${svgOutDir}/suomifi-icons/src/staticIcons/**/*`)
    .pipe(rename((opt) => {
        opt.basename = opt.basename.replace(/^icon-/, "");
        return opt;
      })
    )
    .pipe(gulp.dest(`${svgOutDir}/src/`));
}

// ------------------------------------
// Copy external icons from src to svg.
// ------------------------------------
function copyExternalIcons() {
  return gulp
    .src(`${svgOutDir}/src/**/*`)
    .pipe(rename((opt) => {
        opt.basename = opt.basename.replace(/^icon-/, "");
        return opt;
      })
    )
    .pipe(gulp.dest(`${svgOutDir}/svg/`));
}

// ------------------------
// Override modified icons.
// ------------------------
function copyOverriddenIcons() {
  return gulp
    .src(`${svgOutDir}/overridden/**/*`)
    .pipe(rename((opt) => {
        opt.basename = opt.basename.replace(/^icon-/, "");
        return opt;
      })
    )
    .pipe(gulp.dest(`${svgOutDir}/svg/`));
}

// ----------------------------------
// Save SVG filenames to a JSON file.
// ----------------------------------
gulp.task("svgNamesToJson", () => {
  const filename = {
    fileName: "icons.json"
  };
  return gulp
    .src(svgGlob, { cwd: svgBaseDir })
    .pipe(plumber())
    .pipe(filenames(filename))
    .pipe(gulp.dest(svgOutDir));
});

// ----------------
// Clean SVG sprite
// ----------------
function cleanSVGSprite() {
  console.log("Clean sprites in icons folder");
  return clean(["./icons/icons.*"]);
}

// ----------------
// SVG sprite task.
// ----------------
gulp.task(
  "svgSprite",
  gulp.series(
    cleanSVGSprite,
    copySuomiFiIcons,
    copySuomiFiStaticIcons,
    copyExternalIcons,
    copyOverriddenIcons,
    "svgNamesToJson",
    () =>
      gulp
        .src(svgGlob, { cwd: svgBaseDir })
        .pipe(plumber())
        .pipe(svgSprite(svgSpriteConfig))
        .on("error", error => {
          console.log(error);
        })
        .pipe(gulp.dest(svgOutDir))
  )
);
