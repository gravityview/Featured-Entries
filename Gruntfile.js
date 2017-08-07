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

	grunt.loadNpmTasks('grunt-contrib-less');
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-wp-readme-to-markdown');
	grunt.loadNpmTasks('grunt-potomo');
	grunt.loadNpmTasks('grunt-exec');


	grunt.registerTask( 'default', ['uglify','exec:transifex','potomo','wp_readme_to_markdown','watch'] );

};