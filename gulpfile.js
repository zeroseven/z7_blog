'use strict';

const gulp = require('gulp');
const rename = require('gulp-rename');

gulp.task('BackendSources', done => {

  // Add Tagify library
  gulp.src('./node_modules/@yaireo/tagify/dist/tagify.min.js')
    .pipe(rename('Tagify.js'))
    .pipe(gulp.dest('./Resources/Public/JavaScript/Backend'));

  // Add Tagify library
  gulp.src('./node_modules/@yaireo/tagify/dist/tagify.css')
    .pipe(rename('Tagify.css'))
    .pipe(gulp.dest('./Resources/Public/Css/Backend'));

  done();
});

gulp.task('build', gulp.series('BackendSources'));

gulp.task('watch', () => {
  gulp.watch(['./Resources/Private/JavaScript/**/*.js'], gulp.series('build'));
});

gulp.task('default', gulp.series('build', 'watch'));
