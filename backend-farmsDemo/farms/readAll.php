<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");


include_once '../config/Database.php';
include_once '../class/Farms.php';

$database = new Database();
$db = $database->getConnection();
 
$items = new Farms($db);

$result = $items->readAll();

if($result->num_rows > 0){    
    $itemRecords=array();
    //$itemRecords["items"]=array(); 
    $itemRecords =array(); 
	while ($item = $result->fetch_assoc()) { 	
        extract($item); 
        $itemDetails=array(
            "id" => $id,
            "name" => $name,
            "location" =>  $location,
			"established" => $established	
        ); 
        $itemDetails = array_map('utf8_encode', $itemDetails);
        array_push($itemRecords, $itemDetails);
    }    
    http_response_code(200);   
    echo  json_encode($itemRecords);
}else{     
    http_response_code(404);     
    echo json_encode(
        array("message" => "No item found.")
    );
} 