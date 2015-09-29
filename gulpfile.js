var gulp = require('gulp'),
 inject = require('gulp-inject');
 var browserSync = require('browser-sync').create();

 // Static server
 gulp.task('serve', function() {
     browserSync.init({
         server: {
             baseDir: "./app"
         }
     });
 });
 gulp.task('index', function () {
  var target = gulp.src('./app/index.html');
  // It's not necessary to read the files (will speed up things), we're only after their paths:
  var sources = gulp.src(['./app/js/*.js', './app/css/*.css', './app/fa/css/*.css'], {read: false});
  return target.pipe(inject(sources,{relative: true}))
    .pipe(gulp.dest('./app'));
});
