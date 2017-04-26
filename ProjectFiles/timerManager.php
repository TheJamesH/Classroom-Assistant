<?php              

    switch ($_REQUEST["action"]){
        case "pause":
            break;
        case "stop":
            break;
        default:
            getUserAlarm($_REQUEST["action"]);
            break;
    }

    function getUserAlarm($UID){
        // Connect to database.
        include 'databaseManager.php';
        $dbConnection = connectToCovalaDb();  
        
        // Create SQL query string.
        $sql = "SELECT * FROM Alarms WHERE UID=" . $UID;
        
        // Run query and get alarm time.
        $result = mysqli_query($dbConnection, $sql);
        $row = mysqli_fetch_assoc($result);
        
        $alarm = $row['AlarmTime'];
        if ( $alarm === "--:--:--" ){
            echo $alarm;
        }
        else{
            echo $alarm . ":00";
        }
    }

?>