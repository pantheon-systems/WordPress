/**
 * @file gulpfile.js
 *
 * Defines tasks that can be run on gulp.
 *
 * Summary:
 * - `test` - run `test` with mocha
 * - `coverage` - run `istanbul` with mocha to generate a report of test coverage
 * - `coveralls` - updates coveralls info
 */
'use strict';

var gulp = require('gulp');
var coveralls = require('gulp-coveralls');
var mocha = require('gulp-mocha');
var runSequence = require('run-sequence');
var shell = require('gulp-shell');

var tests = ['test/**/*.js'];

var testMocha = function() {
  return gulp.src(tests).pipe(new mocha({
    reporter: 'spec'
  }));
};

gulp.task('test:node', testMocha);

gulp.task('test', function(callback) {
  runSequence(['test:node'], callback);
});

gulp.task('coverage', shell.task(['node_modules/.bin/./istanbul cover node_modules/.bin/_mocha -- --recursive']));

gulp.task('coveralls', ['coverage'], function() {
  gulp.src('coverage/lcov.info').pipe(coveralls());
});
