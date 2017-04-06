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
        </ul>
      </nav>
    </header>
    <section class="page-content">
      <article>
          
        <!--Add table-->
        <?php         
          include 'databaseManager.php';
          $dbConnection = connectToCovalaDb();
          displayStudentInfoTable($dbConnection);         
        ?>
          
        <!--Add row selectability-->  
        <script>          
            $("tr.selectableRow").hover(
                function() {
                    $(this).css("background", "blue");
                },
                function() {
                    $(this).css("background", "");
                }      
            );
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
