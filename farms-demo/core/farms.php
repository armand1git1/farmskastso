<?php

 switch ($action)
 {
  case "details":
   // display completed details about a transaction 
 
   
      $info_farms_details             =Array();      
      $myfarms_details                =Array();
      $myfamr_index                   =0;
      $Array_sensortype               =Array("temperature","ph","rainfall");
      $sensor_type                    ="temperature";    
      $date_to_comparewith            =""; 
      $array_sorted_data              =Array();

  if (isset($_GET['view'])) {               // get the transaction val 
    $farm_index_decode            = base64_decode($_GET['view']);  // decode and decrease so that it can fit the array logic         
    //echo $farm_index_decode;  
    if(intval($farm_index_decode)>0) {

      //echo $global['api_url']."/v1/farms/".$myfamr_index.""; 
      
      
      $myfamr_index               = $farm_index_decode;             
      $myfarms_details            = CallAPI("GET", "",$global['api_url']."/v1/farms/".$myfamr_index."");  // My farms details
  

      // Search farms based sensor type 
     if (isset($_POST["farm_detail_id"]) && isset($_POST["btn_sentortype"])) 
     {    
      if(isset($_POST["sensor_type"])) {
      $sensor_type                = $_POST["sensor_type"];  // get sensor posted
      }
     }        
      // Everything
      //$info_farms_details         = CallAPI("GET", "",$global['api_url']."/v1/farms/".$myfamr_index."/stats");  // get all farms stats 
    }
    
    //monthly 
    $info_farms_details            = CallAPI("GET", "",$global['api_url']."/v1/farms/".$myfamr_index."/stats/".$sensor_type."/monthly");  // get all farms stats 
   
    // Initializing first date
    if (isset($info_farms_details->stats[0]->year) &&  isset($info_farms_details->stats[0]->month)) {
      $date_to_comparewith         = $info_farms_details->stats[0]->year."-".$info_farms_details->stats[0]->month;    
    }  
    
    // Armand updated here
   //print_r($info_farms_details);
   
   /*
    $ArrFarmStats  = Array(); 
 
       $y= 0;
        foreach($info_farms_details->stats as $farmstats) {
          if (isset($info_farms_details->stats[$y+1]->year) && isset($info_farms_details->stats[$y+1]->month)) {

            // dbInsert("customers_payment_method", $_SESSION["customer_paymethods"][$i]); // insert data into table merchant
           
            $ArrFarmStats['farms_id']           = $info_farms_details->farm_id;
            $ArrFarmStats['sensor_type']        = $sensor_type;
            $ArrFarmStats['month']              = $info_farms_details->stats[$y]->month;
            $ArrFarmStats['year']               = $info_farms_details->stats[$y]->year;
            $ArrFarmStats['average']            = $info_farms_details->stats[$y]->average;
            $ArrFarmStats['median']             = $info_farms_details->stats[$y]->median;
            $ArrFarmStats['standard_deviation'] = $info_farms_details->stats[$y]->standard_deviation;
            dbInsert("farms_stats", $ArrFarmStats); // insert data into table merchant

            $y++;
          }      
    }
    */

    //die("insertion ended");


    
    // sorting farms date desc (bulble sort)
    $i=0;      
    foreach ($info_farms_details->stats as $farmstats) 
    { 
       $y= 0;
        foreach($info_farms_details->stats as $farmstats) {
          $date_to_comparewith                = $info_farms_details->stats[$y]->year."-".$info_farms_details->stats[$y]->month;
          if (isset($info_farms_details->stats[$y+1]->year) && isset($info_farms_details->stats[$y+1]->month)) {


          $date                               = $info_farms_details->stats[$y+1]->year."-".$info_farms_details->stats[$y+1]->month;
                  
          $diff_date                          = nbr_month_2dates($date_to_comparewith, $date); 
          if($diff_date>0) {
            // swaping value
            $swap_month                       = $info_farms_details->stats[$y]->month;
            $swap_year                        = $info_farms_details->stats[$y]->year;
            $swap_average                     = $info_farms_details->stats[$y]->average;
            $swap_median                      = $info_farms_details->stats[$y]->median;
            $swap_median_standard_deviation   = $info_farms_details->stats[$y]->standard_deviation;

            $info_farms_details->stats[$y]->month                = $info_farms_details->stats[$y+1]->month;
            $info_farms_details->stats[$y]->year                 = $info_farms_details->stats[$y+1]->year;
            $info_farms_details->stats[$y]->average              = $info_farms_details->stats[$y+1]->average;
            $info_farms_details->stats[$y]->median               = $info_farms_details->stats[$y+1]->median;
            $info_farms_details->stats[$y]->standard_deviation   = $info_farms_details->stats[$y+1]->standard_deviation;

            $info_farms_details->stats[$y+1]->month                = $swap_month;
            $info_farms_details->stats[$y+1]->year                 = $swap_year;
            $info_farms_details->stats[$y+1]->average              = $swap_average;
            $info_farms_details->stats[$y+1]->median               = $swap_median;
            $info_farms_details->stats[$y+1]->standard_deviation   = $swap_median_standard_deviation;
            }   
           $y++;
          }  
        }
      $i++;
    }

    /*
    echo "<br />";
    echo "<br />";
    echo "===";
    print_r($info_farms_details->stats) ;
    die();  
    */
 
    $total_pages=0;   
    
      if (is_array($info_farms_details) && count($info_farms_details)>0) {  
      $total_pages= count($info_farms_details)/20;
    }
   


  
 } 
 break;

 case "location":
  $info_farms_details             =Array();      
  $myfarms_details                =Array();
  $myfamr_index                   =0;
  $city                           ="tampere";

 if (isset($_GET['view'])) {               // get the transaction val 
    $farm_index_decode            = base64_decode($_GET['view']);  // decode and decrease so that it can fit the array logic         
    //echo $farm_index_decode;  
    if(intval($farm_index_decode)>0) {


      $myfamr_index               = $farm_index_decode;       
    
      $myfarms_details            = CallAPI("GET", "",$global['api_url']."/v1/farms/".$myfamr_index."");  // My farms details

      //print_r($myfarms_details);
     
      if (isset($myfarms_details->location))
      {
        $city = $myfarms_details->location;
      } 
  }

 } 
 break;
  

 default:


 //farms infos  
 //$myfarms_details            = CallAPI("GET", "",$global['api_url']."/v1/farms/".$myfamr_index."");  // My farms details


 //backend-farmDemo/farms_stats/read
 // $farmsList            = CallAPI("GET", "",$global['api_url']."/php classes/examples/rest-api-php-mysql/farms_stats/read");  // My farms details
 /* 
  $farmsList            = CallAPI("GET", "",$global['api_url']."/farms_stats/read");  // My farms details
  echo $global['api_url']."/backend-farmDemo/farms_stats/read";
  print_r($farmsList);
  die();
  */
  

  $list_all_farms  = Array();

  //$list_all_farms  = CallAPI("GET", "",$global['api_url']."/v1/farms");  // get all farms list
  

  $list_all_farms  = CallAPI("GET", "",$global['api_url']."/farms/readAll");  // get all farms list

  

}
?>
