<?php
class SearchArea
{
	var $MinLat;
	var $MaxLat;
	var $MinLon;
	var $MaxLon;

	function __construct($MinLat=0,$MaxLat=0,$MinLon=0,$MaxLon=0)
	{ //If the user has "spun the globe" (i.e. moved it all the way around) degrees can be greater than 180 or even 360.
		//This compensates for that.

		while($MinLon < -180)
			$MinLon = 360+$MinLon;
			while($MaxLon < -180)
				$MaxLon = 360+$MaxLon;

				while($MinLon > 180)
					$MinLon = $MinLon-360;
					while($MaxLon >180)
						$MaxLon = $MaxLon-360;
						if($MaxLon < $MinLon &&($MaxLon > 0 || $MinLon < 0))
							die("Error: Map too large.");
							if($MinLon > $MaxLon)
								$MinLon = -180;
								$this->MinLat = $MinLat;
								$this->MaxLat = $MaxLat;
								$this->MinLon = $MinLon;
								$this->MaxLon = $MaxLon;
	}

	function SpanLat()
	{
		return $this->MaxLat - $this->MinLat;
	}

	function SpanLon()
	{
		if($this->MinLon >0 && $this->MaxLon < 0)//Straddling the 180 meridian
			return 360 - $this->MinLon +$this->MaxLon;
			else
				return $this->MaxLon - $this->MinLon;
	}
}
?>