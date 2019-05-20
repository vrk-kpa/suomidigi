/**
 * Compiler for styles, JS, images and fonts.
 */

// ---------------
// General plugins
// ---------------
var chalk = require('chalk');
var colors = require('ansi-colors');
var gulp = require('gulp');
var log = require('fancy-log');
var noop = require('gulp-noop');
var rename = require('gulp-rename');
var clean = require('gulp-rimraf');

// ------------
// Sass plugins
// ------------
var sass = require('gulp-sass');
var sassGlob = require('gulp-sass-glob');
var autoprefix = require('gulp-autoprefixer');
var cleanCss = require('gulp-clean-css');

// ----------------------------
// Javascript plugins
// ----------------------------
var uglify = require('gulp-uglify');

// ------
// Config
// ------
var basePath = {
  src: './src/',
  dist: './dist/',
  templates: './templates/',
};

var path = {
  fonts: {
    src: basePath.src + 'fonts/',
    dist: basePath.dist + 'fonts/',
  },
  styles: {
    src: basePath.src + 'scss/',
    dist: basePath.dist + 'css/',
  },
  scripts: {
    src: basePath.src + 'js/',
    dist: basePath.dist + 'js/',
  },
  images: {
    src: basePath.src + 'images/',
    dist: basePath.dist + 'images/',
  },
  templates: {
    dist: basePath.templates,
  },
  node_modules: 'node_modules/',
}

var changeEvent = function(evt) {
  log('File', colors.cyan(evt.path.replace(new RegExp('/.*(?=/' + basePath.src + ')/'), '')), 'was', colors.magenta(evt.type));
};

// -------------
// Compile SASS.
// -------------
function compileSASS() {
  return gulp.src(path.styles.src + '**/*.scss')
      .pipe(sassGlob())
      .pipe(
          path.env === 'development' ?
              sass({
                includePaths: [
                  './',
                  require('node-normalize-scss').includePaths
                ],
                outputStyle: 'expanded'
              })
                  .on("error", function (err) {
                    log.error(chalk.black.bgRed(" SASS ERROR", chalk.white.bgBlack(" " + (err.message.split("  ")[2]) + " ")));
                    log.error(chalk.black.bgRed(" FILE:", chalk.white.bgBlack(" " + (err.message.split("\n")[0]) + " ")));
                    log.error(chalk.black.bgRed(" LINE:", chalk.white.bgBlack(" " + err.line + " ")));
                    log.error(chalk.black.bgRed(" COLUMN:", chalk.white.bgBlack(" " + err.column + " ")));
                    log.error(chalk.black.bgRed(" ERROR:", chalk.white.bgBlack(" " + err.formatted.split("\n")[0] + " ")));
                    return this.emit("end");
                  }) :
              sass({
                includePaths: [
                  './',
                  require('node-normalize-scss').includePaths
                ],
                outputStyle: 'expanded'
              })
      )
      .pipe(autoprefix({
        browsers: ['last 2 versions']
      }))
      .pipe(path.env === "production" ? cleanCss() : noop())
      .pipe(gulp.dest(path.styles.dist));
}

// -----------
// Watch task.
// -----------
gulp.task('watch', gulp.series(runWatch));

// ---------------------------------------------------
// Copy fonts from src to dist if necessary.
// ---------------------------------------------------
function copyFonts() {
  return gulp.src(path.fonts.src + '**/*')
      .pipe(gulp.dest(path.fonts.dist));
}

// ---------------------------------------------------
// Copy js from src to dist and uglify if necessary.
// ---------------------------------------------------
function copyScripts() {
  return gulp.src(path.scripts.src + '**/*.js')
      .pipe(path.env === "production" ? uglify() : noop())
      .pipe(rename({
        suffix: '.min'
      }))
      .pipe(gulp.dest(path.scripts.dist));
}

// ---------------------------------------------------
// Copy images from src to dist if necessary.
// ---------------------------------------------------
function copyImages() {
  return gulp.src(path.images.src + '**/*')
      .pipe(gulp.dest(path.images.dist));
}

// ----------------------
// Function to run watch.
// ----------------------
function runWatch() {
  gulp.watch(path.styles.src + '**/*.scss', compileSASS);
  gulp.watch(path.scripts.src + '**/*.js', copyScripts);
}

function cleandist() {
  allowEmpty = true;
  console.log("Clean all files in dist folder");

  return gulp.src('./dist/', { read: false, allowEmpty: true }).pipe(clean());
};

// --------------------------------------
// Set environment variable via dev task.
// --------------------------------------
gulp.task('dev', function(done) {
  environment("development");
  done();
});

// ---------------------------------------
// Set environment variable via prod task.
// ---------------------------------------
gulp.task('prod', function(done) {
  environment("production");
  done();
});

// -------------------------------------
// Build development css & scripts task.
// -------------------------------------
gulp.task('development', gulp.series('dev', cleandist, compileSASS, copyFonts, copyScripts, copyImages), function(done) {
  done();
});

// -------------
// Default task.
// -------------
gulp.task('default', gulp.series('dev', gulp.parallel('watch')), function(done) {
  done();
});

// ----------------
// Production task.
// ----------------
gulp.task('production', gulp.series('prod', cleandist, compileSASS, gulp.parallel(copyFonts, copyScripts, copyImages)), function(done) {
  done();
});


// ------------------------------------------
// Helper function for selecting environment.
// ------------------------------------------
function environment(env) {
  console.log('Running tasks in ' + env + ' mode.');
  return path.env = env;
}
