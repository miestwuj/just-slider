var gulp = require('gulp');

// Include plugins.
var rename 			= require("gulp-rename"),
	sass 			= require('gulp-sass'),
	cleanCSS 		= require('gulp-clean-css'),
	autoprefixer 	= require('gulp-autoprefixer'),
	wpPot 			= require('gulp-wp-pot'),
    uglify			= require('gulp-uglify'),
    gulpif			= require('gulp-if');

gulp.task('css', function(){
	return gulp.src('./scss/**/*')
	.pipe(sass().on('error', sass.logError))
	.pipe(autoprefixer({
		browsers: ['last 2 versions'],
		cascade: false
	}))
	.pipe(rename({suffix: '.min'}))
	.pipe(cleanCSS())
	.pipe(gulp.dest('./assets'));
});

gulp.task('js', function () {
    gulp.src('./assets/src/*.js')
		.pipe(gulpif(global.prod,uglify()))
		.pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest('./assets/js'));
});

gulp.task('pot', function () {
    return gulp.src('./**/*.php')
        .pipe(wpPot( {
            domain: 'just-slider',
            package: 'Just_Slider'
        } ))
        .pipe(gulp.dest('./languages/just-slider.pot'));
});

gulp.task('default', ['css'], function() {
	gulp.watch( './scss/**/*', ['css'] );
});

gulp.task('build', ['css','pot','js'], function() {
});