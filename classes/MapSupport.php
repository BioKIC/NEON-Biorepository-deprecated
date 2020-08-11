<?php
include_once($SERVER_ROOT.'/classes/Manager.php');

class MapSupport extends Manager{

	public function __construct(){
		parent::__construct();
	}

	public function __destruct(){
		parent::__destruct();
	}

	//Static Map functions
	public static function getStaticMap($coordArr){
		$mapThumbnails = false;
		if(isset($GLOBALS['MAP_THUMBNAILS']) && $GLOBALS['MAP_THUMBNAILS']) $mapThumbnails = $GLOBALS['MAP_THUMBNAILS'];
		$url = $GLOBALS['CLIENT_ROOT'].'/images/mappoint.png';
		if($mapThumbnails){
			$mapboxApiKey = '';
			if(isset($GLOBALS['MAPBOX_API_KEY']) && $GLOBALS['MAPBOX_API_KEY']) $mapboxApiKey = $GLOBALS['MAPBOX_API_KEY'];
			$googleMapApiKey = '';
			if(isset($GLOBALS['GOOGLE_MAP_KEY']) && $GLOBALS['GOOGLE_MAP_KEY']) $googleMapApiKey = $GLOBALS['GOOGLE_MAP_KEY'];
			if($mapboxApiKey){
				$url = '//api.mapbox.com/styles/v1/mapbox/outdoors-v11/static/';
				$overlay = '';
				foreach($coordArr as $coordStr){
					$llArr = explode(',', $coordStr);
					$overlay .= 'pin-s('.$llArr[1].','.$llArr[0].'),';
				}
				$url .= trim($overlay,', ').'/auto/200x200?access_token='.$mapboxApiKey;
			}
			elseif($googleMapApiKey){
				$url = '//maps.googleapis.com/maps/api/staticmap?size=200x200&maptype=terrain';
				if($coordArr) $url .= '&markers=size:tiny|'.implode('|',array_slice($coordArr,0,50));
				$url .= '&key='.$googleMapApiKey;
			}
		}
		return $url;
	}
}
?>