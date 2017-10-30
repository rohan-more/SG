<?php
 $GLOBALS['link'] = mysqli_connect('localhost', 'root', '', 'slot_machine');
 function validate($input){
//hash, coins won, coins bet, player ID
	$flag = true;
	if(empty($input['coins_won']) and !isset($input['coins_won'])){
		$flag = false;
	}
	
	if(empty($input['coins_bet']) and !isset($input['coins_bet'])){
		$flag = false;
	}
	
	if(empty($input['player_ID']) and !isset($input['player_ID'])){
		$flag = false;
	}
	
	if(empty($input['hash']) and !isset($input['hash'])){
		$flag = false;
	}
	return $flag;
}

function process_request($params){
	
	$sql = 'select * from player where player_ID = '.$params['player_ID'];

	$result = mysqli_query($GLOBALS['link'], $sql);

	if (mysqli_num_rows($result) > 0) {
		// output data of each row
		$row = mysqli_fetch_assoc($result);
		$orghash = hash('md5',$row['salt_value']);
		$hash_from_post = $params['hash'];
		if(0 != strcmp($orghash,$hash_from_post)){
			return false;
		}
		$lifetime_spins = $row['lifetime_spins'] + 1;
		$credits = $row['credits'] - $params['coins_bet'] + $params['coins_won'];
		$data['player_ID'] = $row['player_ID'];
		$sql = 'update player set lifetime_spins ='.$lifetime_spins.',credits = '.$credits.' where player_ID = '.$params['player_ID'];
		$result = mysqli_query($GLOBALS['link'], $sql);
		if($result){
			return true;
		} else {
			return false;
		}
	}
	else {
		return false;
	}
}

function get_response($player_ID){
	
	$sql = 'select * from player where player_ID = '.$player_ID;

	$result = mysqli_query($GLOBALS['link'], $sql);

	if (mysqli_num_rows($result) > 0) {
		// output data of each row
		$row = mysqli_fetch_assoc($result);
		
		return $row;
	}
	else {
		return false;
	}
}


// get the HTTP method, path and body of the request
$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'),true);
 
// connect to the mysql database
$link = mysqli_connect('localhost', 'root', '', 'slot_machine');
mysqli_set_charset($link,'utf8');
 
 
header('Content-Type: application/json');


try {
	switch ($method) {
	  case 'GET':
			$error_response = array('status' => 'false', 'error_code' => '-1', 'error_description' => 'Invalid Method Type');$jsonErrorObj = json_encode($error_response);
			print_r($jsonErrorObj);
	  break;
	  case 'PUT':
		  if(validate($input)){
					$isSuccess = process_request($input);
					if($isSuccess){
						$response = get_response($input['player_ID']);
						if($response){
							$result['player_ID'] = $response['player_ID'];
							$result['name'] = $response['name'];
							$result['credits'] = $response['credits'];
							$result['lifetime_spins'] = $response['lifetime_spins'];
							$result['lifetime_average_return'] = $result['credits']/$result['lifetime_spins'];
							$jsonResObj = json_encode($result);
							print_r($jsonResObj);
						}
						else{
							$error_response = array('status' => 'false', 'error_code' => '1', 'error_description' => 'Player doesnt exist');            
							$jsonErrorObj = json_encode($error_response);
							print_r($jsonErrorObj);
						}
					}else{
						$error_response = array('status' => 'false', 'error_code' => '1', 'error_description' => 'Invalid Parameters');            
						$jsonErrorObj = json_encode($error_response);
						print_r($jsonErrorObj);
					}
				}
				else{
					$error_response = array('status' => 'false', 'error_code' => '1', 'error_description' => 'Missing Parameters');            
					$jsonErrorObj = json_encode($error_response);
					print_r($jsonErrorObj);
				}
	  break;
	  case 'POST':
			$error_response = array('status' => 'false', 'error_code' => '-1', 'error_description' => 'Invalid Method Type');$jsonErrorObj = json_encode($error_response);
			print_r($jsonErrorObj);
	  break;
	  case 'DELETE':
	  print_r($jsonErrorObj);
	  break;
	}
 
}
catch(Exception $e){
	$error_response = array('status' => 'false', 'error_code' => '1', 'error_description' => $e.getMessage());            
	$jsonErrorObj = json_encode($error_response);
	print_r($jsonErrorObj);
}
// close mysql connection
mysqli_close($link);