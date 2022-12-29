<?php
                             
if(isset($_GET['action']))
{
  if($_GET['action'] == 'login' || $_GET['action'] == 'forgot')
  {
    include_once('login.php');
  }
}  
 /*
@session_start();
if(!isset($_SESSION['login_id']) || !$_SESSION['login_id'])
{
  include_once('templates/login.php');  
  exit;
}  
$login_info = $_SESSION['login_info'];
$login_id   = $_SESSION['login_id'];     */
?>