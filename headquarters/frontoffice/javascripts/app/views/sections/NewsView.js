define(['jquery', 'hbs!templates/sections/news', 'backbone', 'backbone.marionette'],
  function ($, template, Backbone, Marionette) {
    //ItemView provides some default rendering logic
    return Marionette.ItemView.extend({
      template:template
    });
  });