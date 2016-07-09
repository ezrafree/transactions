module.exports = function (grunt) {
	grunt.initConfig({
		jshint: {
			files: [
				'gruntfile.js',
				'assets/javascript/project.js'
			],
			options: {
				globals: {
					jQuery: true
				}
			}
		},
		concat: {
			options: {
				stripBanners: true
			},
			dist: {
				src: [
					'assets/javascript/lib/jquery.min.js',
					'assets/javascript/lib/bootstrap.min.js',
					'assets/javascript/lib/stickyfill.min.js',
					'assets/javascript/lib/Chart.bundle.min.js',
					'assets/javascript/project.js'
				],
				dest: 'js/script.js'
			}
		},
		uglify: {
			files: {
				src: 'js/script.js', // source files mask
				dest: 'js/', // destination folder
				expand: true, // allow dynamic building
				flatten: true, // remove all unnecessary nesting
				ext: '.min.js' // replace .js to .min.js
			}
		},
		prettysass: {
			options: {
				alphabetize: false,
				indent: "t",
				removeBlankLines: true
			},
			dist: {
				src: ['assets/sass/partials/*.scss']
			}
		},
		sass: {
			dist: {
				options: {
					style: 'expanded',
					lineNumbers: false,
					sourcemap: 'none'
				},
				files: {
					'css/style.css': 'assets/sass/style.scss'
				}
			}
		},
		cssmin: {
			options: {
				shorthandCompacting: false,
				roundingPrecision: -1,
				keepSpecialComments: 0
			},
			target: {
				files: {
					'css/style.min.css': 'css/style.css'
				}
			}
		},
		watch: {
			js: {
				files: 'assets/javascript/project.js',
				tasks: [ 'jshint', 'concat', 'uglify' ]
			},
			css: {
				files: 'assets/sass/**/*.scss',
				tasks: [ 'prettysass', 'sass', 'cssmin' ]
			}
		}
	});

	// load plugins
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-contrib-jshint');
	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-prettysass');
	grunt.loadNpmTasks('grunt-contrib-sass');
	grunt.loadNpmTasks('grunt-contrib-cssmin');
	grunt.loadNpmTasks('grunt-contrib-uglify');

	// register tasks
	grunt.registerTask('default', [ 'watch', 'jshint', 'concat', 'prettysass', 'sass', 'cssmin', 'uglify' ]);

};