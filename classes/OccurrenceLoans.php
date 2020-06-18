<?php
include_once($SERVER_ROOT.'/config/dbconnection.php');
include_once('Manager.php');

class OccurrenceLoans extends Manager{

	private $collid = 0;

	function __construct() {
		parent::__construct(null,'write');
	}

	function __destruct(){
		parent::__destruct();
	}

	public function getLoanOutList($searchTerm,$displayAll){
		$retArr = array();
		$sql = 'SELECT l.loanid, l.datesent, l.loanidentifierown, i.institutioncode, l.forwhom, l.dateclosed '.
			'FROM omoccurloans l LEFT JOIN institutions i ON l.iidborrower = i.iid '.
			'WHERE l.collidown = '.$this->collid.' ';
		if($searchTerm){
			$sql .= 'AND l.loanidentifierown LIKE "%'.$searchTerm.'%" ';
		}
		if(!$displayAll){
			$sql .= 'AND ISNULL(l.dateclosed) ';
		}
		$sql .= 'ORDER BY l.loanidentifierown + 1 DESC';
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$retArr[$r->loanid]['loanidentifierown'] = $r->loanidentifierown;
				$retArr[$r->loanid]['institutioncode'] = $r->institutioncode;
				$retArr[$r->loanid]['forwhom'] = $r->forwhom;
				$retArr[$r->loanid]['dateclosed'] = $r->dateclosed;
			}
			$rs->free();
		}
		return $retArr;
	}

	public function getTransInstList($collid){
		$iidArr = array();
		$sql = 'SELECT DISTINCT e.iid, i.institutioncode '.
			'FROM omoccurexchange AS e INNER JOIN institutions AS i ON e.iid = i.iid '.
			'WHERE e.collid = '.$this->collid.' AND e.iid IS NOT NULL '.
			'ORDER BY i.institutioncode';
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$iidArr[$r->iid]['institutioncode'] = $r->institutioncode;
			}
		}
		$sql = 'SELECT rt.iid, e.invoicebalance FROM omoccurexchange AS e '.
			'INNER JOIN (SELECT iid, MAX(exchangeid) AS exchangeid FROM omoccurexchange '.
			'GROUP BY iid,collid HAVING (collid = '.$this->collid.')) AS rt ON e.exchangeid = rt.exchangeid ';
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
					$iidArr[$r->iid]['invoicebalance'] = $r->invoicebalance;
			}
			$rs->free();
		}
		return $iidArr;
	}

	public function getTransactions($collid,$iid){
		$retArr = array();
		$sql = 'SELECT exchangeid, identifier, transactiontype, in_out, datesent, datereceived, '.
			'totalexmounted, totalexunmounted, totalgift, totalgiftdet, adjustment, invoicebalance '.
			'FROM omoccurexchange '.
			'WHERE collid = '.$collid.' AND iid = '.$iid.' '.
			'ORDER BY exchangeid DESC';
		//echo $sql;
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$retArr[$r->exchangeid]['identifier'] = $r->identifier;
				$retArr[$r->exchangeid]['transactiontype'] = $r->transactiontype;
				$retArr[$r->exchangeid]['in_out'] = $r->in_out;
				$retArr[$r->exchangeid]['datesent'] = $r->datesent;
				$retArr[$r->exchangeid]['datereceived'] = $r->datereceived;
				$retArr[$r->exchangeid]['totalexmounted'] = $r->totalexmounted;
				$retArr[$r->exchangeid]['totalexunmounted'] = $r->totalexunmounted;
				$retArr[$r->exchangeid]['totalgift'] = $r->totalgift;
				$retArr[$r->exchangeid]['totalgiftdet'] = $r->totalgiftdet;
				$retArr[$r->exchangeid]['adjustment'] = $r->adjustment;
				$retArr[$r->exchangeid]['invoicebalance'] = $r->invoicebalance;
			}
			$rs->free();
		}
		return $retArr;
	}

	public function getLoanInList($searchTerm,$displayAll){
		$retArr = array();
		$sql = 'SELECT l.loanid, l.loanidentifierborr, l.dateclosed, i.institutioncode, l.forwhom '.
			'FROM omoccurloans l INNER JOIN institutions i ON l.iidowner = i.iid '.
			'WHERE l.collidborr = '.$this->collid.' ';
		if($searchTerm){
			$sql .= 'AND l.loanidentifierborr LIKE "%'.$searchTerm.'%" ';
		}
		if(!$displayAll){
			$sql .= 'AND l.dateclosed IS NULL ';
		}
		$sql .= 'ORDER BY l.loanidentifierborr + 1';
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$retArr[$r->loanid]['loanidentifierborr'] = $r->loanidentifierborr;
				$retArr[$r->loanid]['institutioncode'] = $r->institutioncode;
				$retArr[$r->loanid]['forwhom'] = $r->forwhom;
				$retArr[$r->loanid]['dateclosed'] = $r->dateclosed;
			}
			$rs->free();
		}
		return $retArr;
	}

	public function getLoanOnWayList(){
		$retArr = array();
		$sql = 'SELECT DISTINCT l.loanid, l.loanidentifierown, c2.collectionname '.
				'FROM omcollections c INNER JOIN omoccurloans l ON c.iid = l.iidBorrower '.
				'INNER JOIN omcollections c2 ON l.collidOwn = c2.collid '.
				'WHERE (c.CollID = '.$this->collid.') AND (l.collidBorr IS NULL) AND (l.dateClosed IS NULL)' ;
		//echo $sql;
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$retArr[$r->loanid]['loanidentifierown'] = $r->loanidentifierown;
				$retArr[$r->loanid]['collectionname'] = $r->collectionname;
			}
			$rs->free();
		}
		return $retArr;
	}

	public function getLoanOutDetails($loanid){
		$retArr = array();
		$sql = 'SELECT loanid, loanidentifierown, iidborrower, datesent, totalboxes, '.
			'shippingmethod, datedue, datereceivedown, dateclosed, forwhom, description, '.
			'notes, createdbyown, processedbyown, processedbyreturnown, invoicemessageown '.
			'FROM omoccurloans '.
			'WHERE loanid = '.$loanid;
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$retArr['loanidentifierown'] = $r->loanidentifierown;
				$retArr['iidborrower'] = $r->iidborrower;
				$retArr['datesent'] = $r->datesent;
				$retArr['totalboxes'] = $r->totalboxes;
				$retArr['shippingmethod'] = $r->shippingmethod;
				$retArr['datedue'] = $r->datedue;
				$retArr['datereceivedown'] = $r->datereceivedown;
				$retArr['dateclosed'] = $r->dateclosed;
				$retArr['forwhom'] = $r->forwhom;
				$retArr['description'] = $r->description;
				$retArr['notes'] = $r->notes;
				$retArr['createdbyown'] = $r->createdbyown;
				$retArr['processedbyown'] = $r->processedbyown;
				$retArr['processedbyreturnown'] = $r->processedbyreturnown;
				$retArr['invoicemessageown'] = $r->invoicemessageown;
			}
			$rs->free();
		}
		return $retArr;
	}

	public function getLoanInDetails($loanid){
		$retArr = array();
		$sql = 'SELECT loanid, loanidentifierown, loanidentifierborr, collidown, iidowner, datesentreturn, totalboxesreturned, '.
			'shippingmethodreturn, datedue, datereceivedborr, dateclosed, forwhom, description, numspecimens, '.
			'notes, createdbyborr, processedbyborr, processedbyreturnborr, invoicemessageborr '.
			'FROM omoccurloans '.
			'WHERE loanid = '.$loanid;
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$retArr['loanidentifierown'] = $r->loanidentifierown;
				$retArr['loanidentifierborr'] = $r->loanidentifierborr;
				$retArr['collidown'] = $r->collidown;
				$retArr['iidowner'] = $r->iidowner;
				$retArr['datesentreturn'] = $r->datesentreturn;
				$retArr['totalboxesreturned'] = $r->totalboxesreturned;
				$retArr['shippingmethodreturn'] = $r->shippingmethodreturn;
				$retArr['datedue'] = $r->datedue;
				$retArr['datereceivedborr'] = $r->datereceivedborr;
				$retArr['dateclosed'] = $r->dateclosed;
				$retArr['forwhom'] = $r->forwhom;
				$retArr['description'] = $r->description;
				$retArr['numspecimens'] = $r->numspecimens;
				$retArr['notes'] = $r->notes;
				$retArr['createdbyborr'] = $r->createdbyborr;
				$retArr['processedbyborr'] = $r->processedbyborr;
				$retArr['processedbyreturnborr'] = $r->processedbyreturnborr;
				$retArr['invoicemessageborr'] = $r->invoicemessageborr;
			}
			$rs->free();
		}
		return $retArr;
	}

	public function getExchangeDetails($exchangeId){
		$retArr = array();
		$sql = 'SELECT exchangeid, identifier, collid, iid, transactiontype, in_out, datesent, datereceived, '.
			'totalboxes, shippingmethod, totalexmounted, totalexunmounted, totalgift, totalgiftdet, adjustment, '.
			'invoicebalance, invoicemessage, description, notes, createdby '.
			'FROM omoccurexchange '.
			'WHERE exchangeid = '.$exchangeId;
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$retArr['identifier'] = $r->identifier;
				$retArr['collid'] = $r->collid;
				$retArr['iid'] = $r->iid;
				$retArr['transactiontype'] = $r->transactiontype;
				$retArr['in_out'] = $r->in_out;
				$retArr['datesent'] = $r->datesent;
				$retArr['datereceived'] = $r->datereceived;
				$retArr['totalboxes'] = $r->totalboxes;
				$retArr['shippingmethod'] = $r->shippingmethod;
				$retArr['totalexmounted'] = $r->totalexmounted;
				$retArr['totalexunmounted'] = $r->totalexunmounted;
				$retArr['totalgift'] = $r->totalgift;
				$retArr['totalgiftdet'] = $r->totalgiftdet;
				$retArr['adjustment'] = $r->adjustment;
				$retArr['invoicebalance'] = $r->invoicebalance;
				$retArr['invoicemessage'] = $r->invoicemessage;
				$retArr['description'] = $r->description;
				$retArr['notes'] = $r->notes;
				$retArr['createdby'] = $r->createdby;
			}
			$rs->free();
		}
		return $retArr;
	}

	public function getExchangeValue($exchangeId){
		$exchangeValue = 0;
		$sql = 'SELECT totalexmounted, totalexunmounted FROM omoccurexchange WHERE exchangeid = '.$exchangeId;
		//echo $sql;
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$exchangeValue = (($r->totalexmounted)*2) + ($r->totalexunmounted);
			}
			$rs->free();
		}
		return $exchangeValue;
	}

	public function getExchangeTotal($exchangeId){
		$exchangeTotal = 0;
		$sql = 'SELECT totalexmounted, totalexunmounted, totalgift, totalgiftdet FROM omoccurexchange WHERE exchangeid = '.$exchangeId;
		//echo $sql;
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$exchangeTotal = ($r->totalexmounted) + ($r->totalexunmounted) + ($r->totalgift) + ($r->totalgiftdet);
			}
			$rs->free();
		}
		return $exchangeTotal;
	}

	public function editLoanOut($pArr){
		$statusStr = '';
		$loanid = $pArr['loanid'];
		if(is_numeric($loanid)){
			$sql = '';
			foreach($pArr as $k => $v){
				if($k != 'formsubmit' && $k != 'loanid' && $k != 'collid'){
					$sql .= ','.$k.'='.($v?'"'.$this->cleanInStr($v).'"':'NULL');
				}
			}
			$sql = 'UPDATE omoccurloans SET '.substr($sql,1).' WHERE (loanid = '.$loanid.')';
			if($this->conn->query($sql)){
				$statusStr = 'SUCCESS: information saved';
			}
			else{
				$statusStr = 'ERROR: Editing of loan failed: '.$this->conn->error.'<br/>';
				$statusStr .= 'SQL: '.$sql;
			}
		}
		return $statusStr;
	}

	public function deleteLoan($loanid){
		$status = 0;
		if(is_numeric($loanid)){
			$sql = 'DELETE FROM omoccurloans WHERE (loanid = '.$loanid.')';
			if($this->conn->query($sql)){
				$status = 1;
			}
		}
		return $status;
	}

	public function deleteExchange($exchangeId){
		$status = 0;
		if(is_numeric($exchangeId)){
			$sql = 'DELETE FROM omoccurexchange WHERE (exchangeid = '.$exchangeId.')';
			if($this->conn->query($sql)){
				$status = 1;
			}
		}
		return $status;
	}

	public function editLoanIn($pArr){
		$statusStr = '';
		$loanid = $pArr['loanid'];
		if(is_numeric($loanid)){
			$sql = '';
			foreach($pArr as $k => $v){
				if($k != 'formsubmit' && $k != 'loanid' && $k != 'collid'){
					$sql .= ','.$k.'='.($v?'"'.$this->cleanInStr($v).'"':'NULL');
				}
			}
			$sql = 'UPDATE omoccurloans SET '.substr($sql,1).' WHERE (loanid = '.$loanid.')';
			if($this->conn->query($sql)){
				$statusStr = 'SUCCESS: information saved';
			}
			else{
				$statusStr = 'ERROR: Editing of loan failed: '.$this->conn->error.'<br/>';
				$statusStr .= 'SQL: '.$sql;
			}
		}
		return $statusStr;
	}

	public function editExchange($pArr){
		$statusStr = '';
		$retArr = array();
		$exchangeId = $pArr['exchangeid'];
		$collid = $pArr['collid'];
		$Iid = $pArr['iid'];
		if(is_numeric($exchangeId)){
			$sql = '';
			foreach($pArr as $k => $v){
				if($k != 'formsubmit' && $k != 'exchangeid' && $k != 'collid'){
					$sql .= ','.$k.'='.($v?'"'.$this->cleanInStr($v).'"':'NULL');
				}
			}
			$sql = 'UPDATE omoccurexchange SET '.substr($sql,1).' WHERE (exchangeid = '.$exchangeId.')';
			if($this->conn->query($sql)){
				$statusStr = 'SUCCESS: information saved';
			}
			else{
				$statusStr = 'ERROR: Editing of exchange failed: '.$this->conn->error.'<br/>';
				$statusStr .= 'SQL: '.$sql;
			}

			$sql = 'SELECT invoicebalance FROM omoccurexchange '.
				'WHERE exchangeid =  (SELECT MAX(exchangeid) FROM omoccurexchange '.
				'WHERE (exchangeid < '.$exchangeId.') AND (collid = '.$collid.') AND (iid = '.$Iid.'))';
			if($rs = $this->conn->query($sql)){
				while($r = $rs->fetch_object()){
					$retArr['invoicebalance'] = $r->invoicebalance;
				}
				$rs->free();
			}
			if(!array_key_exists('invoicebalance',$retArr) || !$retArr['invoicebalance']){
				$prevBalance = 0;
			}
			else{
				$prevBalance = $retArr['invoicebalance'];
			}
			$currentBalance = 0;
			if($pArr['transactiontype'] == 'Shipment'){

				if($pArr['in_out'] == 'In'){
					$currentBalance = ($prevBalance - ((($pArr['totalexmounted'])*2) + ($pArr['totalexunmounted'])));
				}
				elseif($pArr['in_out'] == 'Out'){
					$currentBalance = ($prevBalance + ((($pArr['totalexmounted'])*2) + ($pArr['totalexunmounted'])));
				}

			}
			elseif($pArr['transactiontype'] == 'Adjustment'){
				$currentBalance = ($prevBalance + $pArr['adjustment']);
			}
			$sql3 = '';
			$sql3 = 'UPDATE omoccurexchange SET invoicebalance = '.$currentBalance.' WHERE (exchangeid = '.$exchangeId.')';
			if($this->conn->query($sql3)){
				$statusStr .= ' and balance updated.';
			}
		}
		return $statusStr;
	}

	public function createNewLoanOut($pArr){
		$loanid = 0;
		$sql = 'INSERT INTO omoccurloans(collidown,loanidentifierown,iidowner,iidborrower,createdbyown) '.
			'VALUES('.$this->collid.',"'.$this->cleanInStr($pArr['loanidentifierown']).'",(SELECT iid FROM omcollections WHERE collid = '.$this->collid.'), '.
			'"'.$this->cleanInStr($pArr['reqinstitution']).'","'.$this->cleanInStr($pArr['createdbyown']).'") ';
		//echo $sql;
		if($this->conn->query($sql)){
			$loanid = $this->conn->insert_id;
		}
		else{
			$this->errorMessage = 'ERROR: Creation of new loan out failed: '.$this->conn->error.'<br/>';
			//$this->errorMessage .= 'SQL: '.$sql;
		}
		return $loanid;
	}

	public function createNewLoanIn($pArr){
		$loanid = 0;
		$sql = 'INSERT INTO omoccurloans(collidborr,loanidentifierown,loanidentifierborr,iidowner,createdbyborr) '.
			'VALUES('.$this->collid.',"","'.$this->cleanInStr($pArr['loanidentifierborr']).'","'.$this->cleanInStr($pArr['iidowner']).'",
			"'.$this->cleanInStr($pArr['createdbyborr']).'")';
		//echo $sql;
		if($this->conn->query($sql)){
			$loanid = $this->conn->insert_id;
		}
		else{
			$this->errorMessage = 'ERROR: Creation of new loan in failed: '.$this->conn->error.'<br/>';
			//$this->errorMessage .= 'SQL: '.$sql;
		}
		return $loanid;
	}

	public function createNewExchange($pArr){
		$retID = '';
		$sql = 'INSERT INTO omoccurexchange(identifier,collid,iid,transactiontype,createdby) '.
			'VALUES("'.$this->cleanInStr($pArr['identifier']).'",'.$this->collid.',"'.$this->cleanInStr($pArr['iid']).'",
			"'.$this->cleanInStr($pArr['transactiontype']).'","'.$this->cleanInStr($pArr['createdby']).'")';
		if($this->conn->query($sql)){
			$retID = $this->conn->insert_id;
		}
		else{
			$this->errorMessage = 'ERROR: Creation of new exchange failed: '.$this->conn->error.'<br/>';
			//$this->errorMessage .= 'SQL: '.$sql;
		}
		return $retID;
	}

	public function getSpecTotal($loanid){
		$retArr = array();
		$sql = 'SELECT loanid, COUNT(loanid) AS speccount '.
			'FROM omoccurloanslink '.
			'WHERE loanid = '.$loanid.' '.
			'GROUP BY loanid';
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$retArr['speccount'] = $r->speccount;
			}
			$rs->free();
		}
		return $retArr;
	}

	public function getSpecList($loanid){
		$retArr = array();
		$sql = 'SELECT l.loanid, l.occid, IFNULL(o.catalognumber,o.othercatalognumbers) AS catalognumber, '.
			'o.sciname, CONCAT_WS(" ",o.recordedby,IFNULL(o.recordnumber,o.eventdate)) AS collector, '.
			'CONCAT_WS(", ",stateprovince,county,locality) AS locality, l.returndate '.
			'FROM omoccurloanslink AS l LEFT OUTER JOIN omoccurrences AS o ON l.occid = o.occid '.
			'WHERE l.loanid = '.$loanid.' '.
			'ORDER BY o.catalognumber desc,o.catalognumber+1 desc';
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$retArr[$r->occid]['occid'] = $r->occid;
				$retArr[$r->occid]['catalognumber'] = $r->catalognumber;
				$retArr[$r->occid]['sciname'] = $r->sciname;
				$retArr[$r->occid]['collector'] = $r->collector;
				$retArr[$r->occid]['locality'] = $r->locality;
				$retArr[$r->occid]['returndate'] = $r->returndate;
			}
			$rs->free();
		}
		return $retArr;
	}

	//This method is used by the ajax script insertLoanSpecimen.php
	public function addSpecimen($loanid, $catNum){
		if($this->collid && is_numeric($loanid)){
			$occArr = array();
			$sql = 'SELECT occid FROM omoccurrences WHERE (collid = '.$this->collid.') AND (catalognumber = "'.trim($catNum).'") ';
			//echo $sql;
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()) {
				$occArr[] = $r->occid;
			}
			$rs->free();
			//Check to see if number exists in the otherCatalogNumbers
			if(!$occArr){
				$sql = 'SELECT occid FROM omoccurrences WHERE (collid = '.$this->collid.') AND (othercatalognumbers = "'.trim($catNum).'")';
				$rs = $this->conn->query($sql);
				while($r = $rs->fetch_object()) {
					$occArr[] = $r->occid;
				}
				$rs->free();
			}
			//Return results
			if(!$occArr){
				return 0;
			}
			elseif(count($occArr) > 1){
				return 2;
			}
			else{
				$sql = 'INSERT INTO omoccurloanslink(loanid,occid) VALUES ('.$loanid.','.$occArr[0].') ';
				//echo $sql;
				if($this->conn->query($sql)){
					return 1;
				}
				else{
					return 3;
				}
			}
		}
		return 0;
	}

	public function editSpecimen($reqArr){
		$status = "";
		if(!array_key_exists('occid',$reqArr)) return;
		$statusStr = 'SUCCESS: ';
		$occidArr = $reqArr['occid'];
		$applyTask = $reqArr['applytask'];
		$loanid = $reqArr['loanid'];
		if($occidArr){
			if($applyTask == 'delete'){
				$sql = 'DELETE FROM omoccurloanslink WHERE loanid = '.$loanid.' AND (occid IN('.implode(',',$occidArr).')) ';
				$this->conn->query($sql);
				$statusStr .= 'specimens deleted from loan.';
			}
			else{
				$sql = 'UPDATE omoccurloanslink SET returndate = "'.date('Y-m-d H:i:s').'" WHERE loanid = '.$loanid.' AND (occid IN('.implode(',',$occidArr).')) ';
				$this->conn->query($sql);
				$statusStr .= 'specimens checked in.';
			}
		}
		//return $statusStr;
	}

	public function getInvoiceInfo($identifier,$loanType){
		$retArr = array();
		if($loanType == 'exchange'){
			$sql = 'SELECT e.exchangeid, e.identifier, e.iid, '.
			'e.totalboxes, e.shippingmethod, e.totalexmounted, e.totalexunmounted, e.totalgift, e.totalgiftdet, '.
			'e.invoicebalance, e.invoicemessage, e.description, i.contact, i.institutionname, i.institutionname2, '.
			'i.institutioncode, i.address1, i.address2, i.city, i.stateprovince, i.postalcode, i.country '.
			'FROM omoccurexchange AS e LEFT OUTER JOIN institutions AS i ON e.iid = i.iid '.
			'WHERE exchangeid = '.$identifier;
			if($rs = $this->conn->query($sql)){
				while($r = $rs->fetch_object()){
					$retArr['exchangeid'] = $r->exchangeid;
					$retArr['identifier'] = $r->identifier;
					$retArr['iid'] = $r->iid;
					$retArr['totalboxes'] = $r->totalboxes;
					$retArr['shippingmethod'] = $r->shippingmethod;
					$retArr['totalexmounted'] = $r->totalexmounted;
					$retArr['totalexunmounted'] = $r->totalexunmounted;
					$retArr['totalgift'] = $r->totalgift;
					$retArr['totalgiftdet'] = $r->totalgiftdet;
					$retArr['invoicebalance'] = $r->invoicebalance;
					$retArr['invoicemessage'] = $r->invoicemessage;
					$retArr['description'] = $r->description;
					$retArr['contact'] = $r->contact;
					$retArr['institutionname'] = $r->institutionname;
					$retArr['institutionname2'] = $r->institutionname2;
					$retArr['institutioncode'] = $r->institutioncode;
					$retArr['address1'] = $r->address1;
					$retArr['address2'] = $r->address2;
					$retArr['city'] = $r->city;
					$retArr['stateprovince'] = $r->stateprovince;
					$retArr['postalcode'] = $r->postalcode;
					$retArr['country'] = $r->country;
				}
			}
		}
		else{
			$sql = 'SELECT e.loanid, e.loanidentifierown, e.loanidentifierborr, e.datesent, e.totalboxes, e.totalboxesreturned, '.
				'e.numspecimens, e.shippingmethod, e.shippingmethodreturn, e.datedue, e.datereceivedborr, e.forwhom, '.
				'e.description, e.invoicemessageown, e.invoicemessageborr, i.contact, i.institutionname, i.institutionname2, '.
				'i.institutioncode, i.address1, i.address2, i.city, i.stateprovince, i.postalcode, i.country ';
			if($loanType == 'out'){
				$sql .= 'FROM omoccurloans AS e LEFT OUTER JOIN institutions AS i ON e.iidborrower = i.iid ';
			}
			elseif($loanType == 'in'){
				$sql .= 'FROM omoccurloans AS e LEFT OUTER JOIN institutions AS i ON e.iidowner = i.iid ';
			}
			$sql .= 'WHERE loanid = '.$identifier;
			if($rs = $this->conn->query($sql)){
				while($r = $rs->fetch_object()){
					$retArr['loanid'] = $r->loanid;
					$retArr['loanidentifierown'] = $r->loanidentifierown;
					$retArr['loanidentifierborr'] = $r->loanidentifierborr;
					$retArr['datesent'] = $r->datesent;
					$retArr['totalboxes'] = $r->totalboxes;
					$retArr['totalboxesreturned'] = $r->totalboxesreturned;
					$retArr['numspecimens'] = $r->numspecimens;
					$retArr['shippingmethod'] = $r->shippingmethod;
					$retArr['shippingmethodreturn'] = $r->shippingmethodreturn;
					$retArr['datedue'] = $r->datedue;
					$retArr['datereceivedborr'] = $r->datereceivedborr;
					$retArr['forwhom'] = $r->forwhom;
					$retArr['description'] = $r->description;
					$retArr['invoicemessageown'] = $r->invoicemessageown;
					$retArr['invoicemessageborr'] = $r->invoicemessageborr;
					$retArr['contact'] = $r->contact;
					$retArr['institutionname'] = $r->institutionname;
					$retArr['institutionname2'] = $r->institutionname2;
					$retArr['institutioncode'] = $r->institutioncode;
					$retArr['address1'] = $r->address1;
					$retArr['address2'] = $r->address2;
					$retArr['city'] = $r->city;
					$retArr['stateprovince'] = $r->stateprovince;
					$retArr['postalcode'] = $r->postalcode;
					$retArr['country'] = $r->country;
				}
			}
		}
		return $retArr;
	}

	public function getFromAddress($collid){
		$retArr = array();
		$sql = 'SELECT i.institutionname, i.institutionname2, i.phone, '.
			'i.institutioncode, i.address1, i.address2, i.city, i.stateprovince, i.postalcode, i.country '.
			'FROM omcollections AS o LEFT OUTER JOIN institutions AS i ON o.iid = i.iid '.
			'WHERE o.collid = '.$collid.' ';
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$retArr['institutionname'] = $r->institutionname;
				$retArr['institutionname2'] = $r->institutionname2;
				$retArr['phone'] = $r->phone;
				$retArr['institutioncode'] = $r->institutioncode;
				$retArr['address1'] = $r->address1;
				$retArr['address2'] = $r->address2;
				$retArr['city'] = $r->city;
				$retArr['stateprovince'] = $r->stateprovince;
				$retArr['postalcode'] = $r->postalcode;
				$retArr['country'] = $r->country;
			}
			$rs->free();
		}
		return $retArr;
	}

	public function getToAddress($institution){
		$retArr = array();
		$sql = 'SELECT i.contact, i.institutionname, i.institutionname2, i.phone, '.
			'i.institutioncode, i.address1, i.address2, i.city, i.stateprovince, i.postalcode, i.country '.
			'FROM institutions AS i '.
			'WHERE i.iid = '.$institution.' ';
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$retArr['contact'] = $r->contact;
				$retArr['institutionname'] = $r->institutionname;
				$retArr['institutionname2'] = $r->institutionname2;
				$retArr['phone'] = $r->phone;
				$retArr['institutioncode'] = $r->institutioncode;
				$retArr['address1'] = $r->address1;
				$retArr['address2'] = $r->address2;
				$retArr['city'] = $r->city;
				$retArr['stateprovince'] = $r->stateprovince;
				$retArr['postalcode'] = $r->postalcode;
				$retArr['country'] = $r->country;
			}
			$rs->free();
		}
		return $retArr;
	}

	// General AJAX functions
	public function identifierExists($id,$idType){
		$responseCode = 0;
		if($this->collid){
			$sql = '';
			if($idType == 'out'){
				$sql = 'SELECT loanid FROM omoccurloans WHERE loanidentifierown = "'.$id.'" AND collidown = '.$this->collid;
			}
			elseif($idType == 'in'){
				$sql = 'SELECT loanid FROM omoccurloans WHERE loanIdentifierBorr = "'.$id.'" AND collidborr = '.$this->collid;
			}
			elseif($idType == 'ex'){
				$sql = 'SELECT exchangeid FROM omoccurexchange WHERE identifier = "'.$id.'" AND collid = '.$this->collid;
			}
			else{
				return '';
			}
			$rs = $this->conn->query($sql);
			if($rs->num_rows)  $responseCode = 1;
			$rs->free();
		}
		return $responseCode;
	}

	public function generateNextID($idType){
		$retStr = '';
		if($this->collid){
			$sql = '';
			if($idType == 'out'){
				$sql = 'SELECT loanidentifierown AS id FROM omoccurloans WHERE collidown = '.$this->collid.' ORDER BY loanid desc LIMIT 3';
			}
			elseif($idType == 'in'){
				$sql = 'SELECT loanidentifierborr AS id FROM omoccurloans WHERE collidborr = '.$this->collid.' ORDER BY loanid desc LIMIT 3';
			}
			elseif($idType == 'ex'){
				$sql = 'SELECT identifier AS id FROM omoccurexchange WHERE collid = '.$this->collid.' ORDER BY exchangeid desc LIMIT 3';
			}
			else{
				return '';
			}

			if($rs = $this->conn->query($sql)){
				$parsedArr = array();
				while($r = $rs->fetch_object()){
					$id = preg_replace('/[^\d]+/', '-', $r->id);
					$id = preg_replace('/-{2,}/','-',$id);
					$numArr = explode('-',$id);
					$cnt = 0;
					foreach($numArr as $n){
						$parsedArr[$cnt][] = $n;
						$cnt++;
					}
				}
				$rs->free();
				foreach($parsedArr as $vArr){
					$previousValue = '';
					foreach($vArr as $v){
						if($v == $previousValue){
							$retStr = '';
							break;
						}
						if($v++ > $retStr) $retStr = $v++;
						$previousValue = $v;
					}
				}
				if(!$parsedArr) $retStr = 1;
			}
		}
		return $retStr;
	}

	//General look up functions
	public function getInstitutionArr(){
		$retArr = array();
		$sql = 'SELECT i.iid, IFNULL(c.institutioncode,i.institutioncode) as institutioncode, '.
			'i.institutionname '.
			'FROM institutions i LEFT JOIN (SELECT iid, institutioncode, collectioncode, collectionname '.
			'FROM omcollections WHERE colltype = "Preserved Specimens") c ON i.iid = c.iid '.
			'ORDER BY i.institutioncode,c.institutioncode,c.collectionname,i.institutionname';
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$retArr[$r->iid] = $r->institutioncode.' - '.$r->institutionname;
			}
		}
		return $retArr;
	}

	//Setters and getter
	public function setCollId($id){
		if(is_numeric($id)) $this->collid = $id;
	}
}
?>