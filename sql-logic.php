<?php 
    //connection
    //$pdo = new PDO('mysql:host = localhost; port = 3306; dbname=ism', 'Manoj', '123');
    
    if(isset($_GET['str'])){
        check($_GET['str']);
    }

    function chk($pdo, $id, $who){  // id is ip in case of unknown and id of user in case of known(email is validated and user is known)
        if($_SESSION['unknown'] == 1){
            echo("<script>alert('inside unknown');</script>");
            $sql = "SELECT * FROM unknown where ip = :id";
            $st = $pdo->prepare($sql);
            $st->execute(array(
                ':id' => $id) 
            );
        }
        else{
            $sql = "SELECT * FROM user_status where who = :who and uid = :id";
            $st = $pdo->prepare($sql);
            $st->execute(array(
                ':id' => $id,
                ':who' => $who) 
            );
        }
        while($row = $st->fetch(PDO::FETCH_ASSOC)){
            echo("<script>alert('inside while');</script>");
            if($_SESSION['unknown'] == 1){
                if($row['status'] == 1){  // 1->blocked
                    $_SESSION['blocked_id'] = $row['id'];
                    echo("<script>alert('found: ".$_SESSION['blocked_id']."');</script>");
                    return 1;
                }
            }else{
                if($row['status'] == 1 || $row['status'] == 2){  // 1->blocked 2->deleted
                    $_SESSION['blocked_id'] = $row['id'];
                   return 1;
                }
            }
        }
        return 0;
    }
    
    function check($pdo, $str){
        //print_r($_SESSION);
        //echo("<script>alert('".$str."');</script>");
        $flag=0;   // variable to indicate whether sql injection attack found    // 0 -> not found, 1 -> found

        /*---- "1"="1" ---- */

        $arr = explode(" ",$str); // $arr has words from sql injection command
        
        foreach($arr as $item){
            for($var = 0; $var < strlen($item); $var++){
                if($item[$var] == "="){ // if =  is found
                    $check = explode("=", $item);
                    //print_r($check);
                    if($check[0] == $check[1] || !$check){
                        //echo("<script>alert('sql Injection found');</script>");
                        $flag = 1;
                        goto a;
                        break;
                    }
                    else{
                        $check[1] = $check[1]."\""; //add " at end and check again so "1=1
                        //print_r($check);
                        if($check[0] == $check[1]){
                            //echo("<script>alert('Executing: sql Injection found');</script>");
                            $flag = 1;
                            goto a;
                            break;
                        }
                        //else{
                            //echo("<script>alert('NO sql Injection found');</script>");
                    
                        //}
                    }
                }
            }
        }

        //echo("<script>alert('".$str."');</script>");

        /*----comments----- */

        $pattern1 = "/--|;--|#|\/\*|\*\/|@@/";

        if(preg_match($pattern1, $str)){
            //echo("<script>alert('match found');</script>");
            $flag = 1;
            goto a;
        }

        /*----sql keywords----- */

        $file = fopen("command.txt", "r") or die("Unable to open file!");    //import file

        while(!feof($file)){

            $command = rtrim(fgets($file));
            $pattern2 = "/(\s+|;|\(|\")".$command."\s+/i";
            //echo($pattern2);
            if(preg_match($pattern2, $str)){
                //echo("<script>alert('command match found');</script>");
                $flag = 1;
                goto a;
                break;
            }
            //else{
                //echo("<script>alert('match not found');</script>");
            //}
        }

        a:
        if($flag == 1){
            if($_SESSION['unknown'] == 1){
                 return block_unknown($pdo, $_SESSION['ip'], $str);
            }
            else{
                 return block($pdo, $_SESSION['user'], $str);
            }
        }
        return 0;
    }

    function block_unknown($pdo, $ip, $str){

        echo("<script>alert('".$ip."');</script>");
        $sql = "INSERT INTO `unknown`(`ip`, `command`,`status`) VALUES (:ip, :command, 1)";
        $st = $pdo->prepare($sql);
        $st->execute(array(
            ':ip' => $ip,
            ':command' => $str)
        );
        $_SESSION['blocked_id'] = $pdo->lastInsertId();
        return 1;
    }
    
    // 0 -> not blocked, 1 -> blocked, 2 -> account deleted

    function block($pdo, $user, $str){
        $sql = "INSERT INTO user_status ( `uid`, `command`,`status`, who) VALUES (:user, :command, 1, :who)";
        $st = $pdo->prepare($sql);
        $st->execute(array(
            ':user' => $user,
            ':command' => $str,
            ':who' => $_SESSION['who'])
        );
        $_SESSION['blocked_id'] = $pdo->lastInsertId();
        return 1;
    }

    function unblock($pdo, $id){
        $sql = "UPDATE user_status SET status = 0 WHERE id = :id";
        $st = $pdo->prepare($sql);
        $st->execute(array(
            ':id' => $id)
        );
    }

    function unblock_ip($pdo, $id){
        $sql = "UPDATE unknown SET status = 0 WHERE id = :id";
        $st = $pdo->prepare($sql);
        $st->execute(array(
            ':id' => $id)
        );
    }

    function delete($pdo, $id){
        $sql = "UPDATE user_status SET status = 2 WHERE id = :id";
        $st = $pdo->prepare($sql);
        $st->execute(array(
            ':id' => $id)
        );
    }

    function update_msg($pdo, $id, $msg){
        if($_SESSION['unknown'] == 1){
            $sql = "UPDATE unknown SET msg = :msg, msg_time = CURRENT_TIMESTAMP WHERE id = :id";
        }else{
            $sql = "UPDATE user_status SET msg = :msg, msg_time = CURRENT_TIMESTAMP WHERE id = :id";
        }
        $st = $pdo->prepare($sql);
        $st->execute(array(
            ':msg' => $msg,
            ':id' => $id)
        );
    }


?>
<!--<html>
    
    <body>
        <form method="get">
            Enter String:
            <input type="text" name="str"/>
            <input type="submit"/>
        </form> 
    </body>
    
</html>-->







<?php



/*
$str = "" or ""="";
$arr = explode(" ",$str);
foreach($arr as $item){
	for($var = 0; $var < strlen($item); $var++){
    	if($item[$var] == "="){
        	$check = explode("=", $item);
            print_r($check);
            if($check[0] == $check[1]){
            	echo ("\nsql injection detected");
            }
            else{
            	echo("\nNo sql Injection");
            }
        }
    }
}
*/
?> 
