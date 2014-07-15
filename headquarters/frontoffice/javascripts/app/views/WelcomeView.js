define( ['application', 'backbone', 'backbone.marionette', 'jquery', 'models/Model', 'hbs!templates/welcome'],
    function(App, Backbone, Marionette, $, Model, template) {
        //ItemView provides some default rendering logic
        return Marionette.ItemView.extend( {
            template: template,
            model: new Model({
            }),

            // View Event Handlers
            events: {

            }
        });
    });