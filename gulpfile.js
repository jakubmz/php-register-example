'use strict'
var gulp = require('gulp');
var sass = require('gulp-sass');

sass.compiler = require('node-sass');

gulp.task('sass', function () {
  return new Promise((resolve, reject) => {
    gulp.src('src/style.sass')
      .pipe(sass().on('error', sass.logError))
      .pipe(gulp.dest('src'));
    resolve();
    });
});

gulp.task('watch', function () {
  return new Promise((resolve, reject) => {
    gulp.watch('./src/**/*.sass', ['sass']);
    resolve()
  });
});
