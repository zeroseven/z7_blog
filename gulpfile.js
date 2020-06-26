'use strict';

const gulp = require('gulp');
const uglify = require('gulp-uglify');
const babel = require('gulp-babel');
const rename = require('gulp-rename');

gulp.task('BuildFrontend', done => {

  // Define the file paths
  gulp.src(['./Resources/Private/JavaScript/Frontend/**/*.js'])

    // Make compatibility to older browser versions
    .pipe(babel({
      presets: ['@babel/preset-env']
    }))

    // Beautify the code for debugging (only)
    .pipe(uglify({
      compress: false,
      mangle: false,
      output: {
        beautify: true
      }
    }))

    // Save a uncompressed file
    .pipe(rename({suffix: '.dist'}))
    .pipe(gulp.dest('./Resources/Public/JavaScript/Frontend/'))

    // Save the minified JavaScript
    .pipe(uglify())
    .pipe(rename({suffix: '.min'}))
    .pipe(gulp.dest('./Resources/Public/JavaScript/Frontend/'));

  done();
});

gulp.task('BuildBackend', done => {

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

gulp.task('build', gulp.series('BuildBackend', 'BuildFrontend'));

gulp.task('watch', () => {
  gulp.watch(['./Resources/Private/JavaScript/**/*.js'], gulp.series('BuildFrontend'));
});

gulp.task('default', gulp.series('build', 'watch'));
