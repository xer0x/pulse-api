<?php
/**
 * HTTP access for Pulse API data
 *
 * Visit: http://pulse.sluglab.com/data/buildings to run this
 */
 
require './library/Pulse.php';


// handle a GET request
function handleGET($type, $id=null) {

  $pulse = new Pulse();

  switch ($type) {
    case 'buildings':
      $r = $pulse->getBuildings($id);
      break;
    case 'points':
      $r = $pulse->getData($id);
      break;
    default:
      return; // ya, ugly, just be blank. no complaining.
  }
  

  if ($r['code'] == 200) {
    // output the response header
    header("HTTP/1.1 200 OK");
    echo json_encode( array('content'=>$r['body']) );
  } else {
    header('HTTP/1.1 500 Internal Server Error');
    header('X-Reason: ' . $r['body']);
    echo $r['body'];
    return;
  }
    
}

// get the original url in a nice format with a leading /
$url = (array_key_exists('url', $_GET) ? '/' . $_GET['url'] : '/');

// now that we have the url, break up the pieces
$parts = explode('/', $url);

// get the record type
$record_type = $parts[1];

// get the record ID if it exists
$record_id = (isset($parts[2]) ? $parts[2] : FALSE);

// get the request method
$method = $_SERVER['REQUEST_METHOD'];

// handle the method
switch($method) {
    case 'GET':                             // handle GET
        
        // process the GET request
//        handleGET($record_type, $record_id);
        //echo "URL: $url\n";
        //echo "Record type: $record_type\nrecord id: $record_id\n";
        
        handleGet($record_type,$record_id);
        
        break;
    case 'POST':                            // handle POST
    case 'PUT':                             // handle PUT
    case 'DELETE':                          // handle DELETE
        break;
}

?>

