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
        if( $JSON_Request->request->intent->name == "getStudentAttendance" )
		{
            $ReturnValue = getStudentAttendanceHandler($JSON_Request, $conn);
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
		//CallRoll requests
		if( $JSON_Request->request->intent->name == "here" )
		{
            $ReturnValue = hereHandler($JSON_Request, $conn);
        }	
		if( $JSON_Request->request->intent->name == "notHere" )
		{
            $ReturnValue = notHereHandler($JSON_Request, $conn);
        }	
		
		
	}
    
    // Return the JSON response
	return $ReturnValue;
}


//----HANDLERS-------------------------------------------------------------------------//

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
            "shouldEndSession": "False"
          }
        }';	
    
    return $response;
}

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
            "shouldEndSession": "False"
          }
        }';	
    
	echo $response;
    return $response;
}

function callRollHandler($JSON_Request, $conn){
    
    //To Do: actually do SQL query using date.
    $classDate =  $JSON_Request->request->intent->slots->classDate->value;    
    $response = "";
    
    if (!is_null($classDate)){
        $students = getRollListWithStudentNames($conn);
        $confirmation = "Repeating past roll for $classDate";       
        while ($row = mysqli_fetch_assoc($students)){
            $studentAttendanceString = "";
            if($row['Attendance'] == 1){
                $studentAttendanceString = $row['FName'] . " " . $row['LName'] . " was here. ";
            }
            else{
                $studentAttendanceString = $row['FName'] . " " . $row['LName'] . " was not here. ";
            }
            $confirmation = $confirmation . $studentAttendanceString;
        }
        
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
    
    }
    else{
        // If the teacher calls roll for a day again, reset Checked flag;
        resetClassAttendance($conn);

        $nextUncheckedStudent = selectNextUncheckedStudent($conn);
        $studentFName = $nextUncheckedStudent['FName'];
        $studentLName = $nextUncheckedStudent['LName'];

        $confirmation = "Calling Roll. $studentFName $studentLName ?";
        $confirmationReprompt = "$studentLName ?";

        // Generate response
        $response= '
            {
              "version": "1.0",
              "sessionAttributes": {
                "studentFName": "' . $studentFName . '",
                "studentLName": "' . $studentLName . '"
              },
              "response": {
                "outputSpeech": {
                  "type": "PlainText",
                  "text": "' . $confirmation . '"
                },
                "reprompt": {
                  "outputSpeech": {
                    "type": "PlainText",
                    "text": "' . $confirmationReprompt . '"
                  }
                },
                "shouldEndSession": "False"
              }
            }';	
    }
    return $response; 
}

function hereHandler($JSON_Request, $conn){
    $checkedStudentFName = $JSON_Request->session->attributes->studentFName;
    $checkedStudentLName = $JSON_Request->session->attributes->studentLName;
    addStudentAttendance($conn, $checkedStudentFName, $checkedStudentLName);
    addStudentChecked($conn, $checkedStudentFName, $checkedStudentLName);
    
    $nextUncheckedStudent = selectNextUncheckedStudent($conn);
    
    $response = "";
    if (is_null($nextUncheckedStudent)){
        $confirmation = "$checkedStudentFName $checkedStudentLName is here. Roll call is finished!";
        
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
                    "text": " "
                  }
                },
                "shouldEndSession": "False"
              }
            }';	
    }
    else{
        $studentFName = $nextUncheckedStudent['FName'];
        $studentLName = $nextUncheckedStudent['LName'];
    
        $confirmation = "$checkedStudentFName $checkedStudentLName is here. $studentFName $studentLName ?";
        $confirmationReprompt = "$studentLName ?";
        
        // Generate response
        $response= '
            {
              "version": "1.0",
              "sessionAttributes": {
                "studentFName": "' . $studentFName . '",
                "studentLName": "' . $studentLName . '"
              },
              "response": {
                "outputSpeech": {
                  "type": "PlainText",
                  "text": "' . $confirmation . '"
                },
                "reprompt": {
                  "outputSpeech": {
                    "type": "PlainText",
                    "text": "' . $confirmationReprompt . '"
                  }
                },
                "shouldEndSession": "False"
              }
            }';	
    }
    return $response;
    
}
function notHereHandler($JSON_Request, $conn){
    $checkedStudentFName = $JSON_Request->session->attributes->studentFName;
    $checkedStudentLName = $JSON_Request->session->attributes->studentLName;
    removeStudentAttendance($conn, $checkedStudentFName, $checkedStudentLName);
    addStudentChecked($conn, $checkedStudentFName, $checkedStudentLName);
    
    $nextUncheckedStudent = selectNextUncheckedStudent($conn);
    
    $response = "";
    if (is_null($nextUncheckedStudent)){
        $confirmation = "$checkedStudentFName $checkedStudentLName is not here. Roll call is finished!";
        $confirmationReprompt = "$studentLName ?";
        
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
                    "text": " "
                  }
                },
                "shouldEndSession": "False"
              }
            }';	
    }
    else{
        $studentFName = $nextUncheckedStudent['FName'];
        $studentLName = $nextUncheckedStudent['LName'];
    
        $confirmation = "$checkedStudentFName $checkedStudentLName is not here. $studentFName $studentLName ?";
        $confirmationReprompt = "$studentLName ?";
        
        // Generate response
        $response= '
            {
              "version": "1.0",
              "sessionAttributes": {
                "studentFName": "' . $studentFName . '",
                "studentLName": "' . $studentLName . '"
              },
              "response": {
                "outputSpeech": {
                  "type": "PlainText",
                  "text": "' . $confirmation . '"
                },
                "reprompt": {
                  "outputSpeech": {
                    "type": "PlainText",
                    "text": "' . $confirmationReprompt . '"
                  }
                },
                "shouldEndSession": "False"
              }
            }';	
    }
    return $response;
}

function getStudentAttendanceHandler($JSON_Request, $conn){
    
    // Get variables.
    $firstName = $JSON_Request->request->intent->slots->studentFirstName->value;
    $lastName = $JSON_Request->request->intent->slots->studentLastName->value;
    $date = $JSON_Request->request->intent->slots->classDate->value;

    // Create confirmation
    $row = getStudentAttendance($conn,$firstName,$lastName);
    $result = "Student " . $firstName. " ". $lastName. " is here ";
	if ($row['Attendance'] == 0){
		$result = "Student " . $firstName. " ". $lastName. " is not here ";
	}
	
    // Generate response
    $response= '
        {
          "version": "1.0",
          "response": {
            "outputSpeech": {
              "type": "PlainText",
              "text": "' . $result . '"
            },
            "shouldEndSession": "False"
          }
        }';	
    
    return $response;
}

function addStudentAttendanceHandler($JSON_Request, $conn){
    
    // Get variables.
    $firstName = $JSON_Request->request->intent->slots->studentFirstName->value;
    $lastName = $JSON_Request->request->intent->slots->studentLastName->value;
    $date = $JSON_Request->request->intent->slots->classDate->value;
	
	// Create confirmation
	$row = addStudentAttendance($conn,$firstName,$lastName);
	$result = "Student " . $firstName. " ". $lastName. " set to here ";
    
    // Generate response
    $response= '
        {
          "version": "1.0",
          "response": {
            "outputSpeech": {
              "type": "PlainText",
              "text": "' . $result . '"
            },
            "shouldEndSession": "False"
          }
        }';	
    
    return $response;
}

function removeStudentAttendanceHandler($JSON_Request, $conn){
    
    // Get variables.
    $firstName = $JSON_Request->request->intent->slots->studentFirstName->value;
    $lastName = $JSON_Request->request->intent->slots->studentLastName->value;
    $date = $JSON_Request->request->intent->slots->classDate->value;
	
	// Create confirmation
	$row = removeStudentAttendance($conn,$firstName,$lastName);
	$result = "Student " . $firstName. " ". $lastName. " set to not here ";
    
    // Generate response
    $response= '
        {
          "version": "1.0",
          "response": {
            "outputSpeech": {
              "type": "PlainText",
              "text": "' . $result . '"
            },
            "shouldEndSession": "False"
          }
        }';	
    
    return $response;
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

    // Create confirmation
  	$row = addBonusPoints($firstName, $lastName, $points, $conn);
    $newPoints = $row['BonusPoints'];
  	$result = "Student " . $firstName. " ". $lastName. " now has " . $newPoints. " bonus points";

      // Generate response
      $response= '
          {
            "version": "1.0",
            "response": {
              "outputSpeech": {
                "type": "PlainText",
                "text": "' . $result . '"
              },
              "shouldEndSession": "False"
            }
          }';

      return $response;
}
//TO DO: Add functionality. Will cause endpoint error.
function removeStudentBonusPointsHandler($JSON_Request, $conn){

    // Get variables.
    $firstName = $JSON_Request->request->intent->slots->studentFirstName->value;
    $lastName = $JSON_Request->request->intent->slots->studentLastName->value;
    $points = $JSON_Request->request->intent->slots->bonusPoints->value;

    // Create confirmation
  	$row = removeBonusPoints($firstName, $lastName, $points, $conn);
    $newPoints = $row['BonusPoints'];
  	$result = "Student " . $firstName. " ". $lastName. " now has " . $newPoints. " bonus points";

      // Generate response
      $response= '
          {
            "version": "1.0",
            "response": {
              "outputSpeech": {
                "type": "PlainText",
                "text": "' . $result . '"
              },
              "shouldEndSession": "False"
            }
          }';

      return $response;
}
//TO DO: Add functionality. Will cause endpoint error.
function setTimerHandler($JSON_Request, $conn){
    
    // Get variables.
    $time = $JSON_Request->request->intent->slots->alarmTime->value;
    $twelveHrTime = new DateTime($time);
    $twelveHrTimeString = $twelveHrTime->format('h:i:s a');
    
    // Update timer in database.
    updateRowInAlarms(50, $time, $conn);
    
    // Create confirmation
    $confirmation = "Time set for: $twelveHrTimeString.";
    
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
            "text": "Please specify an alarm time."
          }
        },
        "shouldEndSession": "False"
      }
    }';	
    
    return $response;
}
//TO DO: Add functionality. Will cause endpoint error.
function stopTimerHandler($JSON_Request, $conn){
    
    $time = "--:--:--";
    // Update timer in database.
    updateRowInAlarms(50, $time, $conn);
    
    $confirmation = "Timer stopped";
    
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
            "text": "Please specify an alarm time."
          }
        },
        "shouldEndSession": "False"
      }
    }';	
    
    return $response;
}
//TO DO: Add functionality. Will cause endpoint error.
function pauseTimerHandler($JSON_Request, $conn){
    
}
//TO DO: Add functionality. Will cause endpoint error.
function resumeTimerHandler($JSON_Request, $conn){
    
}

?>