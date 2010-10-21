// ==========================================================================
// Project:   Pulse.Building Fixtures
// Copyright: ©2010 My Company, Inc.
// ==========================================================================
/*globals Pulse */

sc_require('models/building');


/**
 * Dummy data 
 * for when HttpRequest not enabled
 */
Pulse.Building.FIXTURES = [

  // vancouver city hall
  { guid: 1,
	name: "Pik Tower",
	latitude: "49.26090751931607",
	longitude: "-123.11394870281221",
	//timezone: "America/New York",
	point: "10001",
	energy: "1234"
  },

  // harbour center
  { guid: 2,
	name: "Frungy Centre",
	latitude: "49.28474640573924",
	longitude: "-123.11207652091981",
	//timezone: "America/New York",
	point: "10002",
	energy: "2222"
  },

  // vancouver public library
  { guid: 3,
	name: "Shofix Hall",
	latitude: "49.279721282656226",
	longitude: "-123.11557412147523",
	//timezone: "America/Vancouver",
	point: "10003",
	energy: "3333"
  }


];
