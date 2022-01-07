<?php
include_once('../../../config/symbini.php');
include_once('../classes/GamesWhereManager.php');
include_once('../classes/SearchArea.php');
header("Content-Type: text/html; charset=".$CHARSET);

$debug = (isset($_GET['Debug'])?true:false);
$stateString = (isset($_GET['States'])?$_GET['States']:'');
$minLat = (isset($_GET['MinLat'])?$_GET['MinLat']:0);
$maxLat = (isset($_GET['MaxLat'])?$_GET['MaxLat']:0);
$minLon = (isset($_GET['MinLon'])?$_GET['MinLon']:0);
$maxLon = (isset($_GET['MaxLon'])?$_GET['MaxLon']:0);

//Sanitation


$whereManager = new GamesWhereManager();

//$debug=true;
if($debug){
	$whereManager->setDebugMode(true);
	echo "Error: MinLat=".substr($minLat,0,5)."&MaxLat=".substr($maxLat,0,5)."&MinLon=".substr($minLon,0,5)."&MaxLon=".substr($maxLon,0,5)."<br>";
	if($debug == 2) die();
}

//*************************************************************************
//Update hit list
$Endtime = getdate(strtotime('tomorrow-7 hours'));
$Today = getdate(time()-3600*7);
$Date = $Today[month]."-".$Today[mday]."-".$Today[year];
$Sessions = json_decode(file_get_contents("Sessions.txt"),true);

if(!isset($_COOKIE['Where']) || !isset($Sessions[$Date]))
	{
	if(array_key_exists($Date,$Sessions))
		$Sessions[$Date]++;
	else
		$Sessions[$Date] = 1;
	setcookie("Where","1", strtotime('tomorrow'));
	}
$Hits = json_decode(file_get_contents("Hits.txt"),true);
if(array_key_exists($Date,$Hits))
	$Hits[$Date]++;
else
	$Hits[$Date] = 1;
$OutString = json_encode($Hits);
$Result = file_put_contents("Hits.txt",$OutString);


$whereManager->setBoundingBox($minLat, $maxLat, $minLon, $maxLon);

//*************************************************************************
//   State limits
//*************************************************************************

//  If any states were selected, limit the search to a rectangle bounded by those states borders.
$MinLatBound = 1000;
$MinLonBound = 1000;
$MaxLatBound = -1000;
$MaxLatBound = -1000;

if($stateString)
	{
	if($stateString == "Full Region")
		$QueryAdd = "";
	else
		{
		$QueryAdd = "AND stateProvince in ('".str_replace(", ","','",$stateString)."') ";
		$States = explode(", ",$stateString);
		$Bounds = file("StateBoundingBoxes.txt");

		//Checks to see that the state selected is actually in view based on rectangular bounds
		foreach($States as $OneState)
			{
			foreach($Bounds as $OneBound)
				{
				$BoundState = explode(",",$OneBound);
				if($BoundState[0] != $OneState)
					continue;

				if($BoundState[1] < $MinLonBound)
					$MinLonBound = $BoundState[1];
				if($BoundState[2]<$MinLatBound)
					$MinLatBound = $BoundState[2];
				if($BoundState[3]>$MaxLonBound)
					$MaxLonBound = $BoundState[3];
				if($BoundState[4] > $MaxLatBound)
					$MaxLatBound = $BoundState[4];
				}
			}
		if($FullArea->MinLon < $MinLonBound)
			$FullArea->MinLon = $MinLonBound;
		if($FullArea->MinLat < $MinLatBound)
			$FullArea->MinLat = $MinLatBound;
		if($FullArea->MaxLon > $MaxLonBound)
			$FullArea->MaxLon = $MaxLonBound;
		if($FullArea->MaxLat > $MaxLatBound)
			$FullArea->MaxLat = $MaxLatBound;
		}
	}
else
	$QueryAdd = "";

//********************

if($stateString != "" && ($FullArea->MinLon > $FullArea->MaxLon || $FullArea->MinLat > $FullArea->MaxLat))
	die("Error: Invalid region.  State ($stateString) not visible?");

//************************************************************************************************************
//**************************** Start of main routine *********************************************************
//************************************************************************************************************

//Load the coordinates that are known to be artifically georeferenced to the middle of a town, state, country, etc.
$TabooCoor = file_get_contents("TabooCoor.txt");
$QueryAdd .= "and CONCAT(decimalLatitude,'#',decimalLongitude) NOT IN ($TabooCoor)";

//Load the coordinates of the ocean exclusion rectangles.
$Oceans = file("Oceans.txt",FILE_IGNORE_NEW_LINES);
if(!$whereManager->CheckOcean($FullArea))
	die("Error: No significant specimens in the selected area.");

if(!$whereManager->hasEnoughRecords()) die("Error:  ".count($SearchArray)." specimens found in whole area.");



//************************************************************************
//  Region zeroed in to.  Find specific location and a list of plants.
//************************************************************************

$SearchArray = array();
$InputArea = clone $FullArea;
do
	{
	$Tries = 0;
	do
		{
		$whereManager->MainQuery($SearchArray,$InputArea); //Find at least 10 and up to 200 plants from a random rectangle inside this region
		}while(count($SearchArray) < 10);

	$Count = count($SearchArray);
	for($RandomChoice = 0;$RandomChoice < 50;$RandomChoice++)
		{//RandomChoice is just a counter.  50 trials should be plenty.
		//Randomly choose one of the specimens from this region.
		$Select = rand(0,$Count-1);
		$Degrees = .1;
		//Search for all plants within 0.1 degrees of this random specimen (about 10 kilometers).
		$CurrentArea = new SearchArea($SearchArray[$Select]['decimalLatitude'] - $Degrees, $SearchArray[$Select]['decimalLatitude'] + $Degrees,$SearchArray[$Select]['decimalLongitude'] - $Degrees, $SearchArray[$Select]['decimalLongitude'] + $Degrees);
		$ResultArray = array();
		$query = "SELECT sciname, decimalLatitude, decimalLongitude, habitat from omoccurrences WHERE sciname NOT LIKE '' AND sciname NOT LIKE 'indet.%' AND decimalLatitude != '' AND (decimalLatitude between {$CurrentArea->MinLat} AND {$CurrentArea->MaxLat}) AND (decimalLongitude between {$CurrentArea->MinLon} AND {$CurrentArea->MaxLon}) $QueryAdd";
		if($debug)
			echo "Query = $query<br><br>";

		//First query SEINet
		$whereManager->QDB($ResultArray,$DB[0],$query);

		//If appropriate, query Neotropica too.
		if($CurrentArea->MinLat < 30 && $CurrentArea->MinLon < -34 && $CurrentArea->MaxLon > -117)
			$whereManager->QDB($ResultArray,$DB[1],$query);

		if($debug)
			echo "Then Found nearby ".count($ResultArray)."<br>";
		if(count($ResultArray) >= 10)
			break; //We found a specimen that has at least 10 specimens nearby.
		}
	$Tries++;
	if($Tries > 200)
		die("Error: Search failed.  Try again. ");//I haven't see this happen...
	}while(count($ResultArray) < 5);
if($debug)
	echo $query."<br><br>";

//************************************************************************
//  Assemble the list of plants to return to the html program.
//************************************************************************
$Plants = array();//This is an array where the keys are scinames, and the values are counts.
$Names = array();
$Count = array();
$LonArray = array(); //Used to find the median coordinates that determine the target location.
$LatArray = array();
$Content = ""; //The string that gets returned to WITW.
$Hint="";
foreach($ResultArray as $One)
	{
	$SciName = $One['sciname'];
	$Preg = preg_split("/[\s]+/", $One['sciname']); //Convert any sciname to "GENUS species", ignoring var., ssp. etc.
	$SciName = $Preg[0]." ".$Preg[1];
	if(array_key_exists($SciName,$Plants))
		$Plants[$SciName]++; //Already in the array, increment.
	else
		$Plants[$SciName] = 1; //Add to the array.

	if($debug)
		$whereManager->printr($Plants,"Plants");

	$LonArray[] = $One['decimalLongitude'];
	$LatArray[] = $One['decimalLatitude'];
	if(strlen($Hint) < strlen($One['habitat']))
		{ //There might be ways to define the character set that would better resolve this.
		$Hint = str_replace('Ã','a',$One['habitat']);
		$Hint = str_replace('a§','c',$Hint);
		$Hint = str_replace('a£','a',$Hint);
		$Hint = str_replace('a¡','A',$Hint);
		$Hint = str_replace('©','c',$Hint);
		$Hint = str_replace('a³','a',$Hint);
		$Hint = str_replace('aº','a',$Hint);
		}
	}

//Determine median coordinates
sort($LonArray);
sort($LatArray);
$Lon = $LonArray[count($LonArray)/2];
$Lat = $LatArray[count($LatArray)/2];

//Randomize, then sort the array of species in descending order of quantity found.
//Randomize first to break up the alphabetical order.
uasort($Plants,function ($Plant1,$Plant2){
	//Pseudo-randomizes the list of plants while preserving the keys (SciName)
	return rand(-1,1);
});
arsort($Plants);

//Take the 20 with the highest count, or if fewer take as many as there are
$Plants = array_slice($Plants,0,20);

//Break the Plants array into Names and Count
$Names = array_keys($Plants);
$Count = array_values($Plants);
$Max = count($Names);

$Content = $Lon.",".$Lat."\r\n";
$Content .= implode(",",$Names)."\r\n";
$Content .= implode(",",$Count)."\r\n";

//************************************************************************
//  Find image urls
//************************************************************************
$Images = array();

for($i=0;$i<$Max;$i++)
	{
	//First look for a sortsequence = 1 image.
	$Images[$i] = $whereManager->GetImageURL($Names[$i],0,true);
	if($Images[$i] == "")
		$Images[$i] = $whereManager->GetImageURL($Names[$i],1,true);
	if($Images[$i] == "")
		{ //Didn't find a 1, now look for another, in diminishing order of likelihood.
		$Images[$i] = $whereManager->GetImageURL($Names[$i],0,false);
		if($Images[$i] == "")
			$Images[$i] = $whereManager->GetImageURL($Names[$i],1,false);
		}
	if($Images[$i] == "")
		$Images[$i] = "images/None.jpg";
	}
$Content .= implode(",",$Images)."\r\n";


$Content .= $Hint."\r\n";
echo $Content;
?>