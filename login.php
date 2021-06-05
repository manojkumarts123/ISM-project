<?php
    $pdo = new PDO('mysql:host = localhost; port = 3306; dbname=ism', 'Manoj', '123');
    session_start();
    include "sql-logic.php";
    
    if(isset($_POST['goback']) && $_POST['goback'] == 1){
        goto b;
    }
    
    elseif ( isset($_POST["email"]) && $_POST["email"] != "") {
        $_SESSION['unknown'] = 1;

        // finding ip address
        $ip = getenv('HTTP_CLIENT_IP')?:
        getenv('HTTP_X_FORWARDED_FOR')?:
        getenv('HTTP_X_FORWARDED')?:
        getenv('HTTP_FORWARDED_FOR')?:
        getenv('HTTP_FORWARDED')?:
        getenv('REMOTE_ADDR');

        $_SESSION['ip'] = $ip;        
        $valid1 = chk($pdo, $ip, 0);   // validating user - check whether ip is blocked or not
        //echo("<script>alert('inside valid: ".$valid1."');</script>");
        if($valid1 == 1){
            header( 'Location: dialog.php');
            return;
        }
        
        $valid2 = check($pdo, $_POST["email"]);    // validating user - check whether input entered cause sql injection or not
        if($valid2 == 1){
            header( 'Location: dialog.php');
            return;
        }


        unset($_SESSION["user"]);               // Logout current user
        unset($_SESSION["email"]); 
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
        $email = $row['email'];

        if ($_POST['email'] == $email) {
            $_SESSION["user"] = $row['id'];
            $_SESSION['unknown'] = 0;
            $valid1 = chk($pdo, $_SESSION['user'], $_SESSION['who']);   // validating user - check whether user is blocked or not
            if($valid1 == 1){
                header( 'Location: dialog.php');
                return;
            }
            $_SESSION['email'] = $email;
            header( 'Location: login.php');
            return;
        } else {
            $_SESSION["error"] = "Incorrect email.";
            header( 'Location: login.php' ) ;
            return;
        }
    }

    elseif(isset($_POST["pw"]) && $_POST["pw"] != "") {

        $valid1 = chk($pdo, $_SESSION['user'], $_SESSION['who']);   // validating user - check whether user is blocked or not
         if($valid1 == 1){
            header( 'Location: dialog.php');
            return;
        }

        $valid2 = check($pdo, $_POST["pw"]);    // validating user - check whether input entered cause sql injection or not
        if($valid2 == 1){
            header( 'Location: dialog.php');
            return;
        }
        
        if($_SESSION['who'] == 'Admin'){
            $sql = "SELECT * FROM admin where id = :id";
        }
        else{
            $sql = "SELECT * FROM iuser where id = :id";
        }
        $st = $pdo->prepare($sql);
        $st->execute(array(
            ':id' => $_SESSION['user'])
        );
        $row = $st->fetch(PDO::FETCH_ASSOC);
        //echo($row['password']);
        if ( $_POST['pw'] == $row['password']){
            $_SESSION["name"] = $row['name'];
            $_SESSION["message"] = "Hi ".$_SESSION['name']."!!Logged in Successfully";
            header( 'Location: index.php' );
            return;
        } else {
            $_SESSION["error"] = "Incorrect password.";
            header( 'Location: login.php' ) ;
            return;
        }
    }
    b:
?>

<html>
    <head>
        <title>Login</title>
        <link rel="stylesheet" href="cyber-css.css">
        <script>
                function unset_user(){      // get go back value to 1 if pressed
                    document.getElementById('goback').value = 1;
                }
        </script>
    </head>
    <body class="login">
        <h1 class="heading">Cyber Security tools</h1>
        <div class="login_container">
            <h2 style="text-align: center;">Login as <?php echo($_SESSION["who"]); ?></h2>  
            
            <?php
                //checking for errors
                if ( isset($_SESSION["error"]) ) {
                    echo('<p style="color:red">'.$_SESSION["error"]."</p>\n");
                    unset($_SESSION["error"]);
                }
                if ( isset($_SESSION["message"]) ) {
                    echo('<p style="color:green">'.$_SESSION["message"]."</p>\n");
                    unset($_SESSION["message"]);
                }
                //print_r($_SESSION);
            ?>
            <!-- 
                Displaying input box according to the user's activity
                 First it displays email text box. If user's ip is not blocked/ deleted then check for sql injection
                 if sql injection is not possible, then asks for password.
                 If user press Go Back buttton, if moves to the previous page displaying email box with email value entered by user.
            -->
            <form method="POST">
                <?php 
                    if(isset($_POST['goback']) && isset($_SESSION['email'])){ // check if go back is pressed
                ?>
                        <input class="login_input" type="text" name="email" id="email" value="<?php echo($_SESSION['email'])?>" required><br/><br/>
                        <a href="login-page.php" class="greenbutton loginbtn" style="float:left; text-align:center">Go Back</a>
                        <input type="submit" class="greenbutton loginbtn" style="float:right">
                        <p class="clearfix"></p>
                <?php
                    }
                    elseif(isset($_SESSION['user']) && isset($_SESSION['email'])){ // check if email is entered ($_SESSION['user] is set while validating email)
                ?>
                    <input class="login_input" type="text" name="email" id="email" value="<?php echo($_SESSION['email'])?>" disabled><br/><br/>
                    <input class="login_input" type="password" name="pw" id="password" placeholder="Password"><br/><br/>
                    <input type="hidden" name="goback" id="goback" value=0>
                    <a href="login.php" onclick="unset_user()"><input type="submit" class="greenbutton loginbtn" style="float:left" value="Go Back"></a>
                    <input type="submit" class="greenbutton loginbtn" style="float:right">
                    <p class="clearfix"></p>
                <?php } else{ ?>    <!--else displays email input box--> 
                    <input class="login_input" type="text" name="email" id="email" placeholder="Email" required><br/><br/>
                    <a href="login-page.php" class="greenbutton loginbtn" style="float:left; text-align:center">Go Back</a>
                    <input type="submit" class="greenbutton loginbtn" style="float:right">
                    <p class="clearfix"></p>
                <?php } ?>
            </form>
        </div>
    </body>

</html>