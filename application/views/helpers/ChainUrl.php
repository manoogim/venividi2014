<?php
class My_View_Helper_ChainUrl
{
	function chainUrl($ownerid, $showid, $teamid, $studentid, $classid) {
		$chain = array();
		if (isset($ownerid)) $chain['owner'] = $ownerid;
		if (isset($showid)) $chain['show'] = $showid;
		if (isset($teamid)) $chain['team'] = $teamid;
		if (isset($studentid)) $chain['student'] = $studentid;
		if (isset($classid)) $chain['class'] = $classid;
		return json_encode($chain);
	}
}