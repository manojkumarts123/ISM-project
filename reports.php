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
        <?php include "menu.php";?>
        <div class="container">
            <div class="report_container">
                <div class="search">
                    <form method='post'>
                        <input type=text class='textbox' name='username'placeholder="Search by name"> 
                        <pre class='text'> or  Search by Date: </pre>
                        <input type=date  class='date' name='fromdate' id='fromdate' onchange="setMin()"> <pre class='text'> to </pre> <input type=date class='date' name='todate'>
                        <input type='submit' class="greenbutton submitbutton">
                        
                    </form>
                </div>
                <br>
                <?php
                    if(isset($_SESSION["error"])){
                        echo("<p style='color:tomato;font-weight:bold;'>".$_SESSION["error"]."</p><br>");
                        unset($_SESSION["error"]);
                    }
            
                ?>
                <table class="report_table">
                    <?php
                        if(isset($_POST['username']) && isset($_POST['fromdate']) && isset($_POST['todate'])){
                            //echo("<script>alert('data is found');</script>");
                            if($_POST['username'] != null && $_POST['fromdate'] ==null && $_POST['todate']==null){
                                //echo("<script>alert('username is found');</script>");
                                $st = $pdo->prepare("SELECT * FROM user_status where (status = 0 or status = 2) ORDER BY status_updated_time DESC");
                                $st->execute(array());
                                $column=0;
                                while($row = $st->fetch(PDO::FETCH_ASSOC)){
                                    //echo("<script>alert('SELECT * FROM user_status INNER JOIN ".($row['who']=='Admin'? 'admin': 'iuser')." ON user_status.uid=".($row['who']=='Admin'? 'admin': 'iuser').".id where (user_status.status = 0 or user_status.status = 2) AND (".($row['who']=='Admin'? 'admin': 'iuser').".name = :nam) ORDER BY status_updated_time DESC')</script>");
                                    $st1 = $pdo->prepare("SELECT * FROM ".($row['who']=='Admin'? 'admin': 'iuser')." WHERE id = :id AND name = :nam");
                                    $st1->execute(array( 
                                        ':id' => $row['uid'],
                                        ':nam' => $_POST['username'] ));
                                    $row1 = $st1->fetch(PDO::FETCH_ASSOC);
                                    if($row1)
                                        $column = display($column, $row, $row1);

                                }
                                if($column == 0){
                                    echo("<h4 style='color:white'>No data found...</h4>");
                                }   
                            }
                            
                            elseif($_POST['username'] == null && $_POST['fromdate'] !=null && $_POST['todate']!=null){
                                //echo("<script>alert('dates is found');</script>");
                                //echo("hi");
                                $st = $pdo->prepare("SELECT * FROM user_status where (status = 0 or status = 2) AND (DATE_FORMAT(status_updated_time, '%Y-%m-%d') BETWEEN DATE_FORMAT( :fd, '%Y-%m-%d') AND DATE_FORMAT(:td, '%Y-%m-%d')) ORDER BY status_updated_time DESC");
                                $FDate = $_POST['fromdate'];
                                $fromDate = date("Y-m-d", strtotime($FDate));
                                
                                $TDate = $_POST['todate'];
                                $toDate = date("Y-m-d", strtotime($TDate));
                                $st->execute(array(
                                    ':fd' => $fromDate,
                                    ':td' => $toDate
                                ));
                                $column=0;
                                while($row = $st->fetch(PDO::FETCH_ASSOC)){
                                    //echo("<script>alert('SELECT * FROM user_status INNER JOIN ".($row['who']=='Admin'? 'admin': 'iuser')." ON user_status.uid=".($row['who']=='Admin'? 'admin': 'iuser').".id where (user_status.status = 0 or user_status.status = 2) AND (".($row['who']=='Admin'? 'admin': 'iuser').".name = :nam) ORDER BY status_updated_time DESC')</script>");
                                    $st1 = $pdo->prepare("SELECT * FROM ".($row['who']=='Admin'? 'admin': 'iuser')." WHERE id = :id");
                                    $st1->execute(array( 
                                        ':id' => $row['uid']));
                                    $row1 = $st1->fetch(PDO::FETCH_ASSOC);
                                    if($row1)
                                        $column = display($column, $row, $row1);

                                }
                                if($column == 0){
                                    echo("<h4 style='color:white'>No data found...</h4>");
                                }
                                /*$st = $pdo->prepare("SELECT * FROM user_status INNER JOIN ".($_SESSION['who']=='Admin'? 'admin': 'iuser')." ON user_status.uid=".($_SESSION['who']=='Admin'? 'admin': 'iuser').".id where (user_status.status = 0 or user_status.status = 2) AND (DATE_FORMAT(status_updated_time, '%Y-%m-%d') BETWEEN DATE_FORMAT( :fd, '%Y-%m-%d') AND DATE_FORMAT(:td, '%Y-%m-%d')) ORDER BY status_updated_time DESC");
                                $FDate = $_POST['fromdate'];
                                $fromDate = date("Y-m-d", strtotime($FDate));
                                
                                $TDate = $_POST['todate'];
                                $toDate = date("Y-m-d", strtotime($TDate));
                                //echo($fromDate." ". $toDate);
                                $st->execute(array(
                                    ':fd' => $fromDate,
                                    ':td' => $toDate
                                ));
                                display($st);*/
                            }

                            elseif($_POST['username'] == null && $_POST['fromdate'] ==null && $_POST['todate']!=null){
                                $_SESSION['error'] = "Please Enter from date<br>";
                                header( 'Location: reports.php' ) ;
                            }

                            elseif($_POST['username'] == null && $_POST['fromdate'] !=null && $_POST['todate']==null){
                                $_SESSION['error'] = "Please Enter to date";
                                header( 'Location: reports.php' ) ;
                            }

                            elseif($_POST['username'] != null && $_POST['fromdate'] !=null && $_POST['todate']!= null){
                                //echo("<script>alert('all data is found');</script>");
                                //echo("bye");
                                //SELECT * FROM user_status where (status = 0 or status = 2) AND (DATE_FORMAT(status_updated_time, '%Y-%m-%d') BETWEEN DATE_FORMAT( '2021-05-13', '%Y-%m-%d') AND DATE_FORMAT('2021-05-13', '%Y-%m-%d')) ORDER BY status_updated_time DESC
                                $st = $pdo->prepare("SELECT * FROM user_status where (status = 0 or status = 2) AND (DATE_FORMAT(status_updated_time, '%Y-%m-%d') BETWEEN DATE_FORMAT( :fd, '%Y-%m-%d') AND DATE_FORMAT(:td, '%Y-%m-%d')) ORDER BY status_updated_time DESC");
                                $FDate = $_POST['fromdate'];
                                $fromDate = date("Y-m-d", strtotime($FDate));
                                
                                $TDate = $_POST['todate'];
                                $toDate = date("Y-m-d", strtotime($TDate));
                                $st->execute(array(
                                    ':fd' => $fromDate,
                                    ':td' => $toDate
                                ));
                                $column=0;
                                while($row = $st->fetch(PDO::FETCH_ASSOC)){
                                    //echo("<script>alert('SELECT * FROM user_status INNER JOIN ".($row['who']=='Admin'? 'admin': 'iuser')." ON user_status.uid=".($row['who']=='Admin'? 'admin': 'iuser').".id where (user_status.status = 0 or user_status.status = 2) AND (".($row['who']=='Admin'? 'admin': 'iuser').".name = :nam) ORDER BY status_updated_time DESC')</script>");
                                    $st1 = $pdo->prepare("SELECT * FROM ".($row['who']=='Admin'? 'admin': 'iuser')." WHERE id = :id AND name = :nam");
                                    $st1->execute(array( 
                                        ':id' => $row['uid'],
                                        ':nam' => $_POST['username'] ));
                                    $row1 = $st1->fetch(PDO::FETCH_ASSOC);
                                    if($row1)
                                        $column = display($column, $row, $row1);

                                }
                                if($column == 0){
                                    echo("<h4 style='color:white'>No data found...</h4>");
                                }
                                /*$st = $pdo->prepare("SELECT * FROM user_status INNER JOIN ".($_SESSION['who']=='Admin'? 'admin': 'iuser')." ON user_status.uid=".($_SESSION['who']=='Admin'? 'admin': 'iuser').".id where (user_status.status = 0 or user_status.status = 2) AND (".($_SESSION['who']=='Admin'? 'admin': 'iuser').".name = :nam) AND (DATE_FORMAT(status_updated_time, '%Y-%m-%d') BETWEEN DATE_FORMAT( :fd, '%Y-%m-%d') AND DATE_FORMAT(:td, '%Y-%m-%d')) ORDER BY status_updated_time DESC");
                                $st->execute(array(
                                    ':fd' => $_POST['fromdate'],
                                    ':td' => $_POST['todate'],
                                    ':nam' => $_POST['username']
                                ));
                                display($st);*/
                            }

                            else{
                                $_SESSION['error'] = "Please Enter complete data(incharge name or dates or both)";
                                header( 'Location: reports.php' ) ;
                            }
                        }

                        else{
                            
                                //echo("<script>alert('data is not entered');</script>");
                                $st = $pdo->prepare("SELECT * FROM user_status where (status = 0 or status = 2) ORDER BY status_updated_time DESC");
                                $st->execute(array());
                                $column=0;
                                while($row = $st->fetch(PDO::FETCH_ASSOC)){
                                    //echo("<script>alert('SELECT * FROM user_status INNER JOIN ".($row['who']=='Admin'? 'admin': 'iuser')." ON user_status.uid=".($row['who']=='Admin'? 'admin': 'iuser').".id where (user_status.status = 0 or user_status.status = 2) AND (".($row['who']=='Admin'? 'admin': 'iuser').".name = :nam) ORDER BY status_updated_time DESC')</script>");
                                    $st1 = $pdo->prepare("SELECT * FROM ".($row['who']=='Admin'? 'admin': 'iuser')." WHERE id = :id");
                                    $st1->execute(array( 
                                        ':id' => $row['uid']));
                                    $row1 = $st1->fetch(PDO::FETCH_ASSOC);
                                    if($row1)
                                        $column = display($column, $row, $row1);

                                }
                                if($column == 0){
                                    echo("<h4 style='color:white'>No data found...</h4>");
                                }
                                /*
                                $st = $pdo->prepare("SELECT * FROM user_status INNER JOIN ".($_SESSION['who']=='Admin'? 'admin': 'iuser')." ON user_status.uid=".($_SESSION['who']=='Admin'? 'admin': 'iuser').".id where (user_status.status = 0 or user_status.status = 2) ORDER BY status_updated_time DESC");
                                $st->execute(array());
                                display($st);*/
                        }
                    ?>
                </table>    
            </div>
        </div>
    </body>
    <?php 
    // function to display tables
    function display($column, $row, $row1){
        
        if($column == 0){
            echo("<tr>
                <th>S.No</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Command</th>
                <th>Message</th>
                <th>Status</th>
                <th>Date of Action</th>
            </tr>");
            $column++;
        }
        echo("<tr>");
        echo("<td>".$column++."</td>");
        echo("<td>".$row1['name']."</td>");
        echo("<td>".$row1['email']."</td>");
        echo("<td>".$row['who']."</td>");
        echo("<td>".$row['command']."</td>");
        echo("<td>".$row['msg']."</td>");
        echo("<td>".$row['status']."</td>");
        echo("<td>".$row['status_updated_time']."</td>");
        echo("</tr>");
    
    return $column;
    }
    ?>
    <script>
        // function to set min value of todate calender field
        function setMin(){
            var dat = document.getElementById('fromdate').value;
            document.getElementsByName('todate')[0].setAttribute('min', dat);
        }
    </script>
</html>