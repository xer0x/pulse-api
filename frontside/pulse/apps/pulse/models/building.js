// ==========================================================================
// Project:   Pulse.Building
// Copyright: ©2010 My Company, Inc.
// ==========================================================================
/*globals Pulse */

/** @class

  (Document your Model here)

  @extends SC.Record
  @version 0.1
*/
Pulse.Building = SC.Record.extend(
/** @scope Pulse.Building.prototype */ {

	name: SC.Record.attr(String),
	latitude: SC.Record.attr(String),
	longitude: SC.Record.attr(String),
	//timezone: SC.Record.attr(String)
	//marker: Object
	point: SC.Record.attr(String),
	energy: SC.Record.attr(String)
	
}) ;
