<?php
/**
 * PULSE API
 */
 
require_once 'API_KEY.php'; // define('PULSE_API_KEY','ABCDE....');

require_once 'Query.php';

class Pulse
{
  const API_BASEURL = 'https://api.pulseenergy.com/pulse/';
  
  private $query;
  
  private $timer = array( // Cache timers for API to avoid doing over 100 requests an hour.
    'hour' => 180,        // 2 min
    'day' => 900,         // 15 min  
    'week' => 3600,       // 30 min
    'month' => 3600,      // 60 min
    'building' => 3600,   // 60 min
    'history' => 10800);  // 180 min

  private $timePeriod = array(
    'hour' => 3600,        // 1 hour
    'day' => 86400,        // 24 hours = 24 x 3600 seconds
    'week' => 604800,      // 1 week..
    'month' => 18144000);  // 30 days..
  
  private $locations = array(
    array('latitude'=>"49.26090751931607", 'longitude'=>"-123.11394870281221"),
    array('latitude'=>"49.28474640573924", 'longitude'=>"-123.11207652091981"),
    array('latitude'=>"49.279721282656226", 'longitude'=>"-123.11557412147523")
    );
  
  /**
   * build data for sproutcore 'models/building'
   *
   * Example building:  Pik Tower / city hall
   *
   *  { guid: 1,
   *	name: "Pik Tower",
   *	latitude: "49.26090751931607",
   *	longitude: "-123.11394870281221",
   *	point: "10001"
   * },
   */
  public function getBuildings() {
    
    $buildings = array();
    $http_result = $this->query->get(
      Pulse::API_BASEURL . 'points.json', HttpRequest::METH_GET, $timer['buildings']);
    
    if ($http_result['code'] == 200) { // if success
      
      $building_points = json_decode($http_result['body']);
        
      // $point contains id, label, unit, type, measurement,timzezone
      $guid = 1;
      foreach($building_points as $point) {
        //echo $point->label . "\n";
        $buildingName = $this->getBuildingName($point->label);        
        if ($point->label == $buildingName.' Energy') {  // ignore Power & Energy (Unscaled)
          $buildings[] = array(
            'guid' => $guid, 
            'name' => $buildingName,
            'latitude' => $this->locations[$guid-1]['latitude'],
            'longitude' => $this->locations[$guid-1]['longitude'],
            'point' => $point->id,
            'energy' => $this->getEnergySumForPoint($point->id));
          $guid++;
        }
        // array notation: $buildings['$buildingName'][$point->id] = $point;
        // object notation: $buildings->{$buildingName}->{$point->id} = $point;
      }
      
      $http_result['body'] = $buildings;
      
    } else {
      // http_result already contains error info code and body.
    }
    
    return $http_result;
  }
  
  /**
   * get energy usage data for a point
   *
   * interval = hour, day, week, month
   *            defaults to week
   * start = unix time, or ISO time format
   *         defaults to the most recent interval
   */
  public function getData($pointId,$interval='week',$start="") {

    if (!$pointId) {
      return array('code'=>500, 'body'=> 'Missing point id. eg: /data/points/1234 <- 1234 is id');
    }
    
    $url = Pulse::API_BASEURL . "points/$pointId/data.json";
    $url .= "?interval=$interval";
    $url .= "&start=".($start ? $start : time()-$this->timePeriod[$interval]);
    
    $r = $this->query->get($url, HttpRequest::METH_GET, $timer[$interval]);

    if ($r['code'] != 200) {
      // show the messy error from API
      echo $r['body'];
    } else {
      // decode json into array object, it'll be json_encoded() by index.php next
      $r['body'] = json_decode($r['body']);

      // timestamps in javascript are the number of milliseconds since epoc
      // timestamps in php/unix are the number of seconds since epoc
      // so we multiply everything by 1000
      $r['body'] = $this->multiplyTimestamps($r['body']);
    }
    
    return $r;
    
  }

  /**
   * convert to unix timestamps to javascript timestamps for FLOT
   * see http://flot.googlecode.com/svn/trunk/API.txt
   *
   * TODO: push this clientside?
   */
  private function multiplyTimestamps($dataset) {
    foreach ($dataset->data as $idx => $entry) {
      //echo "idx: $idx - ".$entry[0].",".$entry[1]."\n";
      $dataset->data[$idx][0] *= 1000;
    }
    return $dataset;
  }
  
  /**
   * returns building name
   * by taking the first 2 words of the label (silly method)
   */
  private function getBuildingName($string) {
    //TODO strip the 'Energy (Unscaled)', 'Power', etc 
    $t = explode(' ', $string);
    return join(' ', array($t[0], $t[1]) );
  }
  
  /**
   * get the amount of energy used over the last $interval
   * quick n dirty.
   */
  private function getEnergySumForPoint($pointId, $interval='day') {
    
    $url = Pulse::API_BASEURL . "points/$pointId/data.json";
    $url .= "?interval=$interval";
    
    $r = $this->query->get($url, HttpRequest::METH_GET, $timer[$interval]);
    
    if ($r['code'] == 200) {
      $data = json_decode($r['body']);
      $result = $data->sum .' '. $data->unit;
    } else {
      $result = 'unknown';
    }
    return $result;
  }
  
  function __construct() {
    $this->query = new Query(PULSE_API_KEY);
  }

  public static function test() {
    echo "\n\nPulse TEST\n==========\n";
    
    $p = new Pulse();
    
    echo "\nBuilding.json :\n";
    echo json_encode($p->getBuildings());
    
    echo "\nData.json :\n";
    echo json_encode($p->getData(25393,'day'));
    
  }
  
}


// Pulse::test();

?>
