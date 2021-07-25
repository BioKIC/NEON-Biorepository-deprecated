<?php
include_once ($SERVER_ROOT . '/config/dbconnection.php');

class GeographicThesaurus extends Manager
{

    function __construct($type = 'write')
    {
        parent::__construct();
    }

    function __destruct()
    {
        parent::__destruct();
    }

    public function getCoordStatistics()
    {
        $retArr = array();
        $totalCnt = 0;
        $sql = 'SELECT COUNT(*) AS cnt FROM omoccurrences WHERE (collid IN(' . $this->collStr . '))';
        $rs = $this->conn->query($sql);
        while ($r = $rs->fetch_object()) {
            $totalCnt = $r->cnt;
        }
        $rs->free();

        // Full count
        $sql2 = 'SELECT COUNT(occid) AS cnt FROM omoccurrences WHERE (collid IN(' . $this->collStr . ')) AND (decimalLatitude IS NULL) AND (georeferenceVerificationStatus IS NULL) ';
        if ($rs2 = $this->conn->query($sql2)) {
            if ($r2 = $rs2->fetch_object()) {
                $retArr['total'] = $r2->cnt;
                $retArr['percent'] = round($r2->cnt * 100 / $totalCnt, 1);
            }
            $rs2->free();
        }

        return $retArr;
    }

    // Setters and getters
}
?>