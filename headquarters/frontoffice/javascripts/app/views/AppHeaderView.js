define( ['application', 'jquery', 'hbs!tmpl/mainHeader', 'backbone.marionette', 'models/MenuModel'],
  function ( App, $, template, Marionette, MenuModel ) {
    //ItemView provides some default rendering logic
    return Marionette.ItemView.extend( {
      template      : template,
      ui            : {
        nav: "ul.rhetina-navbar-left",
        rightnav: 'ul.rhetina-navbar-right'
      },
      events: {
        "click @ui.nav a": "goTo" //is the same as "click .dog":
      },
      model         : new MenuModel( {
        appRoot : App.root,
        sections: [
          {
            name: 'Dashboard',
            url : App.root + '/section_dashboard/'
          },
          {
            name: 'Products',
            url : App.root + '/section_products/'
          },
          {
            name: 'News',
            url : App.root + '/section_news/'
          }
        ]
      } ),
      goTo: function(evt) {
        evt.preventDefault();
          Backbone.history.navigate(evt.currentTarget.pathname.replace(App.root, ''), {trigger: true});
      },
      onBeforeRender: function () {
        // set up final bits just before rendering the view's `el`
      },
      onRender      : function () {
        // manipulate the `el` here. it's already
        // been rendered, and is full of the view's
        // HTML, ready to go.
      },
      onBeforeClose : function () {
        // manipulate the `el` here. it's already
        // been rendered, and is full of the view's
        // HTML, ready to go.
      },
      onClose       : function () {
        // custom closing and cleanup goes here
      }
    } );
  } );