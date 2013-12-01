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
          dest: 'public/css/core.css'
        }, {
          src: ['less/bootstrap/yeti/yeti.less'],
          dest: 'public/css/bootstrap.css'
        }]
      }
    },
    copyto: {
      development: {
        files: [{
          cwd: 'js',
          src: ['**/*'],
          dest: 'public/js/'
        }, {
          cwd: 'lib',
          src: ['**/*'],
          dest: 'public/lib/'
        }],
        options: {
          ignore: ['**/src{,/**/*}']
        }
      }
    },
    mkdir: {
      development: {
        options: {
          create: ['public/css', 'public/js', 'public/lib']
        },
      },
    },
    clean: {
      build: ['public/css', 'public/js', 'public/lib']
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
        files: ['lib/**'],
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
