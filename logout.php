<?php
$pdo = new PDO('mysql:host = localhost; port = 3306; dbname=pms', 'Manoj', '123');
session_start();
?>

<html>
    <head>
        <title>cyber:logout</title>
        <link rel='stylesheet' href='cyber-css.css'>
    </head>
    <body class="login">
        <div class="logout_container">
            <h1 class="logout_head">Cyber Security Tools</h1>
            <h3 class="logout_text">Hey <?php echo(isset($_SESSION['name'])?$_SESSION['name']:$_SESSION['who']); ?>, Your have logged out Successfully...</h3>
            <a href='login-page.php' class="greenbutton logoutbutton">Go To Login</a>
            <p class="clearfix"></p>
        </div>
    
        <!--Logging out--->
        <?php
            session_destroy();
        ?>
    </body>
</html>