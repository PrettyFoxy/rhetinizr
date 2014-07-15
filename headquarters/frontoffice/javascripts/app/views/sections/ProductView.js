define( ['application', 'jquery', 'hbs!templates/sections/product', 'backbone', 'backbone.marionette', 'bootstrap'],
    function ( App, $, template, Backbone, Marionette, Bootstrap ) {
        "use strict";
        //ItemView provides some default rendering logic
        return Marionette.ItemView.extend( {
            template: template,
            tagName: 'tr',
            ui: {
                btnSettings: "button.btn-product-settings",
                btnUpdate: 'button.btn-product-update'
            },
            events: {
                'click @ui.btnSettings': 'showSettings'
            },
            initialize: function (options) {
                console.log('ProductView:');
                console.log(options);
            },
            onRender: function () {
                $( this.ui.btnUpdate )
                    .popover( {
                        placement: 'top',
                        html: true
                    } );
            },
            showSettings: function ( evt ) {
                evt.preventDefault();
                Backbone.history.navigate( '/section_products/' + this.model.id, {trigger: true} );
            }
        } );
    } );