<?php

class TokenVerifier extends DbAdapter{
	private $accessTokenTableName = "access_tokens";
	
	public function __construct(){
		parent::__construct();
	}
	
	public function IsTokenValid($token){
		$count = 0;
		if( $stmt = $this->connection->prepare("SELECT id FROM {$this->accessTokenTableName} WHERE token = ?")){
			$stmt->bind_param('s', $token);
			$stmt->execute();
			$stmt->store_result();
			$count = $stmt->num_rows();
			$stmt->close();
			}
		else{
			throw new Exception("Unable to validate access token.");
		}
		
		return ($count > 0);
	}
}

?>