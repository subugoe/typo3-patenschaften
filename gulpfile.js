var gulp = require('gulp'),
		sass = require('gulp-sass'),
		notify = require('gulp-notify'),
		scsslint = require('gulp-scss-lint'),
		autoprefixer = require('gulp-autoprefixer'),
		cached = require('gulp-cached');

var config = {
	paths: {
		sass: [
			'./Resources/Private/Scss/**/*.scss',
			'./Resources/Private/Scss/*.scss',
			'!./Resources/Private/Scss/vendors/**/*.scss'
		]
	},
	autoprefixer: {
		browsers: [
			'last 2 versions',
			'safari 6',
			'ie 9',
			'opera 12.1',
			'ios 6',
			'android 4'
		],
		cascade: true
	}
};

gulp.task('sass', function () {
	gulp.src(config.paths.sass)
			.pipe(sass({
				style: 'compressed',
				errLogToConsole: true,
				sourcemaps: true
			}))
			.on('error', notify.onError({
				title: 'Sass Error',
				message: '<%= error.message %>'
			}))
			.pipe(autoprefixer(
					config.autoprefixer
			))
			.pipe(gulp.dest('./Resources/Public/Css/'))
});

gulp.task('lint', function () {
	gulp.src(config.paths.sass)
			.pipe(cached('scsslint'))
			.pipe(scsslint({
				'config': 'build/.scss-lint.yml',
				'maxBuffer': 9999999
			}))
});

gulp.task('compile', function () {
	gulp.start('sass')
});

gulp.task('watch', function () {
	gulp.watch(config.paths.sass, ['lint', 'compile'])
});

gulp.task('default', function () {
	gulp.start('lint', 'compile', 'watch')
});