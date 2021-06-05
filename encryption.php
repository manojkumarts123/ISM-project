<?php 
    $pdo = new PDO('mysql:host = localhost; port = 3306; dbname=ism', 'Manoj', '123');
    session_start();

    if(isset($_POST['encrypt'])){
        if(trim($_POST['string']) == "" && trim($_POST['key']) == ""){
            echo("<script>alert('".$_POST['string']."')</script>");
            echo ("<script>alert('Input field is empty.Enter string to Encrypt/Decrypt')</script>");
        }else{
            $wordlen = strlen($_POST['string']);
            $keylen = strlen($_POST['key']);
            $length =  $wordlen + $keylen + 5;
            $encrypt = array();
            //print_r($encrypt);
           
            /*--key length--*/
            //echo($length);
            $num1 = (int)(($wordlen +$keylen)/2);
            $encrypt[$num1] = $keylen;

            /*--salting--*/

            $salt = $wordlen - $num1+2;
            //echo("salt:" . $salt);
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $even = '2468';
            $odd = '13579';
            if(($wordlen+$keylen) %2 == 0){
                
                // word
                $index=0;
                //print_r($encrypt);
                for($i=0; $i<$length; $i+=2){
                    if(empty($encrypt[$i])){
                        //echo $i . " " . $_POST['string'][$index];
                        $encrypt[$i] = $_POST['string'][$index];
                        $index++;
                    }
                    if($index == $wordlen){
                        break;
                    }
                }
        
                $index=0;
                //key
                for($i=0; $i < $length; $i++){
                    if(empty($encrypt[$i])){
                        //echo "<br>".$i. " ". $_POST['key'][$index];
                        $encrypt[$i] = $_POST['key'][$index++];
                    }
                    if($index == $keylen){
                        break;
                    }
                }
                
                //print_r($encrypt);
                $addedsalt = 0;
                for($i=$wordlen+$salt; $i < $length-1; $i += $salt  ){
                    if($addedsalt == 2){
                        break;
                    }
                    if(empty($encrypt[$i])){
                        $encrypt[$i] = $characters[rand(0, strlen($characters) - 1)];
                        $addedsalt++;
                    }else{
                        if(empty($encrypt[$i+1])){
                            $encrypt[$i+1] = $characters[rand(0, strlen($characters) - 1)];
                            $addedsalt++;
                        }
                    }
                }
                //print_r($encrypt);
                $index=$length-2;
                while($addedsalt !=2){
                    if(empty($encrypt[$index])){
                        $encrypt[$index] = $characters[rand(0, strlen($characters) - 1)];
                        $addedsalt++;
                    }
                    $index--;
                }
                //print_r($encrypt);
                if(empty($encrypt[$length-2]))
                    $encrypt[$length-2] = $even[rand(0, strlen($even) - 1)];
                if(empty($encrypt[$length-1]))
                    $encrypt[$length-1] = $characters[rand(0, strlen($characters) - 1)];
                //print_r($encrypt);
                $index=$length-1;
                while(sizeof($encrypt) != $length){
                    if(empty($encrypt[$index])){
                        //echo "<br>".$index. " ";
                        $encrypt[$index] = $characters[rand(0, strlen($characters) - 1)];
                        $addedsalt++;
                    }
                    $index--;
                }
                //print_r($encrypt);
                ksort($encrypt);
                //print_r($encrypt);
                //print_r($encrypt);
                $encrypt = implode("", $encrypt);
                echo("<script>alert('Encrypted text:".$encrypt."')</script>");
            }else{
                $index=0;

                //odd so key first
                for($i=0; $i < $length; $i+=2){
                    if(empty($encrypt[$i])){
                        //echo $i. " ". $_POST['key'][$index];
                        $encrypt[$i] = $_POST['key'][$index++];
                    }
                    if($index == $keylen){
                        break;
                    }
                }
                //print_r($encrypt);

                //adding salt
                $addedsalt = 0;
                for($i=$wordlen+$salt; $i < $length-2; $i += $salt  ){
                    if(empty($encrypt[$i])){
                        $encrypt[$i] = $characters[rand(0, strlen($characters) - 1)];
                        $addedsalt++;
                    }else{
                        $encrypt[$i+1] = $characters[rand(0, strlen($characters) - 1)];
                        $addedsalt++;
                    }
                }
                if($addedsalt !=2){
                    $encrypt[$length-3] = $characters[rand(0, strlen($characters) - 1)];
                    $addedsalt++;
                }
                //print_r($encrypt);
        
                $encrypt[$length-1] = $odd[rand(0, strlen($even) - 1)];
                $encrypt[$length] = $characters[rand(0, strlen($characters) - 1)];

                //print_r($encrypt);
                $index=0;
                //print_r($encrypt);
                for($i=0; $i<$length; $i++){
                    if(empty($encrypt[$i])){
                        //echo $i . " " . $_POST['string'][$index];
                        $encrypt[$i] = $_POST['string'][$index];
                        $index++;
                    }
                    if($index == $wordlen){
                        break;
                    }
                }
                ksort($encrypt);
                //print_r($encrypt);
                $encrypt = implode("", $encrypt);
                echo("<script>alert('Encrypted text:".$encrypt."')</script>");


            }

        }

    }else if(isset($_POST['decrypt'])){
        if(trim($_POST['string']) == "" && trim($_POST['key']) == ""){
            echo ("<script>alert('Input field is empty.Enter string to Encrypt/Decrypt')</script>");
        }else{
            $length = strlen($_POST['string']);
            $string = str_split($_POST['string']);
            $keylen = strlen($_POST['key']);
            $wordlen = $length -$keylen-5;
            $decrypt = array(); $flag = 0;
            
            /*--key length--*/

            $num1 = (int)(($wordlen +$keylen)/2);

            //echo("wordlen:" . $wordlen);
            //echo("keylen: ".$keylen);
            //echo("num1: ".$num1);
            
            if($string[$num1] != strlen($_POST['key'])){
                $flag = 1;
                goto a;
            }

            /*if($string[$length-2] % 2 == 0){
                $wordlen = ($num1*2) -$keylen;
            }else{
                $wordlen = ($num1*2) -$keylen+1;
            }*/
            //echo("<script>alert('key length matches".$string[$num1]."')</script>");
                /*--salting--*/

                $salt = $wordlen - $num1+2;
                //echo("salt:" . $salt);

                if(($wordlen+$keylen) %2 == 0){
                    

                    $temp=0;$index=0;$addedsalt=0;
                    //echo($wordlen." ".$salt." ".$length);

                    /*if($addedsalt != 2){
                        $string[$length-3] = -1;
                        $addedsalt++;
                    }*/
                    //print_r($string);
                    //word
                    $index=0;
                    for($i=0; $i<$length; $i+=2){
                       if($i == $num1 || $string[$i] == -1){
                            continue;
                        }
                        $decrypt[$index++] = $string[$i];
                        $string[$i] = -1;
                        if($index == $wordlen){
                            
                            break;
                        }
                    }
                    if($index != $wordlen){
                        //echo("inside if");
                        for($i=$length-3; $i>0; $i--){
                            //echo("inside ifor");
                            if($string[$i] != -1){
                                //echo("inside wanted if ". $i);
                                $decrypt[$index++] = $string[$i];
                                $string[$i] = -1;
                            }
                            if($index == $wordlen){
                                break;
                            }
                        }
                    }
                    //print_r($decrypt);
                    //print_r($string);
                    //print_r($decrypt);
                    $index=0;
                    for($i=1; $i < $length; $i++){
                        //echo ($string[$i]." ".$_POST['key'][$index]. "<br>");
                        if($string[$i] == $_POST['key'][$index]){
                            $index++;
                            if($index == $keylen){
                                //echo("<script>alert('Key found')</script>");
                                break;
                            }
                            continue;
                        }else if($i == $num1 ||$string[$i] == -1 ){
                            continue;
                        }else{
                            $flag = 1;
                            goto a;
                            
                        }
                    }
                    //even do salt first
                    //print_r($string);
                    /*for($i=$wordlen+$salt; $i<$length-2; $i+=$salt){
                        if($i == $wordlen+$salt+$temp){
                            //echo "<br> inside".$i." ".$wordlen+$salt+$temp;
                            $string[$i] = -1;
                            $temp += $salt;
                            $addedsalt++;
                        }
                        if($addedsalt == 2){
                            
                            break;
                        }
                    }*/

                    $temp=0;
                    /*while($addedsalt !=2){
                        unset($decrypt[sizeof($decrypt)-1]);
                        $addedsalt++;
                    }*/
                    
                    $decrypt = implode("", $decrypt);
                    echo("<script>alert('Decrypted text:".$decrypt."')</script>");
                }else{
                    $index=0;

                    //odd so key first
                    for($i=0; $i < $length; $i+=2){
                        //echo ($string[$i]." ".$_POST['key'][$index]. "<br>");
                        if($string[$i] == $_POST['key'][$index]){
                            $string[$i] = -1;
                            $index++;
                            if($index == $keylen){
                                //echo("<script>alert('Key found')</script>");
                                break;
                            }
                            continue;
                        }else if($i == $num1){
                            continue;
                        }else{
                            $flag = 1;
                            goto a;
                            
                        }
                    }

                    //removing salt
                    $temp=0;$index=0;$addedsalt=0;
                    for($i=0; $i<$length; $i++){
                        if($i == $wordlen+$salt+$temp){
                            if($string[$i] == -1)
                                $i++;
                            //echo "<br>".$i." ".$wordlen+$salt+$temp;
                            $temp += $salt;
                            $addedsalt++;
                            continue;
                        }else if($string[$i] == -1 || $i == $num1 || $i == $length-2 || $i == $length-1){
                            continue;
                        }
                        $decrypt[$index++] = $string[$i];
                    }
                    
                    $temp=0;
                    while($addedsalt !=2){
                        unset($decrypt[sizeof($decrypt)-1]);
                        $addedsalt++;
                    }
                    
                    $decrypt = implode("", $decrypt);
                    echo("<script>alert('Decrypted text:".$decrypt."')</script>");
                }
        }
        a:
        if($flag == 1){
            echo("<script>alert('Your key is wrong')</script>");
        }
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
        <?php include 'menu.php'; ?>        <!--importing menu-->

        <div class="encrypt_container">
            <form method="post">
                <h1 style="color:white; text-align:center">Encryption</h1><br>
                <textarea class="textarea" name="string" rows="20" placeholder="Enter string to Encrypt/Decrypt"></textarea><br><br>

                <input  class="textarea" name="key" placeholder="Enter the key"/><br><br>
                <input type="submit" class="greenbutton encrypt_input" name="encrypt" value="Encrypt">
                <input type="submit" class="redbutton encrypt_input"name="decrypt" value="Decrypt">
            </form>
        </div>

    </body>
</html>