define( ["application","jquery","backbone","models/ProductModel"],
  function(App, $, Backbone, ProductModel) {
    "use strict";
    // Creates a new Backbone Collection class object
    var Collection = Backbone.Collection.extend({
      url: App.api + 'phpfox/products/activated.json',
      // Tells the Backbone Collection that all of it's models will be of type Model (listed up top as a dependency)
      model: ProductModel
    });

    return Collection;
  });