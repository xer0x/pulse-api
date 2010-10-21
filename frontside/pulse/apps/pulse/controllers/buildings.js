// ==========================================================================
// Project:   Pulse.buildingsController
// Copyright: ©2010 My Company, Inc.
// ==========================================================================
/*globals Pulse */

/** @class

  (Document Your Controller Here)

  @extends SC.ArrayController
*/
Pulse.buildingsController = SC.ArrayController.create(
  //SC.CollectionViewDelegate,
/** @scope Pulse.buildingsController.prototype */ {

  selectBuilding: function() {
    
    var building = this.get('selection').firstObject();
  
    console.log('selected building is ' + building.get('name'));
    
    // select the 'marker' for the selected building
    if (Pulse.GMapSelectors && Pulse.GMapSelectors[building.get('guid')]) {
      Pulse.GMapSelectors[building.get('guid')]();
    }

    return YES;
  }

}) ;
