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



  <h5 class="header center boutique section-title">Farm's Location</h5>

  <div class="row">
      <div class="input-field col s6">   
       <strong>   
         <?php if (isset($myfarms_details) && isset($myfarms_details->name))  echo $myfarms_details->name; ?>
    </strong>  
      </div>

      <div class="input-field col s6">
        &nbsp;
      </div>
 </div>   

 <div id="googleMap" style="width:100%;height:400px; align:center"></div>  

  


     
  </form>

</div>
</div>


<script type="text/javascript">
  var map;
  function getData() {
    $.ajax({
    url: "map_api_wrapper.php?city=<?php echo $city; ?>",
    async: true,
    dataType: 'json',
    success: function (data) {
      //console.log(data);
      //load map
      init_map(data);
    }
  });  
  }
  
  function init_map(data) {

          console.log(data);

        var map_options = {
            //zoom: 14,
            zoom: 10,

            center: new google.maps.LatLng(data['latitude'], data['longitude'])
          }
        map = new google.maps.Map(document.getElementById("googleMap"), map_options);
       marker = new google.maps.Marker({
            map: map,
            position: new google.maps.LatLng(data['latitude'], data['longitude'])
        });
        infowindow = new google.maps.InfoWindow({
            content: data['formatted_address']
        });
        google.maps.event.addListener(marker, "click", function () {
            infowindow.open(map, marker);
        });
        infowindow.open(map, marker);
    }

</script>


<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $global['google_maps_keys']; ?>&callback=getData"></script>


