<?php
	require 'Slim/Slim.php';
	
	\Slim\Slim::registerAutoloader();
	include 'AutoLoader.php';
	
	$app = new \Slim\Slim(array('debug' => true));
	
	
	$app->get('/status/', function () use ($app){
		header('Access-Control-Allow-Origin: *');
		$res = $app->response();
		
		try{
			$adapter = new StatusAdapter();
			$status = $adapter->IsSpaceOpen();
			$responseData = array(
				"openStatus" => $status['status'],
				"updated" => $status['updated'],
			);
			
			$res->header('Content-Type', 'application/json');
			echo json_encode($responseData);
		} catch( Exception $e ){
			$res->status(500);
//			$res->header('X-Status-Reason', $e->getMessage());
		}
	});
	
	$app->post('/status/', function() use ($app){
		try{
			$json = $app->request()->getBody();
			$reqData = json_decode($json);
			
			if( $reqData == null )
			  throw new Exception("Missing json body");

			if( !isset($reqData->token) )
			  throw new Exception("Missing token");

			if( !isset($reqData->openStatus) )
			  throw new Exception("Missing status parameter");
			
			$tokenVerifier = new TokenVerifier();
			if( !$tokenVerifier->IsTokenValid($reqData->token) ){
				$app->response()->status(403);
				return;
			}
			
			$adapter = new StatusAdapter();
			$adapter->SetIsSpaceOpen($reqData->openStatus);
			$app->response()->status(201);
			
		} catch (Exception $e) {
			$app->response()->status(400);
//			$app->response()->header('X-Status-Reason', $e->getMessage());
		}
	});
	
	$app->run();
?>
