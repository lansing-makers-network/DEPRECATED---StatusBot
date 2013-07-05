<?php

	
class StatusAdapter extends DbAdapter{
	
	private $statusTableName = "status";
	private $spaceOpenPropName = "space_is_open";
	
	public function __construct(){
		parent::__construct();
	}
	
	public function IsSpaceOpen(){
		$res = $this->connection->query("SELECT value FROM {$this->statusTableName} WHERE prop_name='{$this->spaceOpenPropName}'");
		$row = $res->fetch_assoc();
		
		if( $row != null ){
			return Helpers::asBoolean($row["value"]);
			
		} else {
			throw new Exception("Failed to retrieve open status from database");
		}
	}
	
	public function SetIsSpaceOpen($isOpen){
		$boolValue = Helpers::asBoolean($isOpen);
		
		if( !$this->connection->query("UPDATE {$this->statusTableName} SET value='$boolValue' WHERE prop_name='{$this->spaceOpenPropName}'") ){
			throw new Exception("Failed to update open status");
		}
		
	}
	
	
	
}
