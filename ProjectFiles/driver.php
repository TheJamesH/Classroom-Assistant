<?php

//----MAIN---------------------------------------------------------------------------------//

// Include the databaseManager. You can call all functions in databaseManager.
include 'databaseManager.php';
// Connect to Covala database;
$dbConnection = connectToCovalaDb();

// Retrieve JSON request from Alexa
$JSON_Request = json_decode(file_get_contents('php://input'));

// Handle RequestType and Generate JSON response, pass dbConnection for functions that need it
$JSON_Response = GetJsonResponse($JSON_Request, $dbConnection);

// Return JSON response to Alexa
echo $JSON_Response;


//----GET JSON RESPONSE--------------------------------------------------------------------//
// This function handles the requestType and the intent and generates a JSON response

function GetJsonResponse($JSON_Request, $conn){

    // Get request type (LaunchRequest, SessionEndedRequest, or IntentRequest)
    $RequestMessageType = $JSON_Request->request->type;
    
    // Get request ID
	$RequestId = $JSON_Request->request->requestId;
    
    // Initialize the return response
	$ReturnValue = "";
	
    // Handle the requestType
    // LaunchRequest - When the user starts the skill
    // SessionEndedRequest - When the user ends the session
    // IntentRequest - When the user tells the skill a command
	if( $RequestMessageType == "LaunchRequest" ){
		$ReturnValue= '
		{
		  "version": "1.0",
		  "response": {
			"outputSpeech": {
			  "type": "PlainText",
			  "text": "Welcome to Covala Classroom Assistant!"
			},
			"reprompt": {
			  "outputSpeech": {
				"type": "PlainText",
				"text": "Please say a command."
			  }
			},
			"shouldEndSession": false
		  }
		}';
	}
	
	if( $RequestMessageType == "SessionEndedRequest" )
	{
		$ReturnValue = '{
		  "type": "SessionEndedRequest",
		  "requestId": "$RequestId",
		  "timestamp": "' . date("c") . '",
		  "reason": "USER_INITIATED "
		}
		';
	}
	
	if( $RequestMessageType == "IntentRequest" ){	
        if( $JSON_Request->request->intent->name == "callRoll" )
		{
            $ReturnValue = callRollHandler($JSON_Request, $conn);
        }
        if( $JSON_Request->request->intent->name == "callRollForStudent" )
		{
            $ReturnValue = callRollForStudentHandler($JSON_Request, $conn);
        }
        if( $JSON_Request->request->intent->name == "addStudentAttendance" )
		{
            $ReturnValue = addStudentAttendanceHandler($JSON_Request, $conn);
        }
        if( $JSON_Request->request->intent->name == "removeStudentAttendance" )
		{
            $ReturnValue = removeStudentAttendanceHandler($JSON_Request, $conn);
        }
        if( $JSON_Request->request->intent->name == "addStudentToDatabase" )
		{
            $ReturnValue = addStudentToDatabaseHandler($JSON_Request, $conn);
        }
        if( $JSON_Request->request->intent->name == "removeStudentFromDatabase" )
		{
            $ReturnValue = removeStudentFromDatabaseHandler($JSON_Request, $conn);
        }
        if( $JSON_Request->request->intent->name == "giveStudentBonusPoints" )
		{
            $ReturnValue = giveStudentBonusPointsHandler($JSON_Request, $conn);
        }
        if( $JSON_Request->request->intent->name == "removeStudentBonusPoints" )
		{
            $ReturnValue = removeStudentBonusPointsHandler($JSON_Request, $conn);
        }
        if( $JSON_Request->request->intent->name == "setTimer" )
		{
            $ReturnValue = setTimerHandler($JSON_Request, $conn);
        }
        if( $JSON_Request->request->intent->name == "stopTimer" )
		{
            $ReturnValue = stopTimerHandler($JSON_Request, $conn);
        }
        if( $JSON_Request->request->intent->name == "pauseTimer" )
		{
            $ReturnValue = pauseTimerHandler($JSON_Request, $conn);
        }
        if( $JSON_Request->request->intent->name == "resumeTimer" )
		{
            $ReturnValue = resumeTimerHandler($JSON_Request, $conn);
        }		
		//TEST FUNCTION
		if( $JSON_Request->request->intent->name == "getStudentsInfo" )
		{
            $ReturnValue = getStudentsInfoHandler($JSON_Request, $conn);
        }	
	}
    
    // Return the JSON response
	return $ReturnValue;
}


//----HANDLERS-------------------------------------------------------------------------//

//TEST FUNCTION to Get student information
function getStudentsInfoHandler($JSON_Request,$conn){
 
	// Create confirmation
	$row = getStudentsInfo($conn);
	$result = "Student ID " . $row['SID']. " is ". $row['FName']. " " . $row['LName'] . ".";
    
    // Generate response
    $response= '
        {
          "version": "1.0",
          "response": {
            "outputSpeech": {
              "type": "PlainText",
              "text": "' . $result . '"
            },
            "shouldEndSession": "True"
          }
        }';	
    
    return $response;
}

// TO DO: Add database connectivity.
function addStudentToDatabaseHandler($JSON_Request, $conn){
    
    // Retrieve student name
    $firstName = $JSON_Request->request->intent->slots->studentFirstName->value;
    $lastName = $JSON_Request->request->intent->slots->studentLastName->value;
    
    // Create confirmation
    $confirmation = "Student $firstName $lastName has been added to the database.";
    
    // Generate response
    $response= '
        {
          "version": "1.0",
          "response": {
            "outputSpeech": {
              "type": "PlainText",
              "text": "' . $confirmation . '"
            },
            "reprompt": {
              "outputSpeech": {
                "type": "PlainText",
                "text": "Please say the name of the student to add to the database."
              }
            },
            "shouldEndSession": "True"
          }
        }';	
    
    return $response;
}

//TO DO: Add functionality. Will cause endpoint error.
function callRollHandler($JSON_Request, $conn){
    
    // Get variables.
    $date = $JSON_Request->request->intent->slots->classDate->value;
    
}
//TO DO: Add functionality. Will cause endpoint error.
function callRollForStudentHandler($JSON_Request, $conn){
    
    // Get variables.
    $firstName = $JSON_Request->request->intent->slots->studentFirstName->value;
    $lastName = $JSON_Request->request->intent->slots->studentLastName->value;
    $date = $JSON_Request->request->intent->slots->classDate->value;
}
//TO DO: Add functionality. Will cause endpoint error.
function addStudentAttendanceHandler($JSON_Request, $conn){
    
    // Get variables.
    $firstName = $JSON_Request->request->intent->slots->studentFirstName->value;
    $lastName = $JSON_Request->request->intent->slots->studentLastName->value;
    $date = $JSON_Request->request->intent->slots->classDate->value;
}
//TO DO: Add functionality. Will cause endpoint error.
function removeStudentAttendanceHandler($JSON_Request, $conn){
    
    // Get variables.
    $firstName = $JSON_Request->request->intent->slots->studentFirstName->value;
    $lastName = $JSON_Request->request->intent->slots->studentLastName->value;
    $date = $JSON_Request->request->intent->slots->classDate->value;
}
//TO DO: Add functionality. Will cause endpoint error.
function removeStudentFromDatabaseHandler($JSON_Request, $conn){
    
    // Get variables.
    $firstName = $JSON_Request->request->intent->slots->studentFirstName->value;
    $lastName = $JSON_Request->request->intent->slots->studentLastName->value;
}
//TO DO: Add functionality. Will cause endpoint error.
function giveStudentBonusPointsHandler($JSON_Request, $conn){
    
    // Get variables.
    $firstName = $JSON_Request->request->intent->slots->studentFirstName->value;
    $lastName = $JSON_Request->request->intent->slots->studentLastName->value;
    $points = $JSON_Request->request->intent->slots->bonusPoints->value;
}
//TO DO: Add functionality. Will cause endpoint error.
function removeStudentBonusPointsHandler($JSON_Request, $conn){
    
    // Get variables.
    $firstName = $JSON_Request->request->intent->slots->studentFirstName->value;
    $lastName = $JSON_Request->request->intent->slots->studentLastName->value;
    $points = $JSON_Request->request->intent->slots->bonusPoints->value;
}
//TO DO: Add functionality. Will cause endpoint error.
function setTimerHandler($JSON_Request, $conn){
    
    // Get variables.
    $time = $JSON_Request->request->intent->slots->alarmTime->value;
}
//TO DO: Add functionality. Will cause endpoint error.
function stopTimerHandler($JSON_Request, $conn){
    
}
//TO DO: Add functionality. Will cause endpoint error.
function pauseTimerHandler($JSON_Request, $conn){
    
}
//TO DO: Add functionality. Will cause endpoint error.
function resumeTimerHandler($JSON_Request, $conn){
    
}

?>