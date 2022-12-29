<?php
if (!defined('MAINFILE_INCLUDED'))
{
   die('You cannot access this file directly !');exit;
}

 function connect123()
 {
   if(SITE_MODE == 'LOCAL')
   {
    $global['host']     = "localhost";
    $global['dbname']   = "farms_db_local";
    // $global['dbuser']   = "walkap";
    //$global['dbpasswd'] = "walkap";


     $global['dbuser']   = "root";
     $global['dbpasswd'] = "";
   }
   elseif(SITE_MODE == 'TEST')
   {
     $global['host']     = "walkapnewvusert.mysql.db";
     $global['dbname']   = "walkapnewvusert";
     $global['dbpasswd'] = "Gth459RTb56";
     $global['dbuser']   = "walkapnewvusert";
   }
   elseif(SITE_MODE == 'PRODUCTION')
   {
   //
   }


  $con   = db_connect($global['host'],$global['dbuser'],$global['dbpasswd']);

  if (!$con)
  {
   die('Could not connect with '.$con.' here: ' . mysql_error());
  }else{mysqli_select_db($con,$global['dbname']);}
  return $con;
 }


 $connect = connect123();

?>
