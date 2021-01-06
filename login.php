<?php
    session_start();

    if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
        header("location: welcome.php");
        exit;
    }

    define('DB_SERVER', 'sql203.epizy.com');
    define('DB_USERNAME', 'epiz_27106493');
    define('DB_PASSWORD', 'xGHEC6jV1X');
    define('DB_NAME', 'epiz_27106493_agentie_turism');
     
    $link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
     
    if (!$link) {
            echo "Error: Unable to connect to MySQL.";
            exit;
        }
     
    $username = $password = "";
    $username_err = $password_err = "";

    /*
        if (isset($_POST['tip'])) {
        $tip = filter_var($_POST['tip'], FILTER_SANITIZE_STRING);
    }
    */

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        
        if (isset($_POST['username'])) {
            if(empty(trim($_POST["username"]))){
                $username_err = "Va rugam introduceti username-ul.";
            } else{
                $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
            }
        }
        
        if (isset($_POST['password'])) {
            if(empty(trim($_POST["password"]))){
                $password_err = "Va rugam introduceti parola.";
            } else{
                $password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);
            }
        }

        if(empty($username_err) && empty($password_err)){

            $sql = "SELECT user_id, username, password_, user_type FROM Users WHERE username = ?";
            
            if($stmt = mysqli_prepare($link, $sql)){
      
                mysqli_stmt_bind_param($stmt, "s", $param_username);
                $param_username = $username;
                
                if(mysqli_stmt_execute($stmt)){
                    mysqli_stmt_store_result($stmt);

                    if(mysqli_stmt_num_rows($stmt) == 1){                    
                        mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password, $user_type);
                        if(mysqli_stmt_fetch($stmt)){
                            if(password_verify($password, $hashed_password)){
                         
                                session_start();
                             
                                $_SESSION["loggedin"] = true;
                                $_SESSION["id"] = $id;
                                $_SESSION["username"] = $username;
                                $_SESSION["user_type"] = $user_type;                          
                               
                                header("location: welcome.php");
                            } else{
                                $password_err = "Parola incorecta.";
                            }
                        }
                    } else{
                        $username_err = "Acest username nu exista.";
                    }
                } else{
                    echo "Oops! Something went wrong. Please try again later.";
                }
                mysqli_stmt_close($stmt);
            }
        }
        mysqli_close($link);
    }
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login</title>
    <style type="text/css">
        body {
            font: 14px sans-serif; 
        }
        .wrapper{ 
            width: 350px; 
            padding: 20px; 
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <h1>Autentificare</h1>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                <label><h3>Username</h3></label>
                <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
                <br>
                <span style="color: red;"><?php echo $username_err; ?></span>
            </div>    
            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <label><h3>Parola</h3></label>
                <input type="password" name="password" class="form-control">
                <br>
                <span style="color: red;"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Login">
            </div>
            <p>Nu ai cont? <a href="newAcc.php">Inregistreaza-te acum!</a>.</p>
        </form>
    </div>    
</body>
</html>