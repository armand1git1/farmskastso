<?php
 
 switch ($action)
 {
  default:
   //data to pass for the graph
  // Alll first 15 transactions 
  
  $periodType                                   = "monthly";  // we would like data monthly
  $sensor_type                                  = "rainfall"; // the initial sensor type is rainfall but could be automated
  
  $list_all_farms_rainfall  = array (array ());;    // double list which will receive the value of rainfall
 

 
  $months_sensor_val       =array();
  $year_start              ="2021-01";       // we want the graph from the start of last year


  for ($i=0;$i<11;$i++) {
    $months_sensor_val[$i] = date('Y-m', strtotime(' +' . $i. 'month', strtotime($year_start)));
  }
  

  //for test  
  $test_data_label1   =$months_sensor_val;  
 
  $farms_list = ["","","",""]; // Array of size 4 with farm   
 
  
  $farms_senor_monthly=array (array ());

  for ($i=0;$i<count($farms_list);$i++) {
    $list_all_farms_rainfall  =Array();  
    
   //echo $global['api_url']."/v1/farms/".($i+1)."/stats/".$sensor_type."/".$periodType;

    $list_all_farms_rainfall  = CallAPI("GET", "",$global['api_url']."/v1/farms/".($i+1)."/stats/".$sensor_type."/".$periodType);
    for ($y=0;$y<count($months_sensor_val);$y++) {
      if(isset($list_all_farms_rainfall)) {
     
        $farms_senor_monthly[$i][$y]=0;   
        foreach ($list_all_farms_rainfall->stats as $stats) {      
          if (isset($stats->month) && isset($stats->year)) {    
            $month =$stats->month;
            if (intval($stats->month)<10) {
              $month ="0".$stats->month;    
            }            
             

            if (isset($months_sensor_val[$y]) && (trim($months_sensor_val[$y])== trim($stats->year."-".$month))) {
              if(isset($stats->average)) {
                $farms_senor_monthly[$i][$y]=trim($stats->average);  
              }            
            }
          }
        }           
      } 
    }                  
  }
  
 

  $test_data_serie1    =   $farms_senor_monthly;  
  $test_data_low1  =0;  //origin  


  $link_farms_Friman        ="#";
  $link_farms_PartialTech   ="#";
  $link_farms_Noora         ="#";
  $link_farms_Organic       ="#";

  //echo $link_farms;
  $list_all_farms  = Array();

  $list_all_farms  = CallAPI("GET", "",$global['api_url']."/v1/farms");  // get all farms list
  if (isset($list_all_farms)) {
    foreach ($list_all_farms as $farms) 
    {   
      if(isset($farms->name)) {
        if (($farms->name)=="Friman Metsola Collective") {
          $link_farms_Friman  =$link_farms."&action=location"."&view=".base64_encode($farms->farm_id); 
        }   
        if (($farms->name)=="PartialTech Research Farm") {
          $link_farms_PartialTech  =$link_farms."&action=location"."&view=".base64_encode($farms->farm_id); 
        }   
        if (($farms->name)=="Noora's Farm") {
          $link_farms_Noora  =$link_farms."&action=location"."&view=".base64_encode($farms->farm_id); 
        }   
        if (($farms->name)=="Organic Ossi's Impact That Lasts Plantation") {
          $link_farms_Organic  =$link_farms."&action=location"."&view=".base64_encode($farms->farm_id); 
        }   
      }      
    }
  }

 }
?>
