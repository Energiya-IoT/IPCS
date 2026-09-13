<?php
header("Access-Control-Allow-Origin: *");
require_once("SQLResponseAPI.php");

$action="";
$address="";
$town="";
$poi="";
$country="";
$postcode="";
$from="";
$to="";
$lon="";
$lat="";


if(($_SERVER['REQUEST_METHOD'] == 'GET') || ($_SERVER['REQUEST_METHOD'] == 'POST'))
{
	if(isset($_REQUEST["action"])) $action = $_REQUEST["action"];
	if(isset($_REQUEST["address"])) $address = $_REQUEST["address"];
	if(isset($_REQUEST["town"])) $town = $_REQUEST["town"];
	if(isset($_REQUEST["poi"])) $poi = $_REQUEST["poi"];
	if(isset($_REQUEST["country"])) $country = $_REQUEST["country"];
	if(isset($_REQUEST["ipcs"])) $postcode = $_REQUEST["ipcs"];
	if(isset($_REQUEST["from"])) $from = $_REQUEST["from"];
	if(isset($_REQUEST["to"])) $to = $_REQUEST["to"];
	if(isset($_REQUEST["lon"])) $lon = $_REQUEST["lon"];
	if(isset($_REQUEST["lat"])) $lat = $_REQUEST["lat"];
}

switch($action)
{
	case "getaddresses":
		 $Res = new SQLResponseAPI();
		 $StrRes = $Res->GetAddresses($postcode);
		 echo $StrRes;
		 break;
		
	case "getpostcodes":
		 $Res = new SQLResponseAPI();
		 $StrRes = $Res->GetPostcodes($address, $town, $country, $poi, $postcode);
		 echo $StrRes;
		 break;
		
	case "insertaddress":
		 $Res = new SQLResponseAPI();
		 $StrRes = $Res->InsertAddress($address, $town, $country, $poi, $postcode);
		 echo $StrRes;
		 break;
		 
	case "geocode":
		 $Res = new SQLResponseAPI();
		 $StrRes = $Res->GeneratePostcode($lon, $lat);
		 echo $StrRes;
		 break;

	case "reversegeocode":
		 $Res = new SQLResponseAPI();
		 $StrRes = $Res->ReversePostcode($postcode);
		 echo $StrRes;
		 break;

	case "distance":
		 $Res = new SQLResponseAPI();
		 $StrRes = $Res->CalculateDistance($from, $to);
		 echo $StrRes;
		 break;

    	default:
		 echo "Action unknown".$action.$town.$address.$country.$poi;
		 break;
}

$Res=null;

?>
