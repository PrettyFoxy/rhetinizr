define( ['jquery', 'hbs!templates/sections/products', 'backbone', 'backbone.marionette', 'views/sections/ProductView'],
    function ( $, template, Backbone, Marionette, ProductView ) {
        "use strict";
        // ItemView provides some default rendering logic
        return Marionette.CompositeView.extend( {
            template: template,
            childViewContainer: "tbody",
            childView: ProductView,

            initialize: function ( options ) {
                console.log( 'aaa' );
                console.log( options );
            }
        } );
    } );