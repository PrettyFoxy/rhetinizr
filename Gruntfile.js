'use strict';
var lrSnippet = require( 'grunt-contrib-livereload/lib/utils' ).livereloadSnippet;
var mountFolder = function ( connect, dir ) {
  return connect.static( require( 'path' ).resolve( dir ) );
};

// # Globbing
// templateFramework: 'handlebars'

module.exports = function ( grunt ) {

  // load all grunt tasks
  require( 'matchdep' ).filterDev( 'grunt-*' ).forEach( grunt.loadNpmTasks );
  // show elapsed time at the end
  require( 'time-grunt' )( grunt );

  // configurable paths
  var yeomanConfig = {
    app : 'headquarters/frontoffice',
    dist: 'headquarters/frontoffice'
  };

  grunt.initConfig( {
    yeoman: yeomanConfig,

    // watch list
    watch : {
      less: {
        files: ['<%= yeoman.app %>/stylesheets/{,*/}*.less'],
        tasks: ['less:development']
      },

      compass: {
        files: ['<%= yeoman.app %>/stylesheets/{,*/}*.{scss,sass}'],
        tasks: ['compass']
      }
      //,

//      livereload: {
//        files  : [
//          '{<%= yeoman.app %>}/stylesheets/{,**/}*.css',
//          '{.tmp,<%= yeoman.app %>}/javascripts/{,**/}*.js',
//          '{.tmp,<%= yeoman.app %>}/templates/{,**/}*.hbs',
//          '<%= yeoman.app %>/images/{,*/}*.{png,jpg,jpeg,gif,webp}',
//
//          'test/spec/{,**/}*.js'
//        ],
//        tasks  : ['exec'],
//        options: {
//          livereload: true
//        }
//      }
      /* not used at the moment
       handlebars: {
       files: [
       '<%= yeoman.app %>/templates/*.hbs'
       ],
       tasks: ['handlebars']
       }*/
    },

    // mocha command
    exec  : {
//      mocha: {
//        command: 'mocha-phantomjs http://localhost:<%= connect.testserver.options.port %>/test',
//        stdout : true
//      }
    },

    less     : {
      options    : {
        paths: ["<%= yeoman.app %>/stylesheets/less", "<%= yeoman.app %>/vendor"]
      },
      development: {
        options: {
          sourceMap        : true,
          sourceMapFilename: '<%= yeoman.app %>/stylesheets/css/main.css.map',
          sourceMapURL     : '/module/rhetinizr/headquarters/frontoffice/stylesheets/css/main.css.map'
        },
        files  : {
          "<%= yeoman.app %>/stylesheets/css/main.css": "<%= yeoman.app %>/stylesheets/less/main.less"
        }
      },
      production : {
        options: {
          cleancss: true
        },
        files  : {
          "<%= yeoman.app %>/stylesheets/css/main.css": "<%= yeoman.app %>/stylesheets/less/main.less"
        }
      }
    },

    // linting
    jshint   : {
      options: {
        jshintrc: '.jshintrc',
        reporter: require( 'jshint-stylish' )
      },
      all    : [
        'Gruntfile.js',
        '<%= yeoman.app %>/javascripts/{,*/}*.js',
        '!<%= yeoman.app %>/javascripts/vendor/*'
      ]
    },


    // compass
    compass  : {
      options: {
        sassDir       : '<%= yeoman.app %>/stylesheets/scss',
        cssDir        : '<%= yeoman.app %>/stylesheets',
        imagesDir     : '<%= yeoman.app %>/images',
        javascriptsDir: '<%= yeoman.app %>/javascripts',
        fontsDir      : '<%= yeoman.app %>/fonts',
        importPath    : '<%= yeoman.app %>/vendor',
        relativeAssets: true
      },
      dist   : {},
      server : {
        options: {
          debugInfo: true
        }
      }
    },


    // require
    requirejs: {
      dist: {
        // Options: https://github.com/jrburke/r.js/blob/master/build/example.build.js
        options: {
          // `name` and `out` is set by grunt-usemin
          baseUrl                : '<%= yeoman.app %>/javascripts',
          optimize               : 'none',
          paths                  : {
            'templates': '<%= yeoman.app %>/templates'
          },
          // TODO: Figure out how to make sourcemaps work with grunt-usemin
          // https://github.com/yeoman/grunt-usemin/issues/30
          //generateSourceMaps: true,
          // required to support SourceMaps
          // http://requirejs.org/docs/errors.html#sourcemapcomments
          preserveLicenseComments: false,
          useStrict              : true,
          wrap                   : true,
          //uglify2: {} // https://github.com/mishoo/UglifyJS2
          pragmasOnSave          : {
            //removes Handlebars.Parser code (used to compile template strings) set
            //it to `false` if you need to parse template strings even after build
            excludeHbsParser : true,
            // kills the entire plugin set once it's built.
            excludeHbs       : true,
            // removes i18n precompiler, handlebars and json2
            excludeAfterBuild: true
          }
        }
      }
    },

    // handlebars
    handlebars: {
      compile: {
        options: {
          namespace: 'JST',
          amd      : true
        },
        files  : {
          '<%= yeoman.app %>/javascripts/templates.js': ['<%= yeoman.app %>/templates/**/*.hbs']
        }
      }
    },
    // Wipe out previous builds and test reporting.
    clean     : {
      assets: ['<%= yeoman.app %>/stylesheets/css'],
      build : ["dist/"],
      end   : ["dist/include/library/vendor", "dist/static/jscript/vendor"]
    },

    // Move vendor and app logic during a build.
    copy      : {
      release: {
        files: [
          { src: ["include/**"], dest: "dist/" },
          { src: ["static/**"], dest: "dist/" },
          { src: ["template/**"], dest: "dist/" }
        ]
      }
    },

    compress: {
      php: {
        options: {
          archive: "dist/install/vendors-php.zip"
        },

        files: [
          {
            expand: true,
            cwd   : 'dist/include/library/',
            src   : ["**"],
            dest  : ""
          }
        ]
      },
      js : {
        options: {
          archive: "dist/install/vendors-js.zip"
        },

        files: [
          {
            expand: true,
            cwd   : 'dist/static/jscript/',
            src   : ["**"],
            dest  : ""
          }
        ]
      },
      all: {
        options: {
          archive: "dist/Rhetina-Heart.zip"
        },

        files: [
          {
            expand: true,
            cwd   : "dist/",
            src   : ["**"],
            dest  : "upload/module/rhetinaheart/"
          }
        ]
      }
    }
  } );

  grunt.registerTask( 'prepare-zip', ['compress:php', 'compress:js', 'clean:end', 'compress:all'] );

  // When running the default Grunt command, just lint the code.
  grunt.registerTask( "default", [
    "clean:assets",
    "jshint",
    "less:development"
  ] );
};
