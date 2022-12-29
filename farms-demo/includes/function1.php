<?php
if (!defined('MAINFILE_INCLUDED'))
{
   die('You cannot access this file directly !');exit;
}

function noDirectAccess()
{
   if (!defined('MAINFILE_INCLUDED'))
   {
      die('You cannot access this file directly !');exit;
   }
}

function dbOutput($string)
{
    if (is_string($string)) {
      return trim(stripslashes($string));
    } elseif (is_array($string)) {
      reset($string);
      while (list($key, $value) = each($string)) {
        $string[$key] = dbOutput($value);
      }
      return $string;
    } else {
      return $string;
    }
}

function dbInput($string, $link = 'db_link')
{
    global $link;
    $con    = connect123();

    if (function_exists('mysql_escape_string')) {
      return mysqli_escape_string($con,$string);
    }

    return addslashes(trim($string));
}

function dbQuery($sql)
{
  $con    = connect123();

  $result = mysqli_query($con,$sql) or die(mysqli_error($con));

  return($result);
}

function notEmpty($value)
{
    if (is_array($value))
    {
      if (sizeof($value) > 0)
      {
        return true;
      }
      else
      {
        return false;
      }
    }
    else
    {
      if ( (is_string($value) || is_int($value)) && ($value != '') && ($value != 'NULL') && (strlen(trim($value)) > 0))
      {
        return true;
      }
      else
      {
        return false;
      }
    }
}

// insert into database
function dbInsert($table, $array)
{
  $arrayKeys = array_keys($array);
  $keys = implode(',', $arrayKeys);

  foreach($array as $key => $value)
  {
    if($value=='now()')
    {
    	$newArray[] = "$value";
    }
    else
    {
      $newArray[] = "'" . dbInput($value) . "'";
    }
  }
  $values = implode(',', $newArray);

  $sql = "INSERT into $table ($keys) values ($values)";
  dbQuery($sql);
}

// function to update database
function dbUpdate($table, $array, $where)
{
  if(!is_array($array))
  {
    die('no data to update');
  }
  foreach($array as $key => $value)
  {
    if($value=='now()')
    {
      $newArray[] = $key . ' = ' . dbInput($value);
    }
    else
    {
      $newArray[] = $key . ' = ' . "'" . dbInput($value) . "'";
    }
  }
  $values = implode(',', $newArray);
  $sql = "UPDATE $table set $values WHERE $where";
  dbQuery($sql);
}


function redirectTo($url)
{
  header('Location: ' . $url);
  exit;
}

function safe_query( $query = "")
{
	if ( empty($query) ) RETURN FALSE;

	if( !$result = mysqli_query($query) )
	{
		die("
				The query failed<br>"
				. "&middot; errorno=" . mysql_errno() . "<br>"
				. "&middot; error=" . mysql_error() . "<br>"
				 . "<pre>$query</pre>"
			);
	}
	else
	{
		global $query_count;
		$query_count++;
		global $query_count;
		return $result;
	}
}

#
# Database abstract layer functions
#
function db_connect($sql_host, $sql_user, $sql_password) {
        $myconnect= mysqli_connect($sql_host, $sql_user, $sql_password);
        mysqli_set_charset($myconnect, "utf8");  // manage special characters
        return $myconnect;
}

function db_select_db($sql_db) {
        return mysqli_select_db($sql_db) || die("Could not connect to SQL db");
}

function db_query($query) {

		global $debug_mode;
		global $mysql_autorepair;


		if(defined("START_TIME")) {
			global $__sql_time;
			$t = func_microtime();
		}
		$result = mysqli_query($query);
		if(defined("START_TIME")) {
			$__sql_time += func_microtime()-$t;
		}

		#
		# Auto repair
		#
		if( !$result && $mysql_autorepair && preg_match("/'(\S+)\.(MYI|MYD)/",mysqli_error(), $m) ){
			$stm = "REPAIR TABLE $m[1]";
			error_log("Repairing table $m[1]", 0);
			if ($debug_mode == 1 || $debug_mode == 3) {
				$mysql_error = mysqli_errno()." : ".mysqli_error();
				echo "<B><FONT COLOR=DARKRED>Repairing table $m[1]...</FONT></B>$mysql_error<BR>";
				flush();
			}
			$result = mysqli_query($stm);
			if (!$result)
				error_log("Repaire table $m[1] is failed: ".mysqli_errno()." : ".mysqli_error(), 0);
			else
				$result = mysqli_query($query); # try repeat query...
		}

		if (db_error($result, $query) && $debug_mode==1)
			exit;
		return $result;
}

function db_result($result, $offset) {
        return mysql_result($result, $offset);
}

function db_fetch_row($result) {
        return mysql_fetch_row($result);
}

function db_fetch_array($result, $flag=MYSQL_ASSOC) {
    return mysql_fetch_array($result, $flag);
}

function db_free_result($result) {
        @mysql_free_result($result);
}

function db_num_rows($result) {
       return mysqli_num_rows($result);
}

function db_insert_id() {
       return mysql_insert_id();
}

function db_affected_rows() {
	return mysql_affected_rows();
}

function db_error($mysql_result, $query) {
	global $debug_mode, $error_file_size_limit, $error_file_path, $PHP_SELF;
	global $config, $login, $REMOTE_ADDR, $current_location;

	if ($mysql_result)
		return false;
	else {
		$back_trace = func_get_backtrace();

		$mysql_error = mysql_errno()." : ".mysql_error();
		if (@$config["Email_Note"]["admin_sqlerror_notify"]=="Y") {
			x_session_register("login");
			$err_str  = "Date        : ".date("d-M-Y H:i:s")."\n";
			$err_str .= "Site        : ".$current_location."\n";
			$err_str .= "Script      : ".$PHP_SELF."\n";
			$err_str .= "Remote IP   : $REMOTE_ADDR\n";
			$err_str .= "Logged as   : $login\n";
			$err_str .= "SQL query   : $query\n";
			$err_str .= "Error code  : ".mysql_errno()."\n";
			$err_str .= "Description :\n\n".mysql_error()."\n";
			$err_str .= "Backtrace   :\n".implode("\n", $back_trace);
			func_send_simple_mail($config["Company"]["site_administrator"], $config["Company"]["company_name"].": SQL Error notification", $err_str, $config["Company"]["site_administrator"]);
		}
		if ($debug_mode == 1 || $debug_mode == 3) {
			echo "<B><FONT COLOR=DARKRED>INVALID SQL: </FONT></B>$mysql_error<BR>";
			echo "<B><FONT COLOR=DARKRED>SQL QUERY FAILURE:</FONT></B> $query <BR>";
			flush();
		}
		if ($debug_mode == 2 || $debug_mode == 3) {
			$filename = $error_file_path."/x-errors_sql.txt";
			if ($error_file_size_limit!=0 && @filesize($filename)>$error_file_size_limit*1024)
				@unlink($filename);
			if ($fp = @fopen($filename, "a+")) {
				$err_str = date("[d-M-Y H:i:s]")." SQL error: $PHP_SELF\n".$query."\n".$mysql_error;
				$err_str .= "\nBacktrace:\n".implode("\n", $back_trace);
				$err_str .= "\n-------------------------------------------------\n";
				fwrite($fp, $err_str);
				fclose($fp);
			}
		}
	}
	return true;
}

#
# Execute mysql query adn store result into associative array with
# column names as keys...
#
function funcQuery($query) {

        $result = false;
        if ($p_result = dbQuery($query)) {
 	       while($arr = mysqli_fetch_array($p_result))
				$result[]=$arr;
				mysqli_free_result($p_result);
        }

        return $result;
}

#
# Execute mysql query and store result into associative array with
# column names as keys and then return first element of this array
# If array is empty return array().
#
function func_query_first($query) {

		if ($p_result = db_query($query)) {
			$result = db_fetch_array($p_result);
			db_free_result($p_result);
        }
        return is_array($result)?$result:array();
}

#
# Execute mysql query and store result into associative array with
# column names as keys and then return first cell of first element of this array
# If array is empty return false.
#
function func_query_first_cell($query) {
	if ($p_result = db_query($query)) {
		$result = db_fetch_row($p_result);
		db_free_result($p_result);
	}
	return is_array($result)?$result[0]:false;
}

#
# Function to get backtrace for debugging
#
function func_get_backtrace($skip=0) {
	$result = array();
	if (!function_exists('debug_backtrace')) {
		$result[] = '[func_get_backtrace() is supported only for PHP version 4.3.0 or better]';
		return $result;
	}

	$trace = debug_backtrace();
	if (is_array($trace) && !empty($trace)) {
		if ($skip>0) {
			if ($skip < count($trace))
				$trace = array_splice($trace, $skip);
			else
				$trace = array();
		}

		foreach ($trace as $item) {
			$result[] = $item['file'].':'.$item['line'];
		}
	}

	if (empty($result)) {
		$result[] = '[empty backtrace]';
	}

	return $result;
}


//function to build select dropdown for dates
function datedropdowns($dateval,$offsetday,$field){
 global $global;

 if($dateval>0){
   $dateval = $dateval+ $offsetday*24*60*60;
 }else
 $dateval = $global['current_time']+ $offsetday*24*60*60;

  $day = date("d",$dateval);
  $month = date("m",$dateval);
  $year =  date("Y",$dateval);

  $mondays = 31;
  if($month==1||$month==3||$month==5||$month==7||$month==8||$month==10||$month==12)
  $mondays = 31;
  elseif($month==2 AND ($year%4==0))
  $mondays = 29;
  elseif($month==2 AND ($year%4!=0))
  $mondays = 28;
  else
  $mondays = 31;

  $select = NULL;

  $select .= '<select name="'.$field.'_day"><option value="" selected>Day</option>';

  for($i=1;$i<=$mondays;$i++){
   $select .= '<option value="'.$i.'" '.(($i==$day)?"selected ":"").'>'.$i.'</option>';
  }
  $select .= '</select>';

  $select .= '&nbsp;<select name="'.$field.'_month"><option value="" selected>Month</option>';

  for($i=1;$i<=12;$i++){
   $select .= '<option value="'.$i.'" '.(($i==$month)?"selected ":"").'>'.$i.'</option>';
  }
  $select .= '</select>';

  $select .= '&nbsp;<select name="'.$field.'_year"><option value="" selected>Year</option>';
  for($i=$year-1;$i<=$year+3;$i++){
   $select .= '<option value="'.$i.'" '.(($i==$year)?"selected ":"").'>'.$i.'</option>';
  }
  $select .= '</select>';

  return $select;

}


    //function to add leading zero
    function fixed_length_string($value, $places){
    if(is_numeric($value)){
        for($x = 1; $x <= $places; $x++){
            $ceiling = pow(10, $x);
            if($value < $ceiling){
                $zeros = $places - $x;
                for($y = 1; $y <= $zeros; $y++){
                    $leading .= "0";
                }
            $x = $places + 1;
            }
        }
        $output = $leading . $value;
    }
    else{
        $output = $value;
    }
     return $output;
    }

// function to dump array
   function dumpVar($var)
   {
     echo '<pre>';
     print_r($var);
     echo '</pre>';
   }

////
// Output a raw date string in the selected locale date format
// $raw_date needs to be in this format: YYYY-MM-DD HH:MM:SS
// NOTE: Includes a workaround for dates before 01/01/1970 that fail on windows servers
  function dateShort($raw_date, $format = 'm/d/Y') {
    if ( ($raw_date == '0000-00-00 00:00:00') || empty($raw_date) ) return false;

    $year = substr($raw_date, 0, 4);
    $month = (int)substr($raw_date, 5, 2);
    $day = (int)substr($raw_date, 8, 2);
    $hour = (int)substr($raw_date, 11, 2);
    $minute = (int)substr($raw_date, 14, 2);
    $second = (int)substr($raw_date, 17, 2);

    if (@date('Y', mktime($hour, $minute, $second, $month, $day, $year)) == $year) {
      return date($format, mktime($hour, $minute, $second, $month, $day, $year));
    } else {
      return ereg_replace('2037' . '$', $year, date('m/d/Y', mktime($hour, $minute, $second, $month, $day, 2037)));
    }
  }

  function dbRecordExists($table, $field, $value, $param='')
  {
    $sql = "SELECT count($field) as total FROM $table WHERE $field='". dbOutput($value) . "' $param";
    $query = dbQuery($sql);
    $result = mysql_fetch_array($query);
    return $result['total'];
  }

  function selectAllSql($table, $where=1, $order_by = '', $group_by = '')
  {
    $group_by = $group_by ? "GROUP BY $order_by" : '';
    $order_by = $order_by ? "ORDER BY $order_by" : '';


   $sql = "SELECT * FROM $table WHERE $where $group_by $order_by";
    return $sql;
  }

  function selectAll($table, $where = 1, $order_by = 'transaction_date asc', $group_by = '')
  {
    $sql   = selectAllSql($table, $where, $order_by, $group_by);
    $query = dbQuery($sql);
    $result['query'] = $query;
    $result['count'] = mysqli_num_rows($query);
    return  $result;
  }

  function dbRecordCheck($table, $field, $where=1)
  {
    $sql = "SELECT count($field) as total FROM $table WHERE $where";
    $query = dbQuery($sql);
    $result = mysql_fetch_array($query);
    return $result['total'];
  }

  function createRandomValue($length, $type = 'mixed') {
    if ( ($type != 'mixed') && ($type != 'chars') && ($type != 'digits')) return false;

    $rand_value = '';
    while (strlen($rand_value) < $length) {
      if ($type == 'digits') {
        $char = makeRand(0,9);
      } else {
        $char = chr(makeRand(0,255));
      }
      if ($type == 'mixed') {
        if (eregi('^[a-z0-9]$', $char)) $rand_value .= $char;
      } elseif ($type == 'chars') {
        if (eregi('^[a-z]$', $char)) $rand_value .= $char;
      } elseif ($type == 'digits') {
        if (ereg('^[0-9]$', $char)) $rand_value .= $char;
      }
    }

    return $rand_value;
  }

  ////
// Return a random value
  function makeRand($min = null, $max = null) {
    static $seeded;

    if (!isset($seeded)) {
      mt_srand((double)microtime()*1000000);
      $seeded = true;
    }

    if (isset($min) && isset($max)) {
      if ($min >= $max) {
        return $min;
      } else {
        return mt_rand($min, $max);
      }
    } else {
      return mt_rand();
    }
  }

function rteSafe($strText) {
	//returns safe code for preloading in the RTE
	$tmpString = $strText;

	//convert all types of single quotes
	$tmpString = str_replace(chr(145), chr(39), $tmpString);
	$tmpString = str_replace(chr(146), chr(39), $tmpString);
	$tmpString = str_replace("'", "&#39;", $tmpString);

	//convert all types of double quotes
	$tmpString = str_replace(chr(147), chr(34), $tmpString);
	$tmpString = str_replace(chr(148), chr(34), $tmpString);
//	$tmpString = str_replace("\"", "\"", $tmpString);

	//replace carriage returns & line feeds
	$tmpString = str_replace(chr(10), " ", $tmpString);
	$tmpString = str_replace(chr(13), " ", $tmpString);

	return $tmpString;
}

function getMenuType()
{
  global $global;

  if(in_array($global['module'], $global['admin_pages']))
  {
    return 'admin';
  }
  return 'manager';
}

function adminAuth()
{
  global $login_info, $global;

  if(in_array($global['module'], $global['admin_pages']) && $login_info['user_privilege'] != 'A')
  {
    die('You have no access to this page !<br><br><a href="index.php">&lt;&lt;Back</a>');exit;
  }
}

function addError($error, $field = '')
{
  global $errors;
  if(trim($field) == '')
  {
    $field = 'any';
  } // if

  $errors[$field]= $error;
  $errors['error'] = 1;
} // addError

  function selectOneRecord($table, $field = '*', $where = 1)
  {
    $sql = "SELECT $field FROM $table WHERE $where LIMIT 1";
    $query = dbQuery($sql);

    $result = mysql_fetch_array($query);
    $result['count'] = mysql_num_rows($query);

    db_free_result($query);

    return  $result;
  }

  function deleteRecord($table, $where)
  {
    $sql = "DELETE FROM $table WHERE $where";
    dbQuery($sql);
  }


  ////
// The HTML image wrapper function
  function printImage($src, $alt = '', $width = '', $height = '', $parameters = '')
  {
    if(empty($src))
    {
      return false;
    }

// alt is added to the img tag even if it is null to prevent browsers from outputting
// the image filename as default
    $image = '<img src="' . $src . '" border="0" alt="' . $alt . '"';

    if (notEmpty($alt)) {
      $image .= ' title=" ' . $alt . ' "';
    }

    if ( defined('CALCULATE_IMAGE_SIZE') && (CALCULATE_IMAGE_SIZE == 'true') && (empty($width) || empty($height))) {
      if ($image_size = @getimagesize($src)) {
        if (empty($width) && notEmpty($height)) {
          $ratio = $height / $image_size[1];
          $width = intval($image_size[0] * $ratio);
        } elseif (notEmpty($width) && empty($height)) {
          $ratio = $width / $image_size[0];
          $height = intval($image_size[1] * $ratio);
        } elseif (empty($width) && empty($height)) {
          $width = $image_size[0];
          $height = $image_size[1];
        }
      } elseif (IMAGE_REQUIRED == 'false') {
        return false;
      }
    }

    if (notEmpty($width) && notEmpty($height)) {
      $image .= ' width="' . $width . '" height="' . $height . '"';
    }

    if (notEmpty($parameters)) $image .= ' ' . $parameters;

    $image .= '>';

    return $image;
  }

  function positionDropdown($value = 0, $name='employee[position]', $id = 'position')
  {
    $sql = selectAll(TABLE_POSITIONS);

    $return = "<select name=\"$name\" id=\"$id\">\n";

    while($result = mysql_fetch_array($sql['query']))
    {
      $selected = $result['position_id']== $value ? ' selected' : '';
      $return .='<option value="' . $result['position_id'] . '"' . $selected . '>' . $result['position_name'] . "</option>\n";
    }
    $return .="</select>\n";

    return $return;
  }

  function dateFromString($date)
  {
    $year = substr($date, 0, 4);
    $month = substr($date, 4, 2);
    $day = substr($date, 6, 2);

    return $month . '/' . $day . '/' . $year;
  }

  function stringFromDate($raw_date, $format = 'ymd')
  {
    $month = trim(substr($raw_date, 0, 2));
    $day = trim(substr($raw_date, 3, 2));
    $year = substr($raw_date, 6, 4);

    switch($format)
    {
      case 'mdy': return $month . $day . $year;
        break;
      case 'ymd': return $year . $month . $day;
        break;
      default: return $month . $day . $year;
        break;
    }

  }

  function q($var)
  {
    $return = "'" . addslashes($var) . "'";
    return $return;
  }

  function customersDropdown($value = 0, $name='job[customer_id]', $id = 'customer')
  {
    $sql = selectAll(TABLE_CUSTOMERS);

    $return = "<select name=\"$name\" id=\"$id\">\n";

    while($result = mysql_fetch_array($sql['query']))
    {
      $selected = $result['customer_id']== $value ? ' selected' : '';
      $return .='<option value="' . $result['customer_id'] . '"' . $selected . '>' . $result['company_name'] . "</option>\n";
    }
    $return .="</select>\n";

    return $return;
  }


  function selectRecords($table, $field = '*', $where = 1, $param = '')
  {
    $sql = "SELECT $field FROM $table WHERE $where $param";
    $query = dbQuery($sql);

    $result['query'] = $query;
    $result['count'] = mysql_num_rows($query);
    return  $result;
  }


  function fullFileValidation($file){
    $allowed_types = array ('image/jpeg', 'image/png','image/jpg','application/pdf' );
    $res= in_array($file['type'],$allowed_types) &&  isset($file);;
    if($res && $file['size'] > maximum_file_size){
       return maximum_file_size_error;
    }
    return $res;
  }

   function checkValidityOfSetOfFile($files){
      foreach($files['name'] as $f=>$file){
        $res=fullFileValidationElement($files,$f);
        if(!$res)
          return $res;
        else if($files['size'][$f]>maximum_file_size){
            return maximum_file_size_error;
        }
      }
      return $res;
    }


    function pdfFileValidation($file){
      $allowed_types = 'application/pdf';
      $res=($file['type']==$allowed_types)  && isset($file['name']);

      if($res && ($file['size'])>maximum_file_size){
         return maximum_file_size_error;
      }
      return $res;
    }

  function fullFileValidationElement($file,$key){
    $allowed_types = array ('image/jpeg', 'image/png','image/jpg','application/pdf');
    return  in_array($file['type'][$key],$allowed_types) && isset($file['name'][$key]);;
  }

  function endsWith($string, $endString)
{
    $len = strlen($endString);
    if ($len == 0) {
        return true;
    }
    return (substr($string, -$len) === $endString);
}
?>
