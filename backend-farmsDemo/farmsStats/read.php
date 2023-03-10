<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/Database.php';
include_once '../class/FarmsStats.php';

$database = new Database();
$db = $database->getConnection();
 
$items = new FarmsStats($db);

$items->id = (isset($_GET['id']) && $_GET['id']) ? $_GET['id'] : '0';


$result = $items->readFarmMonthly();

if($result->num_rows > 0){    
    $itemRecords=array();
    $itemRecords["farms_stats"]=array(); 
	while ($item = $result->fetch_assoc()) { 	
        extract($item); 
        
        $itemDetails=array(
            "id" => $id,
            "farms_id" => $farms_id,
            "sensor_type" => $sensor_type,
			"month" => $month,
            "year" => $year,            
			"average" => $average,
            "median" => $median,
            "standard_deviation" => $standard_deviation			
        ); 
       
      
       array_push($itemRecords["farms_stats"], $itemDetails);
    }    
    http_response_code(200);     
    echo json_encode($itemRecords);
}else{     
    http_response_code(404);     
    echo json_encode(
        array("message" => "No item found.")
    );
} 