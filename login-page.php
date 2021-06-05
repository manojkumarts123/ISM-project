<?php 
    $pdo = new PDO('mysql:host = localhost; port = 3306; dbname=ism', 'Manoj', '123');
    //include 'sql-logic.php';
    session_start(); //retrieving old session
    session_destroy();  // closing old session
    session_start();   //creating new session

    if(isset($_GET['success']) && $_GET['success'] == 1){         // if user is blocked and msg is modified...
        echo("<script>alert('Message Updated Successfully... ');</script>");
    }
    if(isset($_POST["submit"])){
        if($_POST["submit"] == "Login as Admin"){
            $_SESSION['who'] = "Admin"; // who is a session variable which decalred to tell type of user during whole session
            header( 'Location: login.php' ) ;
            return;
        }
        else{
            $_SESSION['who'] = "User"; // who is a session variable which decalred to tell type of user during whole session
            header( 'Location: login.php' ) ;
            return;
        }
    }
?>

<html>
    <head>
        <title>Login</title>
        <link rel="stylesheet" href="cyber-css.css">
    </head>
    <body class="login">
        <h1 class="heading">Cyber Security tools</h1>
        <div class="login_container">
            <h2 style="text-align: center;">Login</h2>
            <form method="POST">
                <input type="submit" class="button login_button" name="submit" value="Login as Admin"></input><br>
                <input type="submit" class="button login_button" name="submit" value="Login as User"></input>
            </form>
        </div>
    </body>

</html>