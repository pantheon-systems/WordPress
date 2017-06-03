/**
 *
 * @see  https://github.com/squizlabs/PHP_CodeSniffer
 */
module.exports = function (grunt) {
  'use strict';
  // Project configuration
  grunt.initConfig({
    // Metadata
    pkg: grunt.file.readJSON('package.json'),
    banner: '/*! <%= pkg.name %> - v<%= pkg.version %> - ' +
        '<%= grunt.template.today("yyyy-mm-dd") %>\n' +
        '<%= pkg.homepage ? "* " + pkg.homepage + "\\n" : "" %>' +
        '* Copyright (c) <%= grunt.template.today("yyyy") %> <%= pkg.author.name %>;' +
        ' Licensed <%= props.license %> */\n',
    // PHP Coding Standards
    // ====================
    phpcs: {
      core: {
        dir: ['../*.php'],
      },
      extra: {
        dir: [
          '../inc/*.php',
          '../inc/shortcodes/*.php',
          '../helpers/*.php',
        ],
      },
      options: {
        bin: 'vendor/bin/phpcs',
        // bin: 'vendor/bin/phpcbf',
        standard: 'Wordpress',
        ignore: [ ],
      }
    },
    // CSS Lint
    // ========
    csslint: {
      options: {
        csslintrc: '.csslintrc'
      },
      core: [
        '../*.css'
      ],
      layouts: [
        '../layouts/*.css'
      ]
    },
    // PHPUnit
    // =======
    phpunit: {
      configuration: 'phpunit.xml',
      options: {
        bin: './vendor/bin/phpunit',
        color: true
      }
    },
    // CSS Minify
    // ==========
    cssmin: {
        target: {
            files: [{
                expand: true,
                cwd: '../stylesheets/',
                src: ['*.css', '!*.min.css'],
                dest: '../stylesheets/',
                ext: '.min.css'
            }]
        }
    },
    // Bower
    // =====
    bower: {
      dev: {
        dest: '../assets/',
        options: {
          keepExpandedHierarchy: false,
          packageSpecific: {
            bootstrap: {
              js_dest: '../assets/bootstrap/js',
              css_dest: '../assets/bootstrap/css',
              fonts_dest: '../assets/bootstrap/fonts',
              files: [
                'dist/css/bootstrap.min.css',
                'dist/js/bootstrap.min.js',
                'dist/fonts/*'
              ]
            },
            "bootstrap-asu": {
              keepExpandedHierarchy: true,
              stripGlobBase: true,
              dest: '../assets/asu-web-standards/img',
              js_dest: '../assets/asu-web-standards/js',
              css_dest: '../assets/asu-web-standards/css',
              fonts_dest: '../assets/asu-web-standards/fonts',
              images_dest: '../assets/asu-web-standards/img',
              files: [
                'build/css/*.min.css',
                'build/js/*.min.js',
                'build/fonts/**',
                'build/img/**',
              ],
            },
            jquery: {
              dest: '../assets/jquery',
              files: [
                'dist/jquery.min.js',
              ]
            },
            respond: {
              dest: '../assets/respond',
              files: [
                'dest/respond.min.js'
              ]
            }
          }
        }
      }
    }
  });

  // These plugins provide necessary tasks
  grunt.loadNpmTasks('grunt-phpcs');
  grunt.loadNpmTasks('grunt-contrib-csslint');
  grunt.loadNpmTasks('grunt-phpunit');
  grunt.loadNpmTasks('grunt-bower');

  grunt.registerTask('build', [
    'bower'
  ]);

  grunt.registerTask('test', [
    'phpcs',
    'phpunit',
  ]);

  // Default task
  grunt.registerTask('default', [
    'test',
    'build'
  ]);
};

