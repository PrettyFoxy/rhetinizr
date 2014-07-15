define(['jquery', 'hbs!templates/sections/dashboard', 'backbone.marionette'],
  function ($, template, Marionette) {
    //ItemView provides some default rendering logic
    return Marionette.ItemView.extend({
      template:template,
      model:''
    });
  });