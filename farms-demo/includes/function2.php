<?php
if (!defined('MAINFILE_INCLUDED')) 
{
   die('You cannot access this file directly !');exit;
}   


// Return number of month between two dates. 
function nbr_month_2dates($date1, $date2) {
  $date2_year="";
  $date2_month="";
  
  $date1_year ="";
  $date1_month="";
  $year_diff  ="";
  $month_diff ="";
  

  if (isset($date2)) {
    $arr_explode_date =explode("-",$date2);
    $date2_year       =$arr_explode_date[0]; 
    $date2_month      =$arr_explode_date[1];
  }

  if (isset($date1)) {
    $arr_explode_date = explode("-",$date1);
    $date1_year       = $arr_explode_date[0]; 
    $date1_month      = $arr_explode_date[1];
  }

  //echo $date2_month; echo "=="; echo $date1_month; 
  
  $year_diff         = ($date2_year -  $date1_year)*12;
  $month_diff        = ($date2_month + $year_diff) - $date1_month;      

  // diff between years  
  //echo $date2_year; echo "=="; echo $date1_year;
  //echo "2 dates";
  // to continue 
  return $month_diff;
}

// Checking if there is session and destroy it 
function unset_all_redirect(){
   
 //if(isset($_SESSION['login_id']))   unset($_SESSION['login_id']);
 if ((!isset($_SESSION['login_id'])) || (isset($_SESSION['login_id']) && $_SESSION['login_id']==NULL))
 {
  //session_unset();
  //session_destroy();
  // redirectTo('index.php?lang=en');      
 }  
}

 /* Password encryption using hash function */
function pass_encrypt($value1,$option_size)
{
 $options    = ['cost' => $option_size,];
 $hash_value = password_hash($value1, PASSWORD_BCRYPT, $options);  // using bcrypt function 
 return $hash_value;
} 

 /* Password hash key verification  */
function pass_verify($value1,$encrypted_value)
{
 $result     = FALSE;  
 $result     = password_verify($value1, $encrypted_value);  // check
 return $result;
} 


 
 /* Select max pk  */
function max_pk($table, $table_id)
{
   $rq      ="select MAX(".$table_id.") as max_pk FROM ".$table."";   // query   
  /*
  $result  = mysql_query($rq);
  $row     = mysql_fetch_row($result);
  $highest_id = $row[0];
  */
  $arr      = funcQuery($rq);   
  $max_pk   = 10001;  // minumun pk_id 


  if (isset($arr[0]["max_pk"]) && ($arr[0]["max_pk"]>1))
  {    
    $max_pk =   (int)($arr[0]["max_pk"])+1; 
  }  
 return($max_pk); 
}




function return_current_url($lang,$actual_link="",$module_new="")
{ 
 $word_parts   = "";  

 // step 1 : Treat from the language 
 if (isset($lang))
 {

   $index_lang1     =strpos( $actual_link,"?");        // checking if the current link contains "?" 
   if ($index_lang1===FALSE)    $actual_link =$actual_link."?";  
  
   if($lang=="fr")
   { 
    $index_lang21    =strpos( $actual_link,"lang=fr");        // validate the operations 
    if ($index_lang21===FALSE) 
    {
      $index_lang2     =strpos( $actual_link,"lang");        // checking if the current link contains "?" 
      if ($index_lang2!==FALSE)  
      {
       $word_parts  = explode("?lang=en", $actual_link);        
      } 
      else{ 
       $word_parts  = explode("?", $actual_link);       
      }     
     $actual_link     = $word_parts[0]."?"."lang=fr";  
    }         
   }  
  
   if($lang=="en")
   {
    $index_lang21    =strpos( $actual_link,"lang=en");        // validate the operations 
    if ($index_lang21===FALSE) 
    {

      $index_lang2     =strpos( $actual_link,"lang");        // checking if the current link contains "?" 
      if ($index_lang2!==FALSE)  
      {
       $word_parts  = explode("?lang=fr", $actual_link);             
      } 
      else{ 
       $word_parts  = explode("?", $actual_link);       
      }        
      $actual_link = $word_parts[0]."?"."lang=en";     
    }
   } 

  if (isset($word_parts[1])) $actual_link=$actual_link.$word_parts[1];  
 }
 // step 2 : Treat from the module   
 if((isset($module_new)) && $module_new!="")
 { 

   $index_moudule1    =strpos( $actual_link,"module=");        // validate the operations 
   if ($index_moudule1!==FALSE)  
   {
     $word_parts  = explode("&module", $actual_link); 
     $actual_link = $word_parts[0];          
  } 

    
     //$word_parts  = explode("module", $actual_link);
     $actual_link = $actual_link."&module=".$module_new;
 } 
 return $actual_link; 
}

// Treat xml file
function get_array_xml($file)
{
 $xml                  = Array(); 
 if (isset($file))
 {
   $index_menu       =strpos($file,"menu");                      // checking if the file name contains the character "lss_menu"  
   if ((isset($index_menu) && $index_menu!==FALSE))     $indice  ="menu";  
    
   $index_index      =strpos($file,"index");                      // checking if the file name contains the character "lss_menu"  
   if ((isset($index_index) && $index_index!==FALSE))   $indice  ="index";  
                     
   if(file_exists($file) && filesize($file)>0)                      // if file exits and the file's size is more than zero 
   {    
    $xml = simplexml_load_file($file);       
   }            
 }

 return($xml);
}


// select data cond one                  
function select_all($table,$order=" transaction_date ",$order_cond=0) 
{ 
 $table  =$table;   
 $arr    =Array();        // Initialize my array  
 $rq     ="select *from $table ";   // query   
 
 if ($order_cond=1){
  if (trim($order)!="")  $rq .=" order by ".$order." asc";      
 }
 else{
  if (trim($order)!="")  $rq .=" order by ".$order." desc";       
 } 
 
 $arr =funcQuery($rq);  // return new value of the arry  
 return $arr;
} 

// select data cond one     
  // $table : table name              
  // $attr  : table attribut              
  // $val1  : value of the attribut 
  // $cond  : if we want to specify the order of the sorting or not  
  // $order : attribut used for the order
function select_cond_one($table,$attr,$val1,$cond=0,$order="") 
{ 
 $table  =$table;   
 $arr    =Array();        // Initialize my array  
 $rq     ="select *from $table where $table.$attr='$val1'";   // query    
 if ($cond==2){ $rq .=" order by ".$order." asc";}
 if ($cond==3){ $rq .=" order by ".$order." desc";}

 $arr =funcQuery($rq);  // return new value of the array  
 return $arr;
}                                                       

// select data cond two                  
function select_cond_two($table,$attr1,$attr2,$val1,$val2,$cond=0,$order='$attr1') 
{ 
 $table  =$table;   
 $arr    =Array();        // Initialize my array  
 $rq     ="select *from $table where $table.$attr1='$val1' and $table.$attr2='$val2'";   // query   
 
 if ($cond==0)
 {
 $rq .=" order by $order asc";      
 }   
 
 $arr =funcQuery($rq);  // return new value of the arry  
 return $arr;
}          

                   

 function pagination($query,$page=1)
 {
  //case 1 : the categorie is not specified, we select Field1,Field2,Field3  
  if ((!isset($categorie))  or (isset($categorie) && $categorie==""))
  {                
  $currentPage               = $_SERVER["PHP_SELF"];             //  Current page 
  $currentPage               = "index.php?";
  $maxrows                   = 7;                                //  maximun of ligns of information that will be displayed     
  
  $arr_list                  = Array(); 
 
  $result1                   = dbQuery($query);
  $total_row                 = db_num_rows($result1);         
  $startRow                  = 0;   
  if($page>1) $startRow      = (($page-1) * $maxrows);    // This information allows to the query to know his limitation          
  
  $query_limited             = sprintf("%s LIMIT %d, %d",$query,$startRow,$maxrows); // If the starts rows is 0, we will search to 0   to 7,                                 
                               
  $totalPages                = ceil($total_row/$maxrows);    
  $queryString               = "";
  if (!empty($_SERVER['QUERY_STRING']))
  {
   $params = explode("&", $_SERVER['QUERY_STRING']);
   $newParams = array();
   foreach ($params as $param) 
   {
    if (stristr($param, "pg") == false && stristr($param, "totalRows") == false)
    {
      array_push($newParams, $param);
    }
   }
   if (count($newParams) != 0) 
   {
    $queryString             = htmlentities(implode("&", $newParams));
   }
  }
  $currentPage              .= $queryString;;
  $queryString               = sprintf("&totalRows=%d%s", $total_row, $queryString);
    
  $tab        =Array();   
  
  $tab['query_result']       = $query_limited;  
  $tab['pageNum']            = $page;
  $tab['currentPage']        = $currentPage;
  $tab['queryString']        = $queryString;
  $tab['totalPages']         = $totalPages;

  return($tab);   
  }         
 }          
 
 function error_mail($str)        // is empty
 {
  $a=FALSE;       
   if (preg_match("/^([a-zA-Z0-9-])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", trim($str))) $a=TRUE;
  return $a;
 }

 function error_empty($str)       // is empty
 {
  $a                         = TRUE;             
  if (trim($str)=="")   $a   = FALSE;       
  return $a;
 }
 
 function error_number($str)      // is number 
 {  
  // Removing white spaces from string
  $str = str_replace(' ', '', $str); 
  return (preg_match('/^[0-9]*$/', $str)) ? TRUE : FALSE;
 }
 
 function germanNameTitle($str)
 {
   return (preg_match("/^[\w\s�������',\)\(\.\-]+$/", $str)) ? TRUE : FALSE;
 }
 
 function valAdress($str)         // is address
 {         
   //return (preg_match("/^[a-zA-Z0-9�������'\/\-\. ]+ +[0-9]+(|[a-z\/\-\.])+$/", $str)) ? TRUE : FALSE;    
   //return (preg_match("/^[a-zA-Z0-9���������'.,()\/\-\. ]+$/", $str)) ? TRUE : FALSE;    
   return (preg_match("/^[a-zA-Z0-9���������'.,()\/\-\. ]+$/", $str)) ? TRUE : FALSE;    
 }
 
 function isurl($str)           // is url 
 {
   $c = parse_url($str);
   if(is_array($c)){return $c;}else{return FALSE;}
 }
 
 function isbase64($str)
 {  
  //return (preg_match("/^(?:[A-Za-z0-9+/]{4})*(?:[A-Za-z0-9+/]{2}==|[A-Za-z0-9+/]{3}=|[A-Za-z0-9+/]{4})$/", trim($str))) ? TRUE : FALSE;  
  return ( base64_encode(base64_decode($str)) ===$str) ? TRUE : FALSE;  
 }  
 
 function ip2long1($ip, $getVersion = TRUE)
 {
   $version = valVersion($ip);
   if($getVersion === FALSE && $version === FALSE)return FALSE;
   if($getVersion === FALSE && $version === 'ipv4')return ip2long_v4($ip);
   if($getVersion === FALSE && $version === 'ipv6')return ip2long_v6($ip);

   if($getVersion === TRUE && $version === FALSE)return array('version' => FALSE, 'int' => FALSE);
   if($getVersion === TRUE && $version === 'ipv4')return array('version' => $version, 'int' => ip2long_v4($ip));
   if($getVersion === TRUE && $version === 'ipv6')return array('version' => $version, 'int' => ip2long_v6($ip));

    return trigger_error('inalid argument getVersion in ipFormat::ip2long()!', E_USER_ERROR);
  }
  //eof ip2long

 function valVersion($ip)
 {
     if(filter_var($ip, FILTER_FLAG_IPV4))return 'ipv4';
     if(filter_var($ip, FILTER_FLAG_IPV6))return 'ipv6';
     return FALSE;
  }
  //eof valVersion

 function ip2long_v4($ip)
 {
    list(, $result) = unpack('l',pack('l',ip2long($ip) )  );
    return $result;
 }

 function ip2long_v6($ip) {
    $ip_n = inet_pton($ip);
    $bin = '';
    for ($bit = strlen($ip_n) - 1; $bit >= 0; $bit--) {
        $bin = sprintf('%08b', ord($ip_n[$bit])) . $bin;
    }

    if (function_exists('gmp_init')) {
        return gmp_strval(gmp_init($bin, 2), 10);
    } elseif (function_exists('bcadd')) {
        $dec = '0';
        for ($i = 0; $i < strlen($bin); $i++) {
            $dec = bcmul($dec, '2', 0);
            $dec = bcadd($dec, $bin[$i], 0);
        }
        return $dec;
    } else {
        trigger_error('GMP or BCMATH extension not installed!', E_USER_ERROR);
    }
 }

 function long2ip_v6($dec) {
    if (function_exists('gmp_init')) {
        $bin = gmp_strval(gmp_init($dec, 10), 2);
    } elseif (function_exists('bcadd')) {
        $bin = '';
        do {
            $bin = bcmod($dec, '2') . $bin;
            $dec = bcdiv($dec, '2', 0);
        } while (bccomp($dec, '0'));
    } else {
        trigger_error('GMP or BCMATH extension not installed!', E_USER_ERROR);
    }

    $bin = str_pad($bin, 128, '0', STR_PAD_LEFT);
    $ip = array();
    for ($bit = 0; $bit <= 7; $bit++) {
        $bin_part = substr($bin, $bit * 16, 16);
        $ip[] = dechex(bindec($bin_part));
    }
    $ip = implode(':', $ip);
    return inet_ntop(inet_pton($ip));
 }      

// Getting Ip address
function getRealIpAddr()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
    {
      $ip=$_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
    {
      $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else
    {
      $ip=$_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}  
 
 function msg_error()            //msg for email error
 {
 echo"<div style='background-color:#FF0000;width:900px;align:left;'>&nbsp;<strong class='normaltext'>Error : Please give your appreciation to questions by checking button radio and fill textboxes with valid data.</strong></div><br />";
 }                                 
 
 function msg_appr()            //msg for approving email
 {
 echo"<div style='background-color:#71C671;width:900px;align:left;'>&nbsp;<strong class='text'>Thank you. Your information has been collected.</strong></div><br />";
 } 


function format_calendar_date($date_posted) {
 if (isset($date_posted) && $date_posted!=null) {
  if(strpos($date_posted, "/") !== false){
    $arr_date = explode("/", $date_posted); 
    if (is_array($arr_date)) {
      $date_posted =$arr_date[2]."-".$arr_date[1]."-".$arr_date[0];  
    }    
  }
 }
 return $date_posted;      
}
?>