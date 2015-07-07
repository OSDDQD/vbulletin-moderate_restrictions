<?php
if (!isset($GLOBALS['vbulletin']->db))
{
	exit;
}

class moderateRestriction {

	private $registry;
	private $response;
	private $day;

	public function __construct ($registry) 
	{
		$this->registry = $registry;
		$this->response = $response;
		$this->day = $day = strtotime('1 day', 0);
	}

	public static function verify($userid, $range, $type, $max) 
	{
		$data = array(
			"userid" => $userid,
			"range"	 => $range,
			"type"	 => $type
		);

		return self::getData($data);
 	}

 	private static function getData($data = array())
 	{
 		$rangeStart = TIMENOW - ($day * $data['range']);

 		switch ($data['type']) {
 			case 'post':
 				$response = $registry->db->query_read("
 					SELECT COUNT(primaryid) FROM " . TABLE_PREFIX . "deletionlog
 					WHERE userid = " . $data['userid'] . "
 					AND dateline >= " . $rangeStart . "
 					AND dateline <= " . TIMENOW . "
				");
 				break;
 			
 			case 'infraction'
 				$response = $registry->db->query_read("
 					SELECT COUNT(infractionid) FROM " . TABLE_PREFIX . "infraction
 					WHERE whoadded = " . $data['userid'] . "
 					AND dateline >= " . $rangeStart . "
 					AND dateline <= " . TIMENOW . "
				");
 				break;
 		}

 		return $registry->db->fetch_array($response);

 	}
}