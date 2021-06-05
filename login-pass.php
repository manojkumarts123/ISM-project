<?php
    $pdo = new PDO('mysql:host = localhost; port = 3306; dbname=ism', 'Manoj', '123');
    session_start();
    if ( isset($_POST["email"]) && isset($_POST["pw"]) ) {
        unset($_SESSION["user"]);  // Logout current user
        if($_SESSION['who'] == 'Admin'){
            $sql = "SELECT * FROM admin where email = :em";
        }
        else{
            $sql = "SELECT * FROM iuser where email = :em";
        }
        $st = $pdo->prepare($sql);
        $st->execute(array(
            ':em' => $_POST['email'])
        );
        
        $row = $st->fetch(PDO::FETCH_ASSOC);
        //$name = $row['name'];
        $email = $row['email'];
        //$pw = $row['password'];

        if ( $_POST['pw'] == $pw && $_POST['email'] = $email) {
            $_SESSION["user"] = $row['id'];
            $_SESSION["name"] = $name;
            $_SESSION["message"] = "Hi ".$name."!!Logged in Successfully";
            header( 'Location: index.php' );
            return;
        } else {
            $_SESSION["error"] = "Incorrect email or password.";
            echo("hi");
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
            <h2 style="text-align: center;">Login as <?php echo($_SESSION["who"]); ?></h2>
            <?php
                if ( isset($_SESSION["error"]) ) {
                    echo('<p style="color:red">'.$_SESSION["error"]."</p>\n");
                    unset($_SESSION["error"]);
                }
                if ( isset($_SESSION["message"]) ) {
                    echo('<p style="color:green">'.$_SESSION["message"]."</p>\n");
                    unset($_SESSION["message"]);
                }
            ?>
            <form method="POST">
                <input class="login_input" type="text" name="email" id="email" placeholder="Email" required><br/><br/>
                <!--<input class="login_input" type="password" name="pw" id="password" placeholder="Password" required><br/><br/>-->
                <input type="submit" class="greenbutton loginbutton">
            </form>
        </div>
    </body>

</html>