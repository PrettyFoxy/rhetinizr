define( ['application', 'jquery', 'backbone', 'backbone.marionette', 'views/AppHeaderView', 'underscore.string'],
    function ( App, $, Backbone, Marionette, AppHeaderView, _ ) {
        'use strict';
        return Marionette.Controller.extend( {
            initialize: function ( options ) {
                App.headerRegion.show( new AppHeaderView() );
            },
            //gets mapped to in AppRouter's appRoutes
            index: function ( section ) {
                if ( null === section ) {
                    section = 'dashboard';
                }

                this.setSectionTitle( section );

                require( ['views/sections/' + _.capitalize( section ) + 'View'], function ( SectionView ) {
                    App.mainRegion.show( new SectionView() );
                } );
            },
            productsList: function () {
                this.setSectionTitle( 'products' );
                var self = this;

                require( [
                        'views/sections/ProductListView',
                        'collections/ProductsCollection'
                    ],
                    function ( ProductListView, ProductsCollection ) {
                        // Create a a collection for the view
                        self.products = new ProductsCollection();
                        self.products.fetch( {
                            success: function ( collection ) {
                                console.log( 'self.products' );
                                console.log( collection );
                                var productListView = new ProductListView( {
                                    collection: collection
                                } );
                                App.mainRegion.show( productListView );
                            }
                        } );
                    } );

            },
            productSettingsPage: function ( productid ) {

            },
            other: function () {
                console.log( 'Other Route called' );
            },
            setSectionTitle: function ( title ) {
                $( '#main_title_holder h1' ).text( 'Rhetinizr / ' + _.capitalize( title ) );
            }
        } );
    } );