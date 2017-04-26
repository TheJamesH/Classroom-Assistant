<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Corvala</title>
  <meta name="description" content=""/>
  <link href="css/styles.css" rel="stylesheet">
  <link href="css/gradients.css" rel="stylesheet">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
</head>
<body>
  <div class="wrapper">
    <header>
      <nav class="website-nav">
        <ul>
          <li><a class="home-link" href="index.html">Covala</a></li>
          <li><a href="about.html">About</a></li>
          <li><a href="documentation.html">Documentation &amp; Code</a></li>
          <li><a href="bios.html">Developer Bios</a></li>
            <li><a href="alarm.php">Alarm</a></li>
            <li><a href="yourClass.php">Your Class</a></li> 
        </ul>
      </nav>
    </header>
    <section class="page-content">
      <article>
        
          <p id="timeRemainingHeader">Remaining Time:</p>
          <br>
          <p id="timeRemainingLabel">--:--:--</p>
          
          <script>         
              
              // Begin timer.
              window.onload = function(){ 
                  var UID = 50;
                  setUserAlarm(UID);
              };
              
              function getUserLocalTime(){
                  // Get current hour and minute.
                  var currentDate = new Date();             
                  return currentDate;
              }
              
              function setUserAlarm(UID){
                  var xmlhttp = new XMLHttpRequest();
                  xmlhttp.onreadystatechange = function(){
                      if (this.readyState == 4 && this.status == 200){
                          
                          // Get alarm timestamp from database.
                          var alarmTimestamp = this.responseText;
                          
                          if (alarmTimestamp.toString() == "--:--:--"){
                             document.getElementById("timeRemainingLabel").innerHTML = "--:--:--";
                          }
                          else{
                              // Get current date and convert alarm timestamp to date object.
                              var userCurrentDate = getUserLocalTime();
                              var alarmParts = alarmTimestamp.split(":");
                              var alarmAsDate = new Date(userCurrentDate.getFullYear(), userCurrentDate.getMonth(), userCurrentDate.getDate(), 
                                                         alarmParts[0], alarmParts[1], alarmParts[2]);

                              // Calculate time remaining.
                              var timeRemaining = alarmAsDate.getTime() - userCurrentDate.getTime();
                              if (timeRemaining <= 0){
                                  document.getElementById("timeRemainingLabel").innerHTML = "00:00:00";
                              }
                              else{
                                  // Create new timer label.
                                  var hours = Math.floor((timeRemaining % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                  var minutes = Math.floor((timeRemaining % (1000 * 60 * 60)) / (1000 * 60));
                                  var seconds = Math.floor((timeRemaining % (1000 * 60)) / 1000); 

                                  if (hours.toString().length == 1){
                                      hours = "0" + hours;                
                                  }
                                  if (minutes.toString().length == 1){
                                      minutes = "0" + minutes;                
                                  }
                                  if (seconds.toString().length == 1){
                                      seconds = "0" + seconds;                
                                  }

                                  document.getElementById("timeRemainingLabel").innerHTML = hours + ":" + minutes + ":" + seconds; 
                              }
                          }
                          
                          // Update timer label.
                          setInterval(function(){ setUserAlarm(UID); }, 1000);
                      }
                  };
                  xmlhttp.open("GET", "timerManager.php?action=" + UID, true);
                  xmlhttp.send();            
              }             
          </script>
          
      </article>
    </section>
    <div class="push"></div>

  </div>
  <footer>
  </footer>
  <script src="js/set-background.js"></script>
</body>
</html>
