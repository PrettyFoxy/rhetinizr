define(['backbone', 'backbone.marionette'],
        function(Backbone, Marionette) {
            'use strict';
            return Marionette.AppRouter.extend({
                //'index' must be a method in AppRouter's controller
                appRoutes: {
                 '(/)': 'index',
                 'section_products(/)': 'productsList',
                 'section_:section(/)': 'index'
                 },
                 /* standard routes can be mixed with appRoutes/Controllers above */
                routes: {
                    'some/otherRoute': 'someOtherMethod'
                },
                someOtherMethod: function() {
                    // do something here.
                }
            });
        });