var gulp = require('gulp');
var sass = require('gulp-sass');
var notify = require('gulp-notify');
var plumber = require('gulp-plumber');
var livereload = require('gulp-livereload');

// options
var sassOptions = {
  errLogToConsole: true,
  outputStyle: 'compressed'
};

//error notification settings for plumber
var plumberErrorHandler = { errorHandler: notify.onError({
    title: 'Gulp',
    message: 'Error: <%= error.message %>'
  })
};

// Reload the whole page
var livereloadPage = function () {
   livereload.reload();
 };

gulp.task('sass', function () {
  gulp.src('./assets/scss/**/*.scss')
    .pipe(plumber(plumberErrorHandler))
    .pipe(sass(sassOptions))
    .pipe(gulp.dest('./assets/css/'))
    .pipe(livereload());
});

// watch and live reload
gulp.task('watch', function() {
  livereload.listen();
  gulp.watch('./assets/scss/*.scss', ['sass']);
  gulp.watch('./assets/scss/**/*.scss', ['sass']);

});

gulp.task('default', ['sass', 'watch']);