define( function ( require, exports, module ) {
  'use strict';
  var backbone = require('backbone' ),
    app = require('application' ),
    regionManager = require('regionManager' ),
    appRouter = require('routers/AppRouter' ),
    appController = require('controllers/AppController' );

  app.routers = app.routers || {};

  app.routers.app = new appRouter({
    controller: new appController()
  });

  app.start();
});
