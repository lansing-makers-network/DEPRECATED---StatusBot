<?php
set_include_path( get_include_path() . PATH_SEPARATOR . dirname(__FILE__) );
require_once("Google/Client.php");
require_once("Google/Service/Calendar.php");

class StatusAdapter extends DbAdapter{
	
	private $statusTableName = "status";
	private $spaceOpenPropName = "space_is_open";
	
	public function __construct(){
		parent::__construct();
	}
	
	public function IsSpaceOpen(){
//	       return array('status' => false, 'updated' => '');
		$res = $this->connection->query("SELECT value, updated FROM {$this->statusTableName} WHERE prop_name='{$this->spaceOpenPropName}'");
		$row = $res->fetch_assoc();
		
		if( $row != null ){
			return array('status' =>Helpers::asBoolean($row["value"]), 'updated' => date('l, F jS @ g:ia',strtotime($row["updated"])), 'raw_updated' => $row['updated']);
			
		} else {
			throw new Exception("Failed to retrieve open status from database");
		}
	}
	
	public function SetIsSpaceOpen($isOpen){
		$boolValue = Helpers::asBoolean($isOpen);
		$dateTime = Helpers::asDateTime();
		if (!$isOpen) {

		   $last_update = $this->IsSpaceOpen();
		   if ($last_update['status'] == true) {
		      $start_time = date('c', strtotime($last_update['raw_updated']));
		      $end_time = date('c');
		      $GoogleClient = new Google_Client();
//		      $GoogleClient->setUseObjects(true);
		      $GoogleClient->setApplicationName('lmn-doorbot');
		      $GoogleClient->setClientId('656101111249-uk80lnjrgh6b60lp7ki8o0t61j5shqk8.apps.googleusercontent.com');
		      $GoogleClient->setAssertionCredentials(
			new Google_Auth_AssertionCredentials(
			       "656101111249-uk80lnjrgh6b60lp7ki8o0t61j5shqk8@developer.gserviceaccount.com",
			       array(
				"https://www.googleapis.com/auth/calendar"
			       ),
           		       file_get_contents("GoogleCred.p12")
			)
		      );

		      $service = new Google_Service_Calendar($GoogleClient);

		      $event = new Google_Service_Calendar_Event();
		      $event->setSummary('Twitter Switch OPEN');
   		      $event->setLocation('LMN');
		      $start = new Google_Service_Calendar_EventDateTime();
		      $start->setDateTime($start_time);
		      $start->setTimeZone('America/Detroit');
		      $event->setStart($start);
		      $end = new Google_Service_Calendar_EventDateTime();
		      $end->setDateTime($end_time);
		      $end->setTimeZone('America/Detroit');
		      $event->setEnd($end);
		      
		      $calendar_id = "lansingmakersnetwork.org_ntpart4sekc3m68fvedrni1asc@group.calendar.google.com";

		      $new_event = null;

		      try {
		             $new_event = $service->events->insert($calendar_id, $event);
			     $new_event_id= $new_event->getId();
		      } catch (Google_ServiceException $e) {
		             throw new Exception("Failed to create Google Event");
		      }
		      $event = $service->events->get($calendar_id, $new_event->getId());
/**		     
 if ($event != null) {
		      echo "Inserted:";
		      echo "EventID=".$event->getId();
		      echo "Summary=".$event->getSummary();
		      echo "Status=".$event->getStatus();

	   	      } else {
		      	echo "Error creating event";
		      } **/
		  }
		  }
		if( !$this->connection->query("UPDATE {$this->statusTableName} SET value='$boolValue', updated='$dateTime' WHERE prop_name='{$this->spaceOpenPropName}'") ){
			throw new Exception("Failed to update open status");
		}
		
	}
	
	
	
}
