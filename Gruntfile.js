module.exports = function(grunt) {

  // Project configuration.
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    sass: {
      full: {
        files: {
          'css/styles.css': 'css/common.scss'
        }
      }
    },
    coffee: {
      full: {
        files: {
          'js/scripts.js': 'js/all.coffee'
        }
      },
    },
    autoprefixer: {
      full: {
        files: {
            'css/styles.css': 'css/styles.css'
        }
      }
    },
    watch: {
      styles: {
        files: ['css/*.scss'],
        tasks: ['sass:full','autoprefixer:full']  
      },
      scripts: {
        files: ['js/*.coffee'],
        tasks: ['coffee:full']
      }
    },
    includereplace: {
      full: {
        options: {
          globals: {
            version: "<%= pkg.display_version %>",
            full_version: "<%= pkg.version %>"
          }
        },
        src: 'init.php',
        dest: 'dist/imageRandomizer.php'
      },
      coffee: {
        src: 'js/main.coffee',
        dest: 'js/all.coffee'
      }
    },
    clean: {
      full: ['js/all.coffee']
    },
    copy: {
      demo: {
        src: 'dist/imageRandomizer.php',
        dest: '../image-randomizer-site/demo/index.php'
      } 
    }
  });

  // Load plugins
  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-contrib-coffee');
  grunt.loadNpmTasks('grunt-contrib-sass');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-autoprefixer');
  grunt.loadNpmTasks('grunt-include-replace');
  grunt.loadNpmTasks('grunt-contrib-clean');
  grunt.loadNpmTasks('grunt-contrib-copy');

  // Default task(s).
  grunt.registerTask('default', ['sass:full','autoprefixer:full','includereplace:coffee','coffee:full','includereplace:full','clean:full']);
  grunt.registerTask('demo', ['sass:full','autoprefixer:full','includereplace:coffee','coffee:full','includereplace:full','clean:full','copy:demo']);
};