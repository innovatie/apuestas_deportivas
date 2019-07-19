const gulp = require('gulp'),
    watch = require('gulp-watch'),
    sass = require('gulp-sass'),
    sourcemaps = require('gulp-sourcemaps'),
    cleanCSS = require('gulp-clean-css'),
    prefix = require('gulp-autoprefixer'),
    babel = require('gulp-babel'),
    minify = require('gulp-babel-minify'),
    browserify = require('browserify'),
    buffer = require('vinyl-buffer'),
    source = require('vinyl-source-stream'),
    plumber = require('gulp-plumber');

/* ----------------------------- CSS tasks ----------------------------- */

gulp.task('sass', () => {
    return gulp
    .src('./sass/style.scss')
    .pipe(sourcemaps.init())
    .pipe(sass().on('error', sass.logError))
    .pipe(prefix({
        browsers: ['last 2 versions', 'chrome 28'],
    }))
    .pipe(cleanCSS({ advanced: false, agressiveMerging: false, compatibility: '*' }))
    .pipe(sourcemaps.write('.'))
    .pipe(gulp.dest('./'));
});

/* ----------------------------- JS tasks ----------------------------- */

const bundler = browserify({
    /* Browserify options */
    entries: ['./js/dev-js/theme.js'], /* source file */
    debug: true,
});

const bundle = function () {
    return bundler
    .bundle()
    .on('error', (e) => {
        console.log(`Encountered error ${e}`);
    })
    .pipe(source('theme.js')) /* destination file */ /* gives streaming vinyl file object */
    .pipe(buffer()) /* <----- convert from streaming to buffered vinyl file object */
    .pipe(sourcemaps.init({ loadMaps: true })) /* show true origin of an error */
    .pipe(babel({
        presets: ["@babel/preset-env"],
    }))
    .pipe(minify({
        mangle: {
            keepClassName: true,
        },
    }))
    .pipe(sourcemaps.write('./maps/'))
    .pipe(gulp.dest('./js')); /* destination folder */
};

gulp.task('browserify', () => {
    return bundle();
});


/* ----------------------------- WATCH, DEFAULT ----------------------------- */

gulp.task('watch', () => {
    gulp.watch(['./sass/**/*.scss'], gulp.series('sass'));
    gulp.watch(['./js/dev-js/*.js'], gulp.series('browserify'));
});

//gulp.task('update', ['sass', 'browserify']);
gulp.task('update', gulp.series(gulp.parallel('sass', 'browserify')));

//gulp.task('default', ['watch']); // Default will run the 'entry' watch task
gulp.task('default', gulp.series(gulp.parallel('watch')));

