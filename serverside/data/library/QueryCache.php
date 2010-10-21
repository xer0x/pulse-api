<?php
/**
 * Keeps PULSE API data in REDIS
 */
 
// require_once 'library/Rediska.php';
require_once 'Rediska.php';

class QueryCache
{
  const CACHE_MISS = false;
  const CACHE_HIT = true;
  
  private $rediska;
  
  function __construct() {   
    $options = array(
      'namespace' => 'Pulse_',
      'servers' => array(
        array('host' => '127.0.0.1', 'port' => 6379)
      )
    );
    $this->rediska = new Rediska($options);
  }

  /**
   * check for url in cache
   */
  public function request($url, $method=HttpRequest::METH_GET) {  
    $key = new Rediska_Key($url);    
    $v = $key->getValue();
    return $v ? $v : QueryCache::CACHE_MISS; 
  }
  
  public function isCached($url) {
    $key = new Rediska_Key($url);    
    $v = $key->getValue();
    return $v ? QueryCache::CACHE_HIT : QueryCache::CACHE_MISS;
  }
  
  /**
   * save url to cache
   *
   * expire_ttl is in seconds, default 5 minutes
   *
   * returns a boolean
   */
  public function save($url, $value, $expire_ttl=300) {
    $key = new Rediska_Key($url, array('expire'=>$expire_ttl));
    return $key->setValue($value); 
  }
  
  /**
   * remove a url key from cache
   */
  public function delete($url) {
    return $this->rediska->delete($url);
  }

  public static function test() {
    echo "\n\nQueryCache TEST\n==========\n";
    $url = 'https://api.pulseenergy.com/pulse/points.xml';

    $cache = new QueryCache();
    $cache->delete($url);

    echo "Testing request() miss..\n";
    echo "Expect: We missed...";
    $r = $cache->request($url);
    if ($r == QueryCache::CACHE_MISS) {
      echo "We missed";
    }
    echo "\n";

    echo "Testing Save(), isCached()\n";
    echo "Expect: We hit...";
    $test_body = 'dummy text body';
    $r = $cache->save($url,$test_body);
    $r = $cache->isCached($url);
    if ($r == QueryCache::CACHE_HIT) {
      echo "We hit";
    } else {
      echo "We missed";
    }
    echo "\n";
    echo "Expect body: $test_body..";
    $r = $cache->request($url);
    echo $r;

    echo "\nRemoving test key..\n";
    $cache->delete($url);

    $seconds=5;
    echo "\nTimer test.. for $seconds seconds\n";
    $cache->save($url,'timer body',$seconds);
    echo "Is cached now.. ";
    echo ($cache->isCached($url)) ? 'yes' : 'no';
    echo "\n";
    echo "Sleeping..";
    sleep($seconds + 1);
    echo "\nIs cached now.. ";
    echo ($cache->isCached($url)) ? 'yes' : 'no';
    echo "\n";
    echo "\n";


    echo "\nRemoving test key..\n";
    $cache->delete($url);

  }


}


//QueryCache::test();

?>
