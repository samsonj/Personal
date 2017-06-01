// ===========================================================================================================
// Gulp config file for AKA General theme
// Author: AKA Digital      
// Version: 1.0
// ===========================================================================================================

//   ██████╗  ██████╗     ███╗   ██╗ ██████╗ ████████╗    ███████╗██████╗ ██╗████████╗
//   ██╔══██╗██╔═══██╗    ████╗  ██║██╔═══██╗╚══██╔══╝    ██╔════╝██╔══██╗██║╚══██╔══╝
//   ██║  ██║██║   ██║    ██╔██╗ ██║██║   ██║   ██║       █████╗  ██║  ██║██║   ██║
//   ██║  ██║██║   ██║    ██║╚██╗██║██║   ██║   ██║       ██╔══╝  ██║  ██║██║   ██║
//   ██████╔╝╚██████╔╝    ██║ ╚████║╚██████╔╝   ██║       ███████╗██████╔╝██║   ██║
//   ╚═════╝  ╚═════╝     ╚═╝  ╚═══╝ ╚═════╝    ╚═╝       ╚══════╝╚═════╝ ╚═╝   ╚═╝
//
//   ██╗   ██╗███╗   ██╗██╗     ███████╗███████╗███████╗    ██╗   ██╗ ██████╗ ██╗   ██╗
//   ██║   ██║████╗  ██║██║     ██╔════╝██╔════╝██╔════╝    ╚██╗ ██╔╝██╔═══██╗██║   ██║
//   ██║   ██║██╔██╗ ██║██║     █████╗  ███████╗███████╗     ╚████╔╝ ██║   ██║██║   ██║
//   ██║   ██║██║╚██╗██║██║     ██╔══╝  ╚════██║╚════██║      ╚██╔╝  ██║   ██║██║   ██║
//   ╚██████╔╝██║ ╚████║███████╗███████╗███████║███████║       ██║   ╚██████╔╝╚██████╔╝
//    ╚═════╝ ╚═╝  ╚═══╝╚══════╝╚══════╝╚══════╝╚══════╝       ╚═╝    ╚═════╝  ╚═════╝
//
//   ██╗  ██╗███╗   ██╗ ██████╗ ██╗    ██╗    ██╗    ██╗██╗  ██╗ █████╗ ████████╗
//   ██║ ██╔╝████╗  ██║██╔═══██╗██║    ██║    ██║    ██║██║  ██║██╔══██╗╚══██╔══╝
//   █████╔╝ ██╔██╗ ██║██║   ██║██║ █╗ ██║    ██║ █╗ ██║███████║███████║   ██║
//   ██╔═██╗ ██║╚██╗██║██║   ██║██║███╗██║    ██║███╗██║██╔══██║██╔══██║   ██║
//   ██║  ██╗██║ ╚████║╚██████╔╝╚███╔███╔╝    ╚███╔███╔╝██║  ██║██║  ██║   ██║
//   ╚═╝  ╚═╝╚═╝  ╚═══╝ ╚═════╝  ╚══╝╚══╝      ╚══╝╚══╝ ╚═╝  ╚═╝╚═╝  ╚═╝   ╚═╝
//
//   ██╗   ██╗ ██████╗ ██╗   ██╗     █████╗ ██████╗ ███████╗    ██████╗  ██████╗ ██╗███╗   ██╗ ██████╗
//   ╚██╗ ██╔╝██╔═══██╗██║   ██║    ██╔══██╗██╔══██╗██╔════╝    ██╔══██╗██╔═══██╗██║████╗  ██║██╔════╝
//    ╚████╔╝ ██║   ██║██║   ██║    ███████║██████╔╝█████╗      ██║  ██║██║   ██║██║██╔██╗ ██║██║  ███╗
//     ╚██╔╝  ██║   ██║██║   ██║    ██╔══██║██╔══██╗██╔══╝      ██║  ██║██║   ██║██║██║╚██╗██║██║   ██║
//      ██║   ╚██████╔╝╚██████╔╝    ██║  ██║██║  ██║███████╗    ██████╔╝╚██████╔╝██║██║ ╚████║╚██████╔╝
//      ╚═╝    ╚═════╝  ╚═════╝     ╚═╝  ╚═╝╚═╝  ╚═╝╚══════╝    ╚═════╝  ╚═════╝ ╚═╝╚═╝  ╚═══╝ ╚═════╝

// ===========================================================================================================
// VARIABLES
// ===========================================================================================================

// Global File paths

var config = {
	bowerPath:      'bower_components/',
	bowerPathAll:   'bower_components/**/*',
	jsPath:         'library/js',
	jsLibPath:      'library/js/libs',
	jsPathAll:      'library/js/*.js',
	temp:           'library/temp',
	scssPath:       'library/scss',
	scssPathAll:    'library/scss/*.scss',
	imgPath:        'build/images',
	destImg:        'build/images/*.{gif,png,jpg,jpeg,svg}',
	dest:           'build',
	destCss:        'build/css',
	destJs:         'build/js'
};

// Add the JS files here
var jsFileList = [
   // config.bowerPath + 'bootstrap-sass/assets/javascripts/bootstrap.js',
    // config.bowerPath + 'lightgallery/dist/js/lightgallery.js',
    // config.bowerPath + 'lightgallery/dist/js/lg-thumbnail.js',
    // config.bowerPath + 'lightgallery/dist/js/lg-fullscreen.js',
    // config.bowerPath + 'lightgallery/dist/js/lg-video.js',
    // config.jsLibPath + '/enquire.min.js',  
	config.jsLibPath + '/header.js',     
	//config.jsLibPath + '/imagesloaded.js',  
	config.jsLibPath + '/isotope.js',   
	//config.jsLibPath + '/jquery-bgstretcher-3.0.2.min.js',
	//config.jsLibPath + '/jquery-noconflict.js',
	// config.jsLibPath + '/jquery.easing.1.3',
	// config.jsLibPath + '/jquery.mCustomScrollbar.concat.min.js',
	// config.jsLibPath + '/jquery.selectbox-0.2.min.js',
	// config.jsLibPath + '/jquery.stellar.min.js',
	// config.jsLibPath + '/masonry.pkgd.js',  
	config.jsLibPath + '/mobile-custom.js',  
	config.jsLibPath + '/modernizr.min.js',
	config.jsLibPath + '/page-about.js',  
	config.jsLibPath + '/page-body.js',
	config.jsLibPath + '/retina.js',         
	//config.jsLibPath + '/jquery-1.9.1.min.js'
	config.jsPath 	 + '/scripts.js'     
];

// Styles paths

var scssFilePaths = [
    config.bowerPath + 'bootstrap-sass/assets/stylesheets/',
    config.bowerPath + 'components-font-awesome/scss/',
    config.bowerPath + 'lightgallery/dist/css/'
];

// Load the Gulp plugins

var gulp = require('gulp'); // Load the Gulp core
var runSequence = require('run-sequence'); // Load as this isn't gulp based
var buster = require('gulp-asset-hash'); // Load as this didn't work :P

// Load all the plugins by referring to package.json
   
var gulpLoadPlugins = require('gulp-load-plugins');
var plugins = gulpLoadPlugins();


// ===========================================================================================================
// TASKS
//
// Sequencing became necessary because we only want to lint scripts.js (not every script!)
// Also, we want to fold Modernizr into the concatenated script file for deployment
//
// If there is a better solution then fill your boots
// ===========================================================================================================

gulp.task('default', function() {
	runSequence('bower', 'bower-files', 'modernizr', 'lint', 'scripts', 'styles');
});

// Bower task

gulp.task('bower', function() { 
    return plugins.bower()
         .pipe(gulp.dest(config.bowerPath)) 
});

// Copy assets files from bower components in public/assets/
gulp.task('bower-files', ['bootstrap', 'fontawesome', 'lightgallery-fonts', 'lightgallery-img']);

// Copy bootstrap fonts in destination dir
gulp.task('bootstrap', function () {
    return gulp.src(config.bowerPath + 'bootstrap-sass/assets/fonts/**/**.*')
        .pipe(gulp.dest(config.dest + '/fonts/'))
});

// Copy fontawesome fonts in destination dir
gulp.task('fontawesome', function () {
    return gulp.src(config.bowerPath + 'components-font-awesome/fonts/**/**.*')
        .pipe(gulp.dest(config.dest + '/fonts/'))
});

// Copy lightgallery fonts in destination dir
gulp.task('lightgallery-fonts', function () {
    return gulp.src(config.bowerPath + 'lightgallery/dist/fonts/**/**.*')
        .pipe(gulp.dest(config.dest + '/fonts/'))
});

// Copy lightgallery images in destination dir
gulp.task('lightgallery-img', function () {
    return gulp.src(config.bowerPath + 'lightgallery/dist/img/**/**.*')
        .pipe(gulp.dest(config.dest + '/images/'))
});

// Styles task

gulp.task('styles', function () {
	return gulp.src([config.scssPath + '/styles.scss']) // Base scss include
		.pipe(plugins.plumber(function(error) {
			errorHandler:reportError
		}))
		.pipe(plugins.sass({
			outputstyle: 'compressed',  
            includePaths: scssFilePaths
		}))
		.on('error', reportError)
		.pipe(plugins.autoprefixer('last 2 version', 'safari 5', 'ie 8', 'ie 9', 'opera 12.1', 'ios 6', 'android 4'))
		.pipe(plugins.rename('styles.min.css'))
		//.pipe(plugins.bless())
		//.pipe(plugins.combineMq())
		.pipe(plugins.minifyCss({
			keepSpecialComments : 0
		}))
		.pipe(gulp.dest(config.destCss))
		.pipe(buster.hash({
			manifest: './build/manifest.json',
			template: '<%= name %>.<%= ext %>'
		}))
		.pipe(gulp.dest(config.destCss))  
});

// Scripts task

gulp.task('scripts', function () {
	return gulp.src(jsFileList)
		.pipe(plugins.concat({
			path: config.destJs + '/scripts.js',
			cwd: ''
		}))
		.pipe(plugins.rename('scripts.min.js'))
		.pipe(plugins.uglify())
		.pipe(gulp.dest(config.destJs))
		.pipe(buster.hash({
			manifest: './build/manifest.json',
			template: '<%= name %>.<%= ext %>'
		}))
		.pipe(gulp.dest(config.destJs))
});

// Linting task

gulp.task('lint', function(){
	return gulp.src('js/scripts.js')
		.pipe(plugins.jshint())
		.pipe(plugins.plumber(function(error) {
			errorHandler:reportError
		}))
		.pipe(plugins.jshint.reporter('default'))
		.on('error', reportError)
});

// Modernizr task

gulp.task('modernizr', function() {
	return gulp.src("js/vendor-libs/modernizr.js")
		.pipe(plugins.modernizr({
			options: [
				'setClasses',
				'addTest',
				'html5printshiv',
				'testPropsAll',
				'fnBind',
				'domPrefixes'
			]
		}))
		.pipe(gulp.dest(config.destJs))
});

// Images task

gulp.task('images', function () {
	return gulp.src(config.imgPath)
		.pipe(plugins.cache(plugins.imagemin({
			optimizationLevel: 3,
			progressive: false,
			interlaced: false
		})))
		.pipe(gulp.dest(config.imgPath))
});

// Watch task

gulp.task('watch', function () {
	gulp.watch(config.scssPathAll, ['styles']);
	gulp.watch(config.destImg, ['images']);

	// Run the scripts task in the correct sequence

	gulp.watch(config.jsPathAll, function() {
		runSequence('lint','scripts');
	});     
   
});      

// ===========================================================================================================
// HELPER FUNCTIONS
// ===========================================================================================================

// Error reporter function

var reportError = function (error) {
    var lineNumber = (error.lineNumber) ? 'LINE ' + error.lineNumber + ' -- ' : '';

    var report = '';
    var chalk = plugins.util.colors.yellow.bgRed;

    report += chalk('😢 ') + ' [' + error.plugin + ']\n';
    report += chalk('😢 ') + ' ' + error.message + '\n\n';

    if (error.lineNumber) {
		report += chalk('LINE:') + ' ' + error.lineNumber + '\n';
	}

    if (error.fileName) {
		report += chalk('FILE:') + ' ' + error.fileName + '\n';
	}

    console.error(report);
    this.emit('end'); // Stop the watch task from ending
}
