<?php
include_once($SERVER_ROOT.'/classes/Manager.php');

class GamesWhereManager extends Manager {

	private $stateStr ='';
	private $minLat = 0;
	private $maxLat = 0;
	private $minLon = 0;
	private $maxLon = 0;
	private $debugMode = 0;

	public function __construct(){
		parent::__construct(null,'readonly');
	}

	public function __destruct(){
 		parent::__destruct();
	}

	public function MainQuery(&$ResultArray,$CurrentArea){
		//Main query routine.  Returns the array by reference.
		global $QueryAdd;
		if($this->debugMode)
			$this->printr($CurrentArea,"CurrentArea");

			$Fields = "sciname, decimalLatitude, decimalLongitude, habitat";
			$Mult = 100; //Basically how many decimal places to leave in the floating Lat and Long when finding a random square.  Probably doesn't matter much
			$LatSize = ($CurrentArea->MaxLat - $CurrentArea->MinLat)/50;//Break the region down into rectangles, 50x50 in the full region.
			$LonSize = ($CurrentArea->MaxLon - $CurrentArea->MinLon)/50;
			$RandomArea = new SearchArea();
			do
			{ //Find an area that is not in the ocean.
				$RandomArea->MinLat = rand(floor($Mult*$CurrentArea->MinLat),floor($Mult*$CurrentArea->MaxLat)-$LatSize)/$Mult;
				$RandomArea->MaxLat = $RandomArea->MinLat+$LatSize;
				$RandomArea->MinLon = rand(floor($Mult*$CurrentArea->MinLon),floor($Mult*$CurrentArea->MaxLon)-$LonSize)/$Mult;
				$RandomArea->MaxLon = $RandomArea->MinLon+$LonSize;
			} while(!CheckOcean($RandomArea));

			$query = "SELECT $Fields FROM omoccurrences WHERE sciname NOT LIKE '' AND sciname NOT LIKE 'indet.%' AND decimalLatitude != '' AND decimalLatitude between ".$RandomArea->MinLat." AND ".$RandomArea->MaxLat." AND decimalLongitude between ".$RandomArea->MinLon." AND ".$RandomArea->MaxLon." $QueryAdd LIMIT 200";

			$result = $this->conn->query($query);
			while($One = $result->fetch_assoc())
				$ResultArray[] = $One;
			$result->free();

			if($this->debugMode)
				echo "MQ Count = ".count($ResultArray)."<br>";
	}

	public function CheckOcean($Area){
		//Make sure the rectangle $Area does not lie within an ocean
		//Done before querying the database, much faster than a bunch of queries that turn up empty.
		global $Oceans; //$Oceans is read from file early in the program.
		foreach($Oceans as $O)
		{
			$O = explode(",",$O);
			if(($Area->MinLat > $O[0]) && ($Area->MaxLat < $O[1]) && ($Area->MinLon > $O[2]) && ($Area->MaxLon < $O[3]))
			{
				if($this->debugMode)
					echo "Rejected<br>";
					return false;
			}
		}
		return true;
	}

	public function GetImageURL($SciName, $DBNum, $Sort){
		//Kind of messy.  Determining the best image is not scientific.  If the image sortsequence is 1, then it's probably a good one.  But in many cases the sortsequence is 50 for every image.
		global $DB;
		$TIDQuery = "SELECT TID FROM taxa WHERE SciName LIKE '$SciName%'";
		$TIDresult = $DB[$DBNum]->query($TIDQuery);
		if($TIDresult->num_rows > 0)
		{
			while($OneTID = $TIDresult->fetch_assoc())
			{
				if($TID != "")
					$TID .= ",";
					$TID .= $OneTID['TID'];
			}
		}
		else
			return "";//No TID was found for this plant, so unable to search for an image.

			if($TID == "")
				return "";
				$ImageQuery = "SELECT * FROM images WHERE tid IN ($TID) AND sortsequence = 1 LIMIT 1";
				$ImageResult = $DB[$DBNum]->query($ImageQuery);
				if($ImageResult->num_rows == 0)
				{//Look for the best possible image first.
					if($Sort)
						return "";
						$ImageQuery = "SELECT * FROM images WHERE tid IN ($TID) AND sortsequence > 0 ORDER BY sortsequence";
						$ImageResult = $DB[$DBNum]->query($ImageQuery);
				}

				if($ImageResult->num_rows == 0)
				{
					//die("Error: $SciName, $ImageQuery");
					$ImageQuery = "SELECT * FROM images WHERE tid IN ($TID) AND url != 'empty' AND sortsequence > 0 ORDER BY sortsequence";
					$ImageResult = $DB[$DBNum]->query($ImageQuery);
				}
				if($ImageResult->num_rows == 0)
				{ //Last try.  Any image will be accepted.
					$ImageQuery = "SELECT * FROM images WHERE tid IN ($TID) AND url != 'empty' LIMIT 1";
					$ImageResult = $DB[$DBNum]->query($ImageQuery);
				}
				$OneImageURL = $ImageResult->fetch_assoc();

				$URL = $OneImageURL['url'];
				if(strlen($URL) < 10)
					$URL = $OneImageURL['originalurl'];
					if($URL == "")
						return "";

						if(strstr($URL,"http") === false && strlen($URL) > 10)
						{ //Images that are on the swbiodiversity server are often stored with just local location.  Need to add the http://...
							$URL = "http://swbiodiversity.org".$URL;
						}
						return $URL;
	}

	public function QDB(&$ResultArray,$DB,$query){
		//Query DataBase, return results in array $ResultArray
		$result = $DB->query($query);
		while($One = $result->fetch_assoc())
			$ResultArray[] = $One;
	}

	public function hasEnoughRecords(){
		$bool = false;
		//Check to make sure there are enough specimens in the entire region
		$query = 'SELECT 1 FROM omoccurrences
			WHERE sciname NOT LIKE "" AND sciname NOT LIKE "indet.%"
			AND decimalLatitude between '.$this->minLat.' AND '.$this->maxLat.' AND decimalLongitude between '.$this->minLon.' AND '.$this->maxLon.' ';
		$QueryAdd afdsa;
		$query .= 'LIMIT 20';
		$rs = $this->conn->query($query);
		if($rs->num_rows > 5) $bool = true;
		$rs->free();
		return $bool;
	}

	public function printr($ArrayName, $Caption=""){
		//Encapsulates the print_r() function, adds the name of the array before and a line feed after
		echo "<br>";
		if($Caption != "") echo "$Caption<br>";
		print_r($ArrayName);
		echo "<br>";
	}

	//Setters and getters
	public function setBoundingBox($minLat, $maxLat, $minLon, $maxLon){
		while($minLon < -180) $minLon = 360+$minLon;
		while($maxLon < -180) $maxLon = 360+$maxLon;

		while($minLon > 180) $minLon = $minLon-360;
		while($maxLon >180) $maxLon = $maxLon-360;
		if($maxLon < $minLon && ($maxLon > 0 || $minLon < 0)) die("Error: Map too large.");
		if($minLon > $maxLon) $minLon = -180;
		$this->minLat = $minLat;
		$this->maxLat = $maxLat;
		$this->minLon = $minLon;
		$this->maxLon = $maxLon;

		//Reduce the search area slightly from the visible area so that the target won't fall right on the edge
		//It still might if the random plant selected only has neighbors towards the edge, but unlikely.
		$this->minLat += 0.05*$this->getLatSpan();	//Raise it a little more because of the text on the bottom of the map.
		$this->maxLat -= 0.03*$this->getLatSpan();
		$this->minLon += 0.03*$this->getLonSpan();
		$this->maxLon -= 0.03*$this->getLonSpan();
	}

	public function getLatSpan(){
		return $this->maxLat - $this->minLat;
	}

	public function getLonSpan(){
		if($this->minLon >0 && $this->maxLon < 0) return 360 - $this->minLon +$this->maxLon;		//Straddling the 180 meridian
		else return $this->maxLon - $this->minLon;
	}

	public function setDebugMode($bool){
		if($bool) $this->debugMode = true;
		else $this->debugMode = false;
	}
}
?>