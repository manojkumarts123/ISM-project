<html>
    <body>
    <div class="nav clearfix">
            <ul>
                <li class="lfloat"><a href="index.php">Home</a></li>
                <?php 
                    if($_SESSION['who'] == 'User'){
                        echo("<li class='lfloat' ><a href='encryption.php'>Encryption</a></li>");
                    }
                    else{
                        echo("<li class='lfloat' ><a href='malicious-users.php'>Malicious Users</a></li>");
                        echo("<li class='lfloat' ><a href='malicious-unknown-users.php'>Malicious unknown Users</a></li>");
                        echo("<li class='lfloat' ><a href='reports.php'>Reports</a></li>");
                    }
                ?>
                <li class="rfloat"><a href="#profile">Profile</a></li>
                <li class="rfloat"><a href="logout.php">Logout</a></li>
            </ul> 
    </div>
    </body>
</html>
