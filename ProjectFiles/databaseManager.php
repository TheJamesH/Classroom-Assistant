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

//Test get function
function getStudentsInfo($conn){
     
    // Create SQL query string. String values must be escaped with dots as shown.
    $sql = "SELECT * FROM StudentInfo";
    $result = mysqli_query($conn, $sql);
	$row = mysqli_fetch_assoc($result);

    return $row;
}

function displayStudentInfoTable($conn){
    
    // Create SQL query string.
    $sql = "SELECT * FROM StudentInfo";
    // Run query.
    $result = mysqli_query($conn, $sql);

    // Echo table header row.
    echo "<table border='1'>
                <tr>
                <th>SID</th>
                <th>First</th>
                <th>Middle</th>
                <th>Last</th>
                <th>Nickname</th>
                </tr>";
    
    while ($row = mysqli_fetch_assoc($result)){
        echo "<tr class='selectableRow'>";
        echo "<td>" . $row['SID'] . "</td>";
        echo "<td>" . $row['FName'] . "</td>";
        echo "<td>" . $row['MName'] . "</td>";
        echo "<td>" . $row['LName'] . "</td>";
        echo "<td>" . $row['NName'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

?>