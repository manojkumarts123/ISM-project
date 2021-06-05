<?php 
    $pdo = new PDO('mysql:host = localhost; port = 3306; dbname=ism', 'Manoj', '123');
    session_start();
    require "sql-logic.php";   // importing sql-logic.php
    
    //Alerting for success of unblock and delete
    if(isset($_GET['success']) && $_GET['success'] == 1){
        echo("<script>alert('User unblocked successfully...');</script>");

    }elseif(isset($_GET['success']) && $_GET['success'] == 2){
        echo("<script>alert('User deleted successfully...');</script>");
    }

    if(isset($_POST['unblock'])){
        unblock($pdo, $_POST['id']);      // function in sql-logic.php
        header("location: malicious-users.php?success=1");

    }elseif(isset($_POST['delete'])){
        delete($pdo, $_POST['id']);       // function in sql-logic.php
        header("location: malicious-users.php?success=2");
    }
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
        <?php include "menu.php"  ?>       <!--Importing menu-->
        <div class="container">
            <div class="report_container">
                <?php 
                    $sql = "SELECT * FROM user_status where status = 1";
                    $st = $pdo->prepare($sql);
                    $st->execute(array());
                ?>
                <table class="report_table">
                    <?php
                        $count = 0;
                        while($row = $st->fetch(PDO::FETCH_ASSOC)){
                            if($count == 0){
                                echo("<tr>
                                    <th>S.No</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Command</th>
                                    <th>Message</th>
                                    <th>Action</th>
                                </tr>");
                            }
                            if($row['who'] == 'User'){
                                $sql2 = "SELECT * FROM iuser where id = :id";
                            }else{
                                $sql2 = "SELECT * FROM admin where id = :id";
                            }
                            $st2 = $pdo->prepare($sql2);
                            $st2->execute(array(':id' => $row['uid']));
                            $row2 = $st2->fetch(PDO::FETCH_ASSOC);
                            echo("<tr>");
                            echo("<td>".++$count."</td>");
                            echo("<td>".$row2['name']."</td>");
                            echo("<td>".$row2['email']."</td>");
                            echo("<td>".$row['who']."</td>");
                            echo("<td>".$row['command']."</td>");
                            echo("<td>".$row['msg']."</td>");
                            echo("<td>
                                    <form method='post'>
                                        <input type='hidden' name='id' value='".$row['id']."'>
                                        <input type='submit' name='unblock' class='greenbutton actionbtn' value='Unblock'>&emsp;
                                        <input type='submit' name='delete' class='redbutton actionbtn' value='Delete Account'>
                                    </form>
                                  </td>");
                            echo("</tr>");
                        }
                        if($count == 0){
                            echo("<h3 style='color: white'>No users to process...</h3>");
                          }
                    ?>
                </table>
            </div>
        </div>
    </body>
</html>