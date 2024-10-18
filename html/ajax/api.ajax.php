<?php
require_once '../../utils.php';

$action = $_GET['action'] ?? '';

if ($action == 'delete'){
    $api = $_GET['api'];
    $sql = 'DELETE FROM sae._api_keys where key = \'' . $api . '\'';
  
    //print $sql;
    request($sql);

}


if ($action == 'update'){
    $perm = array();
    $api = $_GET['api'];
    $sql = 'SELECT * FROM  sae._api_keys where key = \'' . $api . '\''; 
   
    $res = request($sql,1);
    $permission = $res['permission'];
    
    $perm = array('admin' => 1, 'indispo' => 1, 'planning' => 1, 'lister' => 1); 

    $keys = ['admin', 'indispo', 'planning', 'lister'];

    foreach ($keys as $index => $key) {
        if ($permission[$index] == 0) {
            $perm[$key] = 0;
        }
    }
    print json_encode($perm);
    


}
if ($action == 'create'){
    print json_encode(array('admin' => 0, 'indispo' => 0, 'planning' => 0, 'lister' => 0));
}