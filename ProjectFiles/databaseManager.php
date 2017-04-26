<?php

//----CONNECTION---------------------------------------------------------------------------//
function connectToCovalaDb(){
    // Setup database parameters.
    $servername = "covalainstance.cxdx76laa8aw.us-east-1.rds.amazonaws.com";
    $username = "covalaDb";
    $password = "covalaPw";
    $dbname = "covalaDatabase";

    // Connect to database using mysqli.
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    
    return $conn;
}

//----ACCESS---------------------------------------------------------------------------------//

// Below is an example of an SQL ADD query.
// TO DO: This is a example function. We should eventually remove this.
function addRowToLoginInfo($UserName, $UPassword, $conn){
    
    // TO DO: Calculate UID
    $UID = 53;
    
    // Create SQL query string. String values must be escaped with dots as shown.
    $sql = "INSERT INTO LoginInfo (UID, UserName, UPassword) VALUES ($UID, '".$UserName."', '".$UPassword."')";

    // Execute SQL query.
    mysqli_query($conn, $sql);
    mysqli_commit($conn);
}

function getRollListWithStudentNames($conn){
    $sql = "SELECT ClassDay.SID, StudentInfo.FName, StudentInfo.LName, ClassDay.Attendance
            FROM ClassDay
            INNER JOIN StudentInfo ON ClassDay.SID=StudentInfo.SID
            ORDER BY LName ASC";
    $result = mysqli_query($conn, $sql);
    
    return $result;
}

function updateRowInAlarms($UID, $AlarmTime, $conn){
    $sql = "UPDATE Alarms SET AlarmTime = '".$AlarmTime."' WHERE UID = $UID";
    
    // Execute SQL query.
    mysqli_query($conn, $sql);
    mysqli_commit($conn);
}

//CallRoll functions
function getRollList($conn){
     
    $sql = "SELECT * FROM ClassDay";
    $result = mysqli_query($conn, $sql);
    return $result;
}

//Test get function
function getStudentsInfo($conn){
     
    // Create SQL query string. String values must be escaped with dots as shown.
    $sql = "SELECT * FROM StudentInfo";
    $result = mysqli_query($conn, $sql);
	$row = mysqli_fetch_assoc($result);

    return $row;
}

function getStudentAttendance($conn,$firstName,$lastName){
     
    // Create SQL query string. String values must be escaped with dots as shown.
    $sql = "SELECT * FROM StudentInfo WHERE FName = '$firstName' AND LName = '$lastName'";
    $result1 = mysqli_query($conn, $sql);
    $row1 = mysqli_fetch_assoc($result1);
	$row1SID = $row1['SID'];
    $sql2 = "SELECT * FROM ClassDay WHERE SID = '$row1SID'";
    $result2 = mysqli_query($conn, $sql2);
    $row2 = mysqli_fetch_assoc($result2);

    return $row2;
}

function addStudentAttendance($conn,$firstName,$lastName){
     
    // Create SQL query string. String values must be escaped with dots as shown.
    $sql = "SELECT * FROM StudentInfo WHERE FName = '$firstName' AND LName = '$lastName'";
    $result1 = mysqli_query($conn, $sql);
    $row1 = mysqli_fetch_assoc($result1);
	$row1SID = $row1['SID'];
    $sql2 = "UPDATE ClassDay SET Attendance = 1 WHERE SID = '$row1SID'";
    $result2 = mysqli_query($conn, $sql2);
    $row2 = mysqli_fetch_assoc($result2);

    return $row2;
}
function addStudentChecked($conn,$firstName,$lastName){
    // Create SQL query string. String values must be escaped with dots as shown.
    $sql = "SELECT * FROM StudentInfo WHERE FName = '$firstName' AND LName = '$lastName'";
    $result1 = mysqli_query($conn, $sql);
    $row1 = mysqli_fetch_assoc($result1);
	$row1SID = $row1['SID'];
    $sql2 = "UPDATE ClassDay SET Checked = 1 WHERE SID = '$row1SID'";
    $result2 = mysqli_query($conn, $sql2);
    $row2 = mysqli_fetch_assoc($result2);

    return $row2;
}
function resetClassAttendance($conn){
    // TO DO: Make sure reset for a particular class day. This resets checked for all CDIDs.
    $sql = "UPDATE ClassDay SET Checked = 0";
    mysqli_query($conn, $sql);
}

function removeStudentAttendance($conn,$firstName,$lastName){
     
    // Create SQL query string. String values must be escaped with dots as shown.
    $sql = "SELECT * FROM StudentInfo WHERE FName = '$firstName' AND LName = '$lastName'";
    $result1 = mysqli_query($conn, $sql);
    $row1 = mysqli_fetch_assoc($result1);
    $row1SID = $row1['SID'];
    $sql2 = "UPDATE ClassDay SET Attendance = 0 WHERE SID = '$row1SID'";
    $result2 = mysqli_query($conn, $sql2);
    $row2 = mysqli_fetch_assoc($result2);

    return $row2;
}


function displayStudentInfoTable($conn){
    
    // Create SQL query string.
    $sql = "SELECT StudentInfo.SID, StudentInfo.FName, StudentInfo.LName, ClassDay.Attendance, StudentInfo.BonusPoints
            FROM StudentInfo
            INNER JOIN ClassDay ON StudentInfo.SID=ClassDay.SID;";
    // Run query.
    $result = mysqli_query($conn, $sql);

    // Echo table header row.
    echo "<table border='1'>
                <tr>
                <th>SID</th>
                <th>First</th>
                <th>Last</th>
                <th>Attendance</th>
                <th>BonusPoints</th>
                </tr>";
    
    while ($row = mysqli_fetch_assoc($result)){
        echo "<tr class='selectableRow'>";
        echo "<td>" . $row['SID'] . "</td>";
        echo "<td>" . $row['FName'] . "</td>";
        echo "<td>" . $row['LName'] . "</td>";
        echo "<td>" . $row['Attendance'] . "</td>";
        echo "<td>" . $row['BonusPoints'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

function selectNextUncheckedStudent($conn){
    $sql = "SELECT ClassDay.SID, StudentInfo.FName, StudentInfo.LName, ClassDay.Attendance
            FROM ClassDay
            INNER JOIN StudentInfo ON ClassDay.SID=StudentInfo.SID
            WHERE ClassDay.Checked=false
            ORDER BY LName ASC";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    
    return $row;
}

function addBonusPoints($studentFirstName, $studentLastName, $points, $conn){
    $sql = "SELECT * FROM StudentInfo WHERE FName = '$studentFirstName' AND LName = '$studentLastName'";
    $result1 = mysqli_query($conn, $sql);
    $row1 = mysqli_fetch_assoc($result1);
    $SID = $row1['SID'];

    $currBonusPoints = $row1['BonusPoints'];
    $newBonusPoints = $currBonusPoints + $points;
    
    $sql2 = "UPDATE StudentInfo SET BonusPoints = $newBonusPoints WHERE SID = '$SID'";
    $result2 = mysqli_query($conn, $sql2);
    
    // Select updated row.
    $result3 = mysqli_query($conn, $sql); 
    $row2 = mysqli_fetch_assoc($result3);

    return $row2;
}

function removeBonusPoints($studentFirstName, $studentLastName, $points, $conn){
    $sql = "SELECT * FROM StudentInfo WHERE FName = '$studentFirstName' AND LName = '$studentLastName'";
    $result1 = mysqli_query($conn, $sql);
    $row1 = mysqli_fetch_assoc($result1);
	  $SID = $row1['SID'];
	  $currBonusPoints = $row1['BonusPoints'];

    $newBonusPoints = $currBonusPoints - $points;

    $sql2 = "UPDATE StudentInfo SET BonusPoints = $newBonusPoints WHERE SID = '$SID'";
    $result2 = mysqli_query($conn, $sql2);
    
    // Select updated row.
    $result3 = mysqli_query($conn, $sql); 
    $row2 = mysqli_fetch_assoc($result3);

    return $row2;
}

?>