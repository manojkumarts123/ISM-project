<?php 
    $pdo = new PDO('mysql:host = localhost; port = 3306; dbname=ism', 'Manoj', '123');
    session_start();
    require "sql-logic.php";
    if(isset($_POST['msg'])){
        if( isset($_SESSION['msg']) && $_POST['msg'] == $_SESSION['msg']){  // msg not changed
            echo("<script>alert('Message is not modified...');</script>");
        }elseif($_POST['msg'] != ""){  // if msg is not empty
            
            update_msg($pdo, $_SESSION['blocked_id'], $_POST['msg']);
            header( 'Location: login-page.php?success=1');
        }else{ // if message is empty
            echo("<script>alert('Please Enter message...');</script>");
        }
    }
    
?>

<html>
    <head>
        <title>Cyber: Dialog</title>
        <link rel='stylesheet' href="cyber-css.css">
    </head>
    <body class="login">
        <div class="dialog_container">
            <h1 class="dialog_head">PROMPT</h1>
            <form method="post">
            <?php 
                //print_r($_SESSION);
                if($_SESSION['unknown'] == 1){
                    $sql = "SELECT * FROM unknown where id = :id";
                }
                else{
                    $sql = "SELECT * FROM user_status where id = :id";
                }
                $st = $pdo->prepare($sql);
                $st->execute(array(
                    ':id' => $_SESSION['blocked_id'])
                );
                $row = $st->fetch(PDO::FETCH_ASSOC);

                // for displaying details
                if($_SESSION['unknown'] == 1){
                    $ip = "IP address: ". $row['ip'];
                }else{
                    if($_SESSION['who'] == 'Admin'){
                        $sql2 = "SELECT * FROM admin where id =:id";
                    }
                    else{
                        $sql2 = "SELECT * FROM iuser where id = :id";
                    }
                    $st2 = $pdo->prepare($sql2);
                    $st2->execute(array(
                        ':id' => $row['uid']
                    ));
                    $row2 = $st2->fetch(PDO::FETCH_ASSOC);
                    $name = "Name: ".$row2['name'];
                    $email = "Email: ". $row2['email'];
                }
                // if msg is null
                if($row['msg'] == NULL){
            ?>

            <h3 class="dialog_text">
                    <?php echo(($_SESSION['unknown'] == 1)?$ip."<br>":$name."<br>".$email."<br>"); 
                    if($row['status'] == 1){   //is user is blocked
                        echo("<br>You are <ins><i>blocked</i></ins> due to use of suspicious Statement.<br>Enter message to admin");
                        echo("<textarea class='dialog_msg' name='msg' id='msg' rows=5></textarea>");
                        echo("<input type='submit' class='greenbutton loginbutton'>");
                    }elseif($row['status'] == 2){  //if user account is deleted
                        echo("<br>Your Account was <ins><i>deleted</i></ins> by Admin<br>Contact Customer Care...<br>
                            <br>Mobile No: +91-1234567890<br>Email: custmercar@cyber.com");
                    }
                    ?>
            </h3>
            <?php
                }else{ // if msg is not null
                    $_SESSION['msg'] = $row['msg'];
                
            ?>
            <h3 class="dialog_text">
                    <?php echo(($_SESSION['unknown'] == 1)?$ip."<br>":$name."<br>".$email."<br>"); 
                    if($row['status'] == 1){   //is user is blocked
                        echo("<br>You are <ins><i>blocked</i></ins> due to use of suspicious Statement.<br>You have already messaged Admin.<br> Edit your message Below");
                        echo("<textarea class='dialog_msg' name='msg' id='msg' rows=5>". $row['msg'] ."</textarea>");
                        echo("<input type='submit' class='greenbutton loginbutton'>");
                    }elseif($row['status'] == 2){    //if user account is deleted
                        echo("<br>Your Account was <ins><i>deleted</i></ins> by Admin<br>Contact Customer Care...<br>
                            <br>Mobile No: +91-1234567890<br>Email: custmercar@cyber.com");
                    }
                }
                    ?>
            </h3>
            </form>

        
        </div>
    </body>
</html>