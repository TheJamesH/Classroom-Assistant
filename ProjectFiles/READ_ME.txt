Hey guys! So I've set up the basic structure of our driver.php and our databaseManager.php.
The driver is what handles Alexa requests, and it uses the databaseManager to connect to the database.
Please review both php files and ask me any questions.

FAQ:

- *** HOW DO I TEST MY CODE CHANGES?: Okay, so here's my idea. For right now, let's use the Elastic Beanstalk deployment page for testing since it's the easiest way at the moment. Let's use the GitHub ONLY for commits, so ONLY for code that you've proven doesn't break. So, here's the test process: 
1.Download the ENTIRE latest Covala project comitted to GitHub. 2.Make your changes. 3.Zip your Covala working folder and deploy it to the Covala EB. 4.Navigate to the Covala Alexa Skill test page. At the bottom is a box where you can input Alexa voice commands. Use that to verify that Covala is returning the right response. 5.If things aren't working right, keep changing your code and deploying. 6.Once you're sure your changes are good, commit the project with your changes to GitHub.

- *** HOW DO I TEST CHANGES (such as add, delete) TO THE DATABASE?: How I've been doing it is by running the appropriate code and then using the MySQLWorkbench program to verify.

    Database name: covalaDatabase
    Database username: covalaDb
    Database password: covalaPw
    Database server/host name: "covalainstance.cxdx76laa8aw.us-east-1.rds.amazonaws.com"
    Port: 3306

- WHERE IS THE COVALA ELASTIC BEANSTALK?: Login to Amazon Web Services. Navigate to Services > Elastic Beanstalk > Covala.

- HOW DO I DEPLOY CODE TO THE COVALA EB?: Navigate INTO your local Covala working folder. Highlight all files and compress them into a zip. (NOTE: You CANNOT just right click the Covala working folder and zip the files. It will pull on error on Elastic Beanstalk.) Click the upload and deploy button on the Covala EB page to upload your zip.

- WHERE IS THE COVALA ALEXA SKILL?: Login to Amazon Developer Console. Navigate to Alexa > Alexa Skills Kit > Covala.

- WHERE IS THE INTERACTION MODEL?: Go to the Covala Alexa Skill. It's under Interaction Model. You can edit the Intent Schema and the Sample Utterances here.

- DO YOU HAVE A MYSQLI REFERENCE?: See W3Schools. In their examples, we are using Procedural Mysqli.

- HOW TO HANDLE SESSIONS?: Not sure yet. Look into "Sessions", "Session Attributes".