<?php
/**
 * HTTP query request mechanism
 */
 
require_once 'QueryCache.php';

class Query
{
  private $PULSE_API_KEY;
  
  function __construct($PULSE_API_KEY="") {
    // This method of setting the API key is silly, my excuse is that this is getting late
    if ($PULSE_API_KEY != "") {
      $this->PULSE_API_KEY = $PULSE_API_KEY; 
    } elseif (PULSE_API_KEY) {
      $this->PULSE_API_KEY = PULSE_API_KEY;
    }
  }
  
  /**
   * shorter name for cacheableRequest()
   */
  public function get($url, $method=HttpRequest::METH_GET, $expire_ttl=300) {
    return $this->cacheableRequest($url);
  }
  
  /**
   * Fetches data over HTTP
   */
  public function request($url, $method=HttpRequest::METH_GET) {

    // $r = new HttpRequest('https://api.pulseenergy.com/pulse/points.xml',HttpRequest::METH_GET);
    $r = new HttpRequest($url, $method);

    $r->setHeaders(array('Authorization' => $this->PULSE_API_KEY));
 
    $result = array();
    try {
      $r->send();
      //echo $response['code'] = $r->getResponseCode();
      $response['code'] = $r->getResponseCode();
      if ($r->getResponseCode() == 200) {
        $response['body'] = $r->getResponseBody();
      } else {
        $response['body'] = "HTTP " . $r->getResponseCode() . "\n" . $r->getResponseBody();
      }
    } catch (HttpException $ex) {
      $response['code'] = -1;
      $response['body'] = $ex;
    }

    return $response;
  }
  
  /**
   * Checks cache first, before calling request()
   */
  public function cacheableRequest($url, $method=HttpRequest::METH_GET, $expire_ttl=300) {
    $cache = new QueryCache();
    $cached_body = $cache->request($url,$method);
    if ($cached_body == QueryCache::CACHE_MISS) {
      $r = $this->request($url,$method);
      if ($r['code'] == 200) { $cache->save($url,$r['body'],$expire_ttl); }
      $r['cache_hit'] = QueryCache::CACHE_MISS;
    } else {
      $r = array('code'=>'200','body'=>$cached_body, 'cache_hit'=>QueryCache::CACHE_HIT);
    }
    return $r;
  }

  public static function test() {    
    echo "\n\nQuery TEST\n==========\n";
    $q = new Query();
    // $r = $q->request('https://api.pulseenergy.com/pulse/points.xml');
    $r = $q->request('https://api.pulseenergy.com/pulse/points/22495/data?interval=week');

    if ($r['code'] == 200) {
      echo $r['body'];
    } else {
      echo $r['code'] . "\n";
      echo $r['body'];
    }

    echo "\n";

  }

}

//Query::test();

?>
