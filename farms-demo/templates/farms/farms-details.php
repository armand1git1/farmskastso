<script>
  $( function() {
    $( "#datepicker" ).datepicker();
  } );

  $( function() {
    $( "#datepicker2" ).datepicker();
  } );
  </script>

<div class="row">
<div id="div_1" class="container">
 <form action="" method="post" name="search_trans">

   <?php if(isset($_SESSION['error_transaction']) && (!empty($_SESSION['error_transaction']) && ($_SESSION['error_transaction']==TRUE)))                   {   
  ?>
       <table width="70%" style="margin-left: 70px ">
       
        <?php if(isset($_SESSION['error_transaction']) && $_SESSION['error_transaction']==TRUE) {  ?>
        <tr>
          <td class="text" style="height:3px" width="200" align="center" colspan="4" bgcolor="#26a69a">
            <label  class="darktext" > <?php if (isset($tab_value['details7'])) echo $tab_value['details7'];?>  </label>
          </td>
        </tr>
     <?php  
       if (isset($_SESSION['error_transaction'])) unset($_SESSION['error_transaction']);
      }  ?>
       </table>
  <?php }  ?>





  	<h5 class="header center boutique section-title"><?php if (isset($myfarms_details) && isset($myfarms_details->name))  echo $myfarms_details->name; ?></h5>
  
<div class="row">
      <div class="input-field col s6">      
        <input id="datepicker" type="text" class="validate"  name="date_from" value="<?php if(isset($_POST['date_from'])) {echo $_POST['date_from'];}else{if(isset($_SESSION["date1"])) echo $_SESSION["date1"];}   ?>" autocomplete="off">
        <label for="datepicker">From:(mm/dd/yyyy)</label>
      </div>

      <div class="input-field col s6">
        <input id="datepicker2" type="text" class="validate" name="date_to" value="<?php if(isset($_POST['date_to'])) {echo $_POST['date_to'];}else{if(isset($_SESSION["date2"]))  echo $_SESSION["date2"];} ?>" autocomplete="off">
        <label for="date_to">To:(mm/dd/yyyy)</label>
      </div>
 </div>   


 <div class="row">
      <div class="input-field col s6">      
      Sensor :&nbsp;        <input  type="hidden"  name="farm_detail_id"  maxlength="100" value="">    
      <select name="sensor_type" class="text">
        <!--
        <option value="ANY" <?php if (isset($_POST["sensor_type"]) &&  ($_POST["sensor_type"]=="SELECTED")) echo "selected"; ?>>ANY</option>  
        -->
        <?php 
           
          if(isset( $Array_sensortype) && is_array($Array_sensortype)){
             for($i=0; $i<count($Array_sensortype); $i++) {   
            ?>      
            <option value="<?php if (isset($Array_sensortype[$i])) echo $Array_sensortype[$i]; ?>" <?php if (isset($_POST["sensor_type"]) && ($_POST["sensor_type"]==$i)) {echo "selected";}?>>
                    <?php if  (isset($Array_sensortype[$i])) echo $Array_sensortype[$i]; ?> 
            </option>
          <?php
            }
          } 
          ?>         
       </select>
       
      </div>

      <div class="input-field col s6">&nbsp;</div>
 </div> 
     

 <div class="row">
    <div class="input-field col s6">
      <input class="btn" type="submit" name="btn_sentortype" value="Search" >
    </div>  
 </div> 

 <div class="row">
    <div class="input-field col s6">
    <label  class="darktext" style="font-size:20px" > <?php if (isset($sensor_type)) echo $sensor_type;?>  </label>
    </div>  
 </div> 
 <br />

     <table style="border-spacing: 0px; width:100%; margin-left:auto;margin-right:auto;">
     <!-- Transactions -->
     <thead>
     <tr>
      <th style="width:200px">
          Id 
     </th>
    
     <th>
          Average
     </th>
    
     <th>
          Median
     </th>

     <th>
       Standard deviation
     </th>
     
     <th style="align:righ; width:200px">
       Period    
     </th>
     
    </tr>
    </thead>

    <tbody>
  
    <?php
         
     // list transactions 
     if (isset($info_farms_details)) // Checking if the array exists and contains value
     {      
      $i=0;        
      //if(isset($info_farms_details->measurements)){      
        //foreach ($info_farms_details->measurements as $farms) 
        foreach ($info_farms_details->stats as $farmsstats) 
        {         
 
         //$farms->farm_id;
         $style="height: 5px; background-color: #D3D3D3";    
         $result1 =($i % 2);
        if ($result1==True)    $style="height: 5px; background-color: #FFFFFF";  
       ?>
        <tr style="<?php if (isset($style)) echo $style; ?> ">
         <td style="height:3px; border-radius:0px;" width="50" colspan="1">
          <strong>
           <?php 
              $prefix    ='1000000'; 
              
              if (strlen('100000')>strlen($i)){
               $nbr_diff =strlen('100000')-strlen($i); 
               $prefix   =substr($prefix, 0, (strlen('100000')-1));    
               echo $prefix.$i;
              }else{
                echo $i;
              }
           ?>
          </strong> 
         </a> 
        </td>

        <td>
          <?php if (isset($farmsstats->average)) echo $farmsstats->average; ?>
        </td>

         <td>
           <?php
                  if (isset($farmsstats->median)) echo $farmsstats->median; 
            ?>         
        </td>

        <td>
           <?php
                  if (isset($farmsstats->standard_deviation)) echo $farmsstats->standard_deviation; 
           ?>
        </td>

        <td>
         <?php           
          // Information on my farms  
          $farms_details  = Array();
          $date           = null;     
          $month          = 0;  
          if(isset($farmsstats->month) && isset($farmsstats->year)) {   
              if (isset($farmsstats->month)) {
                $month          = $farmsstats->month;
                if ($farmsstats->month<10) {
                  $month="0".$farmsstats->month; 
                }
              }                   
              $date = $farmsstats->year."-".$month;
          }
          
          
          if (isset($date) && $date!=null) {
              echo $date; 
          }
          
         ?>
        </td>

       </tr>

        <?php    
        if ($i==50) break;
         $i++;  
      }         
    } 
   ?>
    </tbody>
   </table>

   <table style="margin-bottom:10px;align:right;width:100%">
   <tr valign="bottom">
    <td style="text-align: right;" colspan="7">
    
   <?php
 //echo base64_decode($_GET['pg']); echo "==";
       
    for($i=1;$i<=$total_pages;$i++)
    {
           $class ="ui-button-active";
           $link_page=$link_farms."&action=details"."&view=".base64_encode($myfamr_index)."&pg=".base64_encode($i).""; 

           //$link_page=$link_farms."&action=details"."&pg=".base64_encode($i)."";
           //$link  =$link_farms."&pg=".base64_encode($i)."";
           if (isset($_GET["srchdate"]))  $link  =$link_transactions."&pg=".base64_encode($i)."&srchdate";           
           if (isset($_GET["srchstatus"]))  $link  =$link_transactions."&pg=".base64_encode($i)."&srchstatus";
           if (isset($_GET["srchstatus"]) && isset($_GET["srchdate"]))  $link  =$link_transactions."&pg=".base64_encode($i)."&srchdate&srchstatus"; 
            if(
                (!isset($_GET['pg']) && $i==1)
                             ||
                  (isset($_GET['pg']) && base64_decode($_GET['pg'])==$i)
              )
            {
             $class="ui-button";
            }        
    ?>
     <a href="<?php echo $link_page; ?>"><label class="<?php  echo $class; ?>" ><?php echo $i; ?></label></a>&nbsp;
   <?php
    } 
   ?>

   </td>
  </tr>
  </table>
  </form>

</div>
</div>
