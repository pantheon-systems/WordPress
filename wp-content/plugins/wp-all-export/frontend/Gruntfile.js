/*jslint node: true */
"use strict";


module.exports = function(grunt) {

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        uglify: {
            dist: {
                files: {
                    '../dist/app.min.js': [ '../dist/app.js' ]
                },
                options: {
                    mangle: true
                }
            }
        },

        html2js: {
            dist: {
                src: [ 'src/**/*.tpl.html' ],
                dest: 'tmp/templates.js'
            }
        },

        clean: {
            temp: {
                src: [ 'tmp' ]
            }
        },

        concat: {
            dist: {
                src: [
                    //NOTE: Tipsy is already loaded in admin js, if we remove it from there don't forget to
                    // add it here
                    'node_modules/angular/angular.js',
                    'node_modules/ng-slide-down/dist/ng-slide-down.js',
                    'node_modules/angular-animate/angular-animate.js',
                    'node_modules/angular-chosen-localytics/dist/angular-chosen.js',
                    'node_modules/angular-sanitize/angular-sanitize.js',
                    'node_modules/dotjem-angular-tree/src/directives/dxTree.js',
                    'node_modules/@iamadamjowett/angular-click-outside/clickoutside.directive.js',
                    'src/app.js',
                    'src/**/*.js',
                    'tmp/*.js'
                ],
                dest: '../dist/app.js'
            },
            css: {
                src: ['tmp/**/*.css'],
                dest: '../dist/styles.css'
            }
        },
        
        connect: {
            server: {
                options: {
                    hostname: 'localhost',
                    port: 8080
                }
            }
        },

        watch: {
            dev: {
                files: [ 'Gruntfile.js', 'src/**/*.js', 'src/**/*.html', 'src/**/*.scss' ],
                tasks: [ 'html2js:dist', 'sass', 'concat:css', 'concat:dist', 'clean:temp', 'package' ],
                options: {
                    atBegin: true,
                    livereload:true
                }
            },
            min: {
                files: [ 'Gruntfile.js', 'src/*.js', '*.html' ],
                tasks: [ 'jshint', 'karma:unit', 'html2js:dist', 'concat:dist', 'clean:temp', 'uglify:dist' ],
                options: {
                    atBegin: true
                }
            }
        },

        compress: {
            dist: {
                options: {
                    archive: '../dist/<%= pkg.name %>-<%= pkg.version %>.zip'
                },
                files: [{
                    src: [ 'index.html' ],
                    dest: '/'
                }, {
                    src: [ '../dist/**' ],
                    dest: '../dist/'
                }, {
                    src: [ 'assets/**' ],
                    dest: 'assets/'
                }, {
                    src: [ 'libs/**' ],
                    dest: 'libs/'
                }]
            }
        },
        sass: {
            files: {
                expand: true,
                cwd: 'src',
                src: ['**/*.scss'],
                dest: 'tmp/',
                ext: '.css'
            }
        },
        karma: {
            options: {
                configFile: 'config/karma.conf.js'
            },
            unit: {
                singleRun: true
            },

            continuous: {
                singleRun: false,
                autoWatch: true
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-clean');
    grunt.loadNpmTasks('grunt-contrib-connect');
    grunt.loadNpmTasks('grunt-contrib-compress');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-html2js');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-bower-task');
    grunt.loadNpmTasks('grunt-contrib-sass');

    grunt.registerTask('dev', ['connect:server', 'watch:dev' ]);
    grunt.registerTask('test', [ 'bower', 'jshint', 'karma:continuous' ]);
    grunt.registerTask('minified', ['connect:server', 'watch:min' ]);
    grunt.registerTask('package', [ 'html2js:dist', 'sass', 'concat:css', 'concat:dist', 'clean:temp', 'uglify:dist']);
};