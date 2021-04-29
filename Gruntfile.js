module.exports = function(grunt) {

	grunt.initConfig({

		pkg: grunt.file.readJSON('package.json'),

		less: {
		  development: {
			options: {
			  compress: true,
			  yuicompress: true,
			  optimization: 2
			},
			files: {
			  // target.css file: source.less file
			  "assets/css/featured-entries.css": "assets/css/source/featured-entries.less"
			}
		  }
		},

		uglify: {
			options: { mangle: false },
			featured_entries: {
				files: [{
				  expand: true,
				  cwd: 'assets/js',
				  src: ['**/*.js','!**/*.min.js'],
				  dest: 'assets/js',
				  ext: '.min.js'
			  }]
			}
		},

		// Build translations
		makepot: {
			target: {
				options: {
					mainFile: 'featured-entries.php',
					type: 'wp-plugin',
					domainPath: '/languages',
					updateTimestamp: false,
					exclude: ['node_modules/.*', 'assets/.*', 'tmp/.*', 'vendor/.*' ],
					potHeaders: {
						poedit: true,
						'x-poedit-keywordslist': true
					},
					processPot: function( pot, options ) {
						pot.headers['language'] = 'en_US';
						pot.headers['language-team'] = 'GravityView <hello@gravityview.co>';
						pot.headers['last-translator'] = 'GravityView <hello@gravityview.co>';
						pot.headers['report-msgid-bugs-to'] = 'https://gravityview.co/support/';

						var translation,
							excluded_meta = [
								'GravityView - Featured Entries Extension',
								'https://gravityview.co/extensions/featured-entries',
								'Promote entries as Featured in Views',
								'https://gravityview.co',
								'GravityView',
								'GPLv2 or later',
								'http://www.gnu.org/licenses/gpl-2.0.html',
							];

						for ( translation in pot.translations[''] ) {
							if ( 'undefined' !== typeof pot.translations[''][ translation ].comments.extracted ) {
								if ( excluded_meta.indexOf( pot.translations[''][ translation ].msgid ) >= 0 ) {
									console.log( 'Excluded meta: ' + pot.translations[''][ translation ].msgid );
									delete pot.translations[''][ translation ];
								}
							}
						}

						return pot;
					}
				}
			}
		},

		addtextdomain: {
			options: {
				textdomain: 'gravityview-featured-entries',    // Project text domain.
				updateDomains: [ 'gravityview', 'gravity-view', 'gravityforms', 'edd_sl', 'edd', 'easy-digital-downloads' ]  // List of text domains to replace.
			},
			target: {
				files: {
					src: [
						'*.php',
						'**/*.php',
						'!node_modules/**',
						'!tests/**',
						'!tmp/**'
					]
				}
			}
		},

		watch: {
			featured_entries: {
				files: ['assets/css/source/*.less','assets/js/*.js','!assets/js/*.min.js','readme.txt'],
				tasks: ['uglify','wp_readme_to_markdown','less']
			}
		},

		wp_readme_to_markdown: {
			your_target: {
				files: {
					'readme.md': 'readme.txt'
				}
			}
		},

		dirs: {
			lang: 'languages'
		},

		// Convert the .po files to .mo files
		potomo: {
			dist: {
				options: {
					poDel: false
				},
				files: [{
					expand: true,
					cwd: '<%= dirs.lang %>',
					src: ['*.po'],
					dest: '<%= dirs.lang %>',
					ext: '.mo',
					nonull: true
				}]
			}
		},

		// Pull in the latest translations
		exec: {
			transifex: 'tx pull -a',

			// Create a ZIP file
			zip: 'git-archive-all ../gravityview-featured-entries.zip'
		}
	});

	grunt.loadNpmTasks('grunt-wp-i18n');
	grunt.loadNpmTasks('grunt-contrib-less');
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-wp-readme-to-markdown');
	grunt.loadNpmTasks('grunt-potomo');
	grunt.loadNpmTasks('grunt-exec');

	grunt.registerTask( 'default', [ 'less', 'uglify', 'translate', 'wp_readme_to_markdown' ] );

	// Translation stuff
	grunt.registerTask( 'translate', [ 'exec:transifex', 'potomo', 'addtextdomain', 'makepot' ] );
};