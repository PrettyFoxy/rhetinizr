define( function ( require, exports, module ) {
    'use strict';

    var $ = require( 'jquery' ),
      Backbone = require( 'backbone' ),
      Communicator = require( 'communicator' );

//    require( [module.config().routers.rhetina_uploadr], function(ModRouter){
//      console.log(ModRouter);
//    });

    var App = new Backbone.Marionette.Application( Rhetina.config );

    // Organize Application into regions corresponding to DOM elements
    // Regions can contain views, Layouts, or subregions nested as necessary
    App.addRegions( {
      headerRegion: "#rhetinaMainContainer>header",
      mainRegion  : "#rhetinaMainContainer>main"
    } );

    /* Add initializers here */
    App.addInitializer( function () {
      Communicator.mediator.trigger( "APP:START" );
      Backbone.history.start( {
        root     : App.root,
        pushState: true,
        silent   : false
      } );
    } );

    return App;
  }
);
