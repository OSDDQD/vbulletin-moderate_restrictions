<?php
if (!isset($GLOBALS['vbulletin']->db))
{
	exit;
}

class moderateRestriction 
{

	var $registry = null;
	var $day = null;

	public function __construct ($registry) 
	{
		$this->registry = $registry;
		$this->day = strtotime('1 day', 0);
	}

	public function verify($userid, $range, $type, $limit) 
	{
		$count = self::getData(array(
			"userid" => $userid,
			"range"	 => $range,
			"type"	 => $type
		));

		if($count >= $limit)
			switch ($type) {
				case 'post':
					$result = array(
							"error" => "modrestriction_limit_post",
							"range"	=> $range,
							"limit"	=> $limit
						);
					break;
				
				case 'infraction':
					$result = array(
							"error" => "modrestriction_limit_infraction",
							"range"	=> $range,
							"limit"	=> $limit
						);
					break;
			}

			return $result;

		return false;
 	}

 	public function getData($data = array())
 	{
 		$rangeStart = TIMENOW - ($this->day * $data['range']);

 		switch ($data['type']) {
 			case 'post':
 				$response = $this->registry->db->query_read("
 					SELECT * FROM " . TABLE_PREFIX . "deletionlog
 					WHERE userid = " . $data['userid'] . "
 					AND type = 'post'
 					AND dateline >= " . $rangeStart . "
 					AND dateline <= " . TIMENOW . "
				");
 				break;
 			
 			case 'infraction':
 				$response = $this->registry->db->query_read("
 					SELECT * FROM " . TABLE_PREFIX . "infraction
 					WHERE whoadded = " . $data['userid'] . "
 					AND action <> 2
 					AND actionuserid <>  " . $data['userid'] . "
 					AND dateline >= " . $rangeStart . "
 					AND dateline <= " . TIMENOW . "
				");
 				break;
 		}

 		return $this->registry->db->num_rows($response);

 	}
}