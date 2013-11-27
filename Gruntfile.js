/*global module:false*/
module.exports = function(grunt) {

  // Project configuration.
  grunt.initConfig({
    // Task configuration.
    less: {
      development: {
        options: {
          paths: ['less/']
        },
        files: [{
          src: ['less/core.less'],
          dest: 'WebContent/css/core.css'
        }]
      }
    },
    copyto: {
      development: {
        files: [{
          cwd: 'fonts',
          src: ['**/*'],
          dest: 'WebContent/fonts/'
        }, {
          cwd: 'img',
          src: ['**/*'],
          dest: 'WebContent/img/'
        }, {
          cwd: 'js',
          src: ['**/*'],
          dest: 'WebContent/js/'
        }, {
          cwd: 'lib',
          src: ['**/*'],
          dest: 'WebContent/lib/'
        }, {
          cwd: '.',
          src: ['index.html'],
          dest: 'WebContent/'
        }],
        options: {
          ignore: ['**/src{,/**/*}']
        }
      }
    },
    mkdir: {
      development: {
        options: {
          create: ['WebContent', 'WebContent/css', 'WebContent/fonts', 'WebContent/img', 'WebContent/js']
        },
      },
    },
    clean: {
      build: ['WebContent/']
    },
    watch: {
      css: {
        files: ['less/**'],
        tasks: ['less:development']
      },
      js: {
        files: ['js/**'],
        tasks: ['copyto:development']
      },
      other: {
        files: ['index.html', 'lib/**', 'img/**', 'fonts/**'],
        tasks: ['copyto:development']
      }
    }
  });

  // These plugins provide necessary tasks.
  grunt.loadNpmTasks('grunt-mkdir');
  grunt.loadNpmTasks('grunt-copy-to');
  grunt.loadNpmTasks('grunt-contrib-less');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-contrib-clean');

  // Default task.
  grunt.registerTask('default', ['mkdir', 'less', 'copyto']);

};
