<?php

namespace App\Http\Controllers;

use App\Occurrence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OccurrenceController extends Controller{
	/**
	 * Occurrence controller instance.
	 *
	 * @return void
	 */
	public function __construct(){
	}

	/**
	 * @OA\Get(
	 *	 path="/api/v2/occurrence",
	 *	 operationId="/api/v2/occurrence",
	 *	 tags={""},
	 *	 @OA\Parameter(
	 *		 name="catalogNumber",
	 *		 in="query",
	 *		 description="catalogNumber",
	 *		 required=false,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="occurrenceID",
	 *		 in="query",
	 *		 description="occurrenceID",
	 *		 required=false,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="country",
	 *		 in="query",
	 *		 description="country",
	 *		 required=false,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="stateProvince",
	 *		 in="query",
	 *		 description="State, Province, or second level political unit",
	 *		 required=false,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="county",
	 *		 in="query",
	 *		 description="County, parish, or third level political unit",
	 *		 required=false,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="collid",
	 *		 in="query",
	 *		 description="collid - collection identifier in portal",
	 *		 required=false,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="datasetID",
	 *		 in="query",
	 *		 description="dataset ID within portal",
	 *		 required=false,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="family",
	 *		 in="query",
	 *		 description="family",
	 *		 required=false,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="sciname",
	 *		 in="query",
	 *		 description="Scientific Name - binomen only without authorship",
	 *		 required=false,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="eventDate",
	 *		 in="query",
	 *		 description="Date as YYYY, YYYY-MM or YYYY-MM-DD",
	 *		 required=false,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="limit",
	 *		 in="query",
	 *		 description="Controls the number of results per page",
	 *		 required=false,
	 *		 @OA\Schema(type="integer", default=100)
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="offset",
	 *		 in="query",
	 *		 description="Determines the offset for the search results. A limit of 200 and offset of 100, will get the third page of 100 results.",
	 *		 required=false,
	 *		 @OA\Schema(type="integer", default=0)
	 *	 ),
	 *	 @OA\Response(
	 *		 response="200",
	 *		 description="Returns list of occurrences",
	 *		 @OA\JsonContent()
	 *	 ),
	 *	 @OA\Response(
	 *		 response="400",
	 *		 description="Error: Bad request. ",
	 *	 ),
	 * )
	 */
	public function showAllOccurrences(Request $request){
		$this->validate($request, [
			'limit' => ['integer', 'max:300'],
			'offset' => 'integer'
		]);
		$limit = $request->input('limit',100);
		$offset = $request->input('offset',0);

		$conditions = [];
		if($request->has('catalogNumber')) $conditions[] = ['catalogNumber',$request->catalogNumber];
		if($request->has('occurrenceID')) $conditions[] = ['occurrenceID',$request->occurrenceID];
		if($request->has('country')) $conditions[] = ['country',$request->country];
		if($request->has('stateProvince')) $conditions[] = ['stateProvince',$request->stateProvince];
		if($request->has('county')) $conditions[] = ['county','LIKE',$request->county.'%'];
		if($request->has('collid')) $conditions[] = ['collid',$request->collid];
		if($request->has('family')) $conditions[] = ['family',$request->family];
		if($request->has('sciname')) $conditions[] = ['sciname','LIKE',$request->sciname.'%'];
		if($request->has('datasetID')) $conditions[] = ['datasetID',$request->datasetID];
		if($request->has('eventDate')) $conditions[] = ['eventDate','LIKE',$request->eventDate.'%'];


		$fullCnt = Occurrence::where($conditions)->count();
		$result = Occurrence::where($conditions)->skip($offset)->take($limit)->get();

		$eor = false;
		$retObj = [
			"offset" => (int)$offset,
			"limit" => (int)$limit,
			"endOfRecords" => $eor,
			"count" => $fullCnt,
			"results" => $result
		];
		return response()->json($retObj);
	}

	/**
	 * @OA\Get(
	 *	 path="/api/v2/occurrence/{identifier}",
	 *	 operationId="/api/v2/occurrence/identifier",
	 *	 tags={""},
	 *	 @OA\Parameter(
	 *		 name="identifier",
	 *		 in="path",
	 *		 description="occid or specimen GUID (occurrenceID) associated with target occurrence",
	 *		 required=true,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="includeMedia",
	 *		 in="query",
	 *		 description="Whether to include media within output",
	 *		 required=false,
	 *		 @OA\Schema(type="integer")
	 *	 ),
	 *	 @OA\Parameter(
	 *		 name="includeIdentifications",
	 *		 in="query",
	 *		 description="Whether to include full Identification History within output",
	 *		 required=false,
	 *		 @OA\Schema(type="integer")
	 *	 ),
	 *	 @OA\Response(
	 *		 response="200",
	 *		 description="Returns single occurrence record",
	 *		 @OA\JsonContent()
	 *	 ),
	 *	 @OA\Response(
	 *		 response="400",
	 *		 description="Error: Bad request. Occurrence identifier is required.",
	 *	 ),
	 * )
	 */
	public function showOneOccurrence($id, Request $request){
		$this->validate($request, [
			'includeMedia' => 'integer',
			'includeIdentifications' => 'integer'
		]);
		$id = $this->getOccid($id);
		$occurrence = Occurrence::find($id);
		if($occurrence) $occurrence->recordID = DB::table('guidoccurrences')->where('occid',$id)->value('guid');
		if($request->input('includeMedia')) $occurrence->media;
		if($request->input('includeIdentifications')) $occurrence->identification;
		return response()->json($occurrence);
	}


	/**
	 * @OA\Get(
	 *	 path="/api/v2/occurrence/{identifier}/identifications",
	 *	 operationId="/api/v2/occurrence/identifier/identifications",
	 *	 tags={""},
	 *	 @OA\Parameter(
	 *		 name="identifier",
	 *		 in="path",
	 *		 description="occid or specimen GUID (occurrenceID) associated with target occurrence",
	 *		 required=true,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Response(
	 *		 response="200",
	 *		 description="Returns identification records associated with a given occurrence record",
	 *		 @OA\JsonContent()
	 *	 ),
	 *	 @OA\Response(
	 *		 response="400",
	 *		 description="Error: Bad request. Occurrence identifier is required.",
	 *	 ),
	 * )
	 */
	public function showOneOccurrenceIdentifications($id, Request $request){
		$id = $this->getOccid($id);
		$media = Occurrence::find($id)->identification;
		return response()->json($media);
	}

	/**
	 * @OA\Get(
	 *	 path="/api/v2/occurrence/{identifier}/media",
	 *	 operationId="/api/v2/occurrence/identifier/media",
	 *	 tags={""},
	 *	 @OA\Parameter(
	 *		 name="identifier",
	 *		 in="path",
	 *		 description="occid or specimen GUID (occurrenceID) associated with target occurrence",
	 *		 required=true,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Response(
	 *		 response="200",
	 *		 description="Returns media records associated with a given occurrence record",
	 *		 @OA\JsonContent()
	 *	 ),
	 *	 @OA\Response(
	 *		 response="400",
	 *		 description="Error: Bad request. Occurrence identifier is required.",
	 *	 ),
	 * )
	 */
	public function showOneOccurrenceMedia($id, Request $request){
		$id = $this->getOccid($id);
		$media = Occurrence::find($id)->media;
		return response()->json($media);
	}

	/**
	 * @OA\Get(
	 *	 path="/api/v2/occurrence/{identifier}/reharvest",
	 *	 operationId="/api/v2/occurrence/identifier/reharvest",
	 *	 tags={""},
	 *	 @OA\Parameter(
	 *		 name="identifier",
	 *		 in="path",
	 *		 description="occid or specimen GUID (occurrenceID) associated with target occurrence",
	 *		 required=true,
	 *		 @OA\Schema(type="string")
	 *	 ),
	 *	 @OA\Response(
	 *		 response="200",
	 *		 description="Triggers a reharvest event of a snapshot record. If record is Live managed, request is ignored",
	 *		 @OA\JsonContent()
	 *	 ),
	 *	 @OA\Response(
	 *		 response="400",
	 *		 description="Error: Bad request. Occurrence identifier is required.",
	 *	 ),
	 * )
	 */
	public function oneOccurrenceReharvest($id, Request $request){
		$status = false;
		$id = $this->getOccid($id);
		$occurrence = Occurrence::find($id)->media;

		return response()->json($status);
	}

	private function getOccid($id){
		if(!is_numeric($id)){
			$occid = Occurrence::where('occurrenceID', $id)->value('occid');
			if(!$occid) $occid = DB::table('guidoccurrences')->where('guid', $id)->value('occid');
			if(is_numeric($occid)) $id = $occid;
		}
		return $id;
	}

	public function create(Request $request){
		//$occurrence = Occurrence::create($request->all());
		//return response()->json($occurrence, 201);
	}

	public function update($id, Request $request){
		//$occurrence = Occurrence::findOrFail($id);
		//$occurrence->update($request->all());
		//return response()->json($occurrence, 200);
	}

	public function delete($id){
		//Occurrence::findOrFail($id)->delete();
		//return response('Occurrence Deleted Successfully', 200);
	}
}
