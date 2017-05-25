'use strict';

var gulp = require('gulp');
var uglify = require('gulp-uglify');
var sass = require('gulp-sass');
var stylus = require('gulp-stylus');
var nib = require('nib');
var concatCss = require('gulp-concat-css');
var cleanCSS = require('gulp-clean-css');
var amdOptimize = require('gulp-amd-optimizer');
var concat = require('gulp-concat');

var fs = require('fs');
var amdOptimize = require('gulp-amd-optimizer');


var bootstrap_sass_conf = {
	cwd : './vendor/twbs/bootstrap-sass/assets/stylesheets',
	dest : './assets/release/bootstrap/css',
	src : [ './assets/package/frontend.scss', './assets/package/login.scss']
}
gulp.task('frontend-sass', function() {
	return gulp.src(bootstrap_sass_conf.src).pipe(sass({
		includePaths : [ bootstrap_sass_conf.cwd ],
		outputStyle : 'compressed'
	}).on('error', sass.logError)).pipe(gulp.dest(bootstrap_sass_conf.dest));
});

var dojo_flat = {
	cwd : './library/dojo/themes/flat',
	dest : './assets/release/dojo/themes/flat'
}
gulp.task('dojo-flat', function() {
	return gulp.src(
		[ '**/*.styl', '**/**/*.styl', '!**/**mixins**.styl', '!**/**variables**.styl' ], {
			cwd : dojo_flat.cwd
		}).pipe(stylus({compress : true, use : [ nib() ], 'include css' : true
	})).pipe(gulp.dest(dojo_flat.cwd));
});

gulp.task('dojo-flat-concact', function() {
	return gulp.src( [
	'**/**/*.css',
	'**/*.css',
	'!**/styles.css', 
	'!**/*_rtl.css', 
	'!**/**/*_rtl.css',
	'!flat_dijit.css',
	'!flat_dijit_rtl.css',
	'!flat.min.tmp.css'
	], {
		cwd : dojo_flat.cwd
	})
	.pipe(concatCss("flat.min.tmp.css"))
	.pipe(cleanCSS({compatibility: 'ie8'}))
	.pipe(gulp.dest(dojo_flat.cwd));
});

gulp.task('dojo-flat-dijit-concact', function() {
	return gulp.src( [
		'flat.min.tmp.css',
	    'flat_dijit.css',
	    'styles/styles.css'
	], {
		cwd : dojo_flat.cwd
	})
	.pipe(concatCss("flat.min.css"))
	.pipe(cleanCSS({compatibility: 'ie8'}))
	.pipe(gulp.dest(dojo_flat.dest));
});


gulp.task('dojo-flat-rtl-concact', function() {
	return gulp.src( [
	'flat_dijit_rtl.css',
	'**/*_rtl.css', 
	'**/**/*_rtl.css',
	], {
		cwd : dojo_flat.cwd
	})
	.pipe(concatCss("flat-rtl.min.css"))
	.pipe(cleanCSS({compatibility: 'ie8'}))
	.pipe(gulp.dest(dojo_flat.dest));
});


gulp.task('pinwin-css', function() {
	return gulp.src(['./assets/package/pinwin/flat-styles.css'])
	.pipe(concatCss("flat-styles.min.css"))
	.pipe(cleanCSS({compatibility: 'ie8'}))
	.pipe(gulp.dest('./assets/release/pinwin'));
});



gulp.task('all', [ 'frontend-sass', 'dojo-flat', 'dojo-flat-concact', 'dojo-flat-dijit-concact', 'dojo-flat-rtl-concact', 'pinwin-css']);

gulp.task('watch', function() {
	
	gulp.watch([
	    './vendor/twbs/bootstrap-sass/assets/stylesheets/bootstrap/**/*.scss', 
	    './vendor/twbs/bootstrap-sass/assets/stylesheets/bootstrap/*.scss'], 
	['frontend-sass']);
	
	gulp.watch(['./library/dojo/themes/flat/**/*.styl', './library/dojo/themes/flat/**/**/*.styl'], ['dojo-flat-concact']);
	
	gulp.watch([
	    './library/dojo/themes/flat/flat.min.tmp.css', 
	    './library/dojo/themes/flat/flat_dijit.css', 
	    './library/dojo/themes/flat/styles/styles.css'
	], ['dojo-flat-dijit-concact']
	);
	
	gulp.watch([
	    '/library/dojo/themes/flat/**/*_rtl.css', 
	    '/library/dojo/themes/flat/**/**/*_rtl.css'
	], ['dojo-flat-rtl-concact']);
	
	gulp.watch(['./assets/package/pinwin/flat-styles.css'], ['pinwin-css']);
});
