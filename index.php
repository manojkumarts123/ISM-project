<?php 
    $pdo = new PDO('mysql:host = localhost; port = 3306; dbname=ism', 'Manoj', '123');
    session_start();
?>

<html>
    <head>
        <title>Home</title>
        <link rel="stylesheet" href="cyber-css.css">
    </head>
    <body>
        <div class="head">
            <h1>Cyber Scurity Tools</h1>
        </div>
        <?php include 'menu.php'; ?>        <!--importing menu-->
        <div class="container">
            <?php
                if ( isset($_SESSION["error"]) ) {
                    echo('<h3 style="color:red">'.$_SESSION["error"]."</h3>\n");
                    unset($_SESSION["error"]);
                }
                if ( isset($_SESSION["message"]) ) {
                    echo('<h3 style="color:yellowgreen">'.$_SESSION["message"]."</h3>\n");
                    unset($_SESSION["message"]);
                }
            ?>
            <div class="home_container">
                <p>This is a simple website created to demonstrate sql injection detection and prevention. 
                This website block users who submitted suscpious input in any form in website, like in form. 
                </p>
            </div>
        </div>
    </body>
</html>