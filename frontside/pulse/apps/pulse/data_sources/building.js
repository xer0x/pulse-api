// ==========================================================================
// Project:   Pulse.BuildingDataSource
// Copyright: ©2010 My Company, Inc.
// ==========================================================================
/*globals Pulse */

sc_require('models/building');

Pulse.BUILDINGS_QUERY = SC.Query.local(Pulse.Building, {
  orderBy: 'name'
});

/** @class

  (Document Your Data Source Here)

  @extends SC.DataSource
*/
Pulse.BuildingDataSource = SC.DataSource.extend(
/** @scope Pulse.BuildingDataSource.prototype */ {

  // ..........................................................
  // QUERY SUPPORT
  // 

  fetch: function(store, query) {

    // TODO: Add handlers to fetch data for specific queries.  
    // call store.dataSourceDidFetchQuery(query) when done.

    if (query === Pulse.BUILDINGS_QUERY) {
      // SC.Request.getUrl('http://pulse.sluglab.com/data/buildings')
      SC.Request.getUrl('/data/buildings')
        //.header({'Accept:': 'application/json'})
        .json()
        .notify(this, 'didFetchBuildings', store, query)
        .send();
      return YES;
    }

    return NO ; // return YES if you handled the query
  },
  
  // callback function for fetch: SC.Request.getUrl httpRequest
  didFetchBuildings: function(response, store, query) {
    if (SC.ok(response)) {
      //console.log('ok ok ok');
      store.loadRecords(Pulse.Building, response.get('body').content);
      store.dataSourceDidFetchQuery(query);
      
      Pulse.setMarkers();
      
    } else {
      // console.log(SC.inspect(response));
      console.log('failure loading /data/buildings');
      store.dataSourceDidErrorQuery(query, response);
    }
  },

  // ..........................................................
  // RECORD SUPPORT
  // 
  
  retrieveRecord: function(store, storeKey) {
    
    // TODO: Add handlers to retrieve an individual record's contents
    // call store.dataSourceDidComplete(storeKey) when done.
    
    return NO ; // return YES if you handled the storeKey
  },
  
  createRecord: function(store, storeKey) {
    
    // TODO: Add handlers to submit new records to the data source.
    // call store.dataSourceDidComplete(storeKey) when done.
    
    return NO ; // return YES if you handled the storeKey
  },
  
  updateRecord: function(store, storeKey) {
    
    // TODO: Add handlers to submit modified record to the data source
    // call store.dataSourceDidComplete(storeKey) when done.

    return NO ; // return YES if you handled the storeKey
  },
  
  destroyRecord: function(store, storeKey) {
    
    // TODO: Add handlers to destroy records on the data source.
    // call store.dataSourceDidDestroy(storeKey) when done
    
    return NO ; // return YES if you handled the storeKey
  }
  
}) ;
