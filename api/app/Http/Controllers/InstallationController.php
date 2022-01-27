<?php
namespace App\Http\Controllers;

use App\PortalIndex;
use Illuminate\Http\Request;

class InstallationController extends Controller
{
	/**
	 * Installation controller instance.
	 *
	 * @return void
	 */
	public function __construct(){
	}

	/**
	 * @OA\Get(
	 *	 path="/api/v2/installation",
	 *	 operationId="/api/v2/installation",
	 *	 tags={""},
	 *	 @OA\Response(
	 *		 response="200",
	 *		 description="Returns list of installation",
	 *		 @OA\JsonContent()
	 *	 ),
	 *	 @OA\Response(
	 *		 response="400",
	 *		 description="Error: Bad request. ",
	 *	 ),
	 * )
	 */
	public function showAllPortals(Request $request){
		$this->validate($request, [
			'limit' => 'integer',
			'offset' => 'integer'
		]);
		$limit = $request->input('limit',100);
		$offset = $request->input('offset',0);

		$fullCnt = PortalIndex::count();
		$result = PortalIndex::skip($offset)->take($limit)->get();

		$eor = false;
		$retObj = [
				"offset" => $offset,
				"limit" => $limit,
				"endOfRecords" => $eor,
				"count" => $fullCnt,
				"results" => $result
		];
		return response()->json($retObj);
	}

	/**
	 * @OA\Get(
	 *	 path="/api/v2/installation/{identifier}",
	 *	 operationId="/api/v2/installation/identifier",
	 *	 tags={""},
	 *	 @OA\Parameter(
	 *		 name="identifier",
	 *		 in="path",
	 *		 description="Installation ID or GUID associated with target installation",
	 *		 required=true,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Response(
	 *		 response="200",
	 *		 description="Returns occurrence data",
	 *		 @OA\JsonContent()
	 *	 ),
	 *	 @OA\Response(
	 *		 response="400",
	 *		 description="Error: Bad request. Occurrence identifier is required.",
	 *	 ),
	 * )
	 */
	public function showOnePortal($id, Request $request){
		$portalObj = null;
		if(is_numeric($id)) $portalObj = PortalIndex::find($id);
		else $portalObj = PortalIndex::where('guid',$id)->first();
		if(!$portalObj->count()) $portalObj = ["status"=>false,"error"=>"Unable to locate installation based on identifier"];
		return response()->json($portalObj);
	}

	public function pingPortal(Request $request){
		$portalObj = null;
		if(isset($_ENV['DEFAULT_TITLE']) && isset($_ENV['PORTAL_GUID'])){
			$portalObj['status'] = true;
			$portalObj['portalName'] = $_ENV['DEFAULT_TITLE'];
			$portalObj['guid'] = $_ENV['PORTAL_GUID'];
			$portalObj['managerEmail'] = $_ENV['ADMIN_EMAIL'];
			$portalObj['urlRoot'] = $this->getServerDomain().$_ENV['CLIENT_ROOT'];
			//$portalObj['symbVersion'] = '';
		}
		else{
			$portalObj['status'] = false;
			if(!isset($_ENV['DEFAULT_TITLE'])) $portalObj['error'][] = 'Portal title is NULL';
			if(!isset($_ENV['PORTAL_GUID'])) $portalObj['error'][] = 'Portal GUID is NULL';
			$portalObj['status'] = false;
		}
		return response()->json($portalObj);
	}

	public function portalHandshake($id, Request $request){
		$responseArr = array();
		$portalObj = PortalIndex::where('guid',$id)->get();
		if($portalObj->count()){
			$responseArr['status'] = true;
			$responseArr['message'] = 'Portal previously registered';
		}
		elseif($request->has('endpoint')){
			//Remote installation not yet in system, thus add and then process list from remote
			if($baseUrl = $request->input('endpoint')){
				//Insert portal
				$urlPing = $baseUrl.'/api/v2/installation/ping';
				if($remote = $this->getAPIResponce($urlPing)){
					try {
						$portalObj = PortalIndex::create($remote);
						$responseArr['status'] = true;
						$responseArr['message'] = 'Portal registered successfully';
					} catch(\Illuminate\Database\QueryException $ex){
						$responseArr['status'] = false;
						$responseArr['error'] = 'Registration failed: Unable insert database record: '.$ex->getMessage();
					}
				}
				else{
					$responseArr['status'] = false;
					$responseArr['error'] = 'Registration failed: Unable to obtain data from endpoint: '.$urlSelf;
				}
			}
		}
		else{
			$responseArr['status'] = false;
			$responseArr['error'] = 'Registration failed: Unable to obtain portal endpoint';
		}
		$responseArr['results'] = $portalObj;
		return response()->json($responseArr);
	}

	public function propagateRegistration($id, Request $request){
		//Register all portals listed within remote, if not alreay registered
		if($baseUrl = $request->input('endpoint')){
			$urlInstallation = $baseUrl.'/api/v2/installation';
			if($remoteInstallationArr = $this->getAPIResponce($urlInstallation)){
				$previousRegistered = 0;
				$newRegistration = 0;
				$failedRegistration = 0;
				foreach($remoteInstallationArr['results'] as $portal){
					if(PortalIndex::where('guid',$portal->guid)->count()) $previousRegistered++;
					else{
						//Touch returns successful data return
						$urlTouch = $portal->urlRoot.'/api/installation/'.$_ENV['PORTAL_GUID'].'/touch?endpoint='.htmlentities($this->getServerDomain().$_ENV['CLIENT_ROOT']);
						if($this->getAPIResponce($urlTouch)){
							try{
								$portalObj[] = PortalIndex::create($portal);
								$newRegistration++;
							} catch(\Illuminate\Database\QueryException $ex){
								$responseArr['failed'][] = array('guid'=>$portal->guid,'error'=>'Database error: '.$ex->getMessage());
								$failedRegistration++;
							}
						}
						else{
							$responseArr['failed'][] = array('guid'=>$portal->guid,'error'=>'Unable to touch: '.$ex->getMessage());
							$failedRegistration++;
						}
					}
				}
			}
			else $responseArr['error'] = 'Unable to obtain remote installation listing: '.$urlInstallation;
		}
	}

	public function create(Request $request){
		/*
		$portalIndex = PortalIndex::create($request->all());

		return response()->json($portalIndex, 201);
		*/
	}

	public function update($id, Request $request){
		/*
		$portalIndex = PortalIndex::findOrFail($id);
		$portalIndex->update($request->all());

		return response()->json($portalIndex, 200);
		*/
	}

	public function delete($id){
		/*
		PortalIndex::findOrFail($id)->delete();
		return response('Portal Index deleted successfully', 200);
		*/
	}

	//Helper functions
	private function getAPIResponce($url){
		$resJson = false;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_URL, $url);
		//curl_setopt($ch, CURLOPT_HTTPGET, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		$resJson = curl_exec($ch);
		if(!$resJson){
			$this->errorMessage = 'FATAL CURL ERROR: '.curl_error($ch).' (#'.curl_errno($ch).')';
			return false;
			//$header = curl_getinfo($ch);
		}
		curl_close($ch);
		return json_decode($resJson,true);
	}

	private function getServerDomain(){
		$domain = "http://";
		if((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443) $domain = "https://";
		$domain .= $_SERVER["SERVER_NAME"];
		if($_SERVER["SERVER_PORT"] && $_SERVER["SERVER_PORT"] != 80 && $_SERVER['SERVER_PORT'] != 443) $domain .= ':'.$_SERVER["SERVER_PORT"];
		return $domain;
	}
}