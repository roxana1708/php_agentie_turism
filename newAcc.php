<?php  

    define('DB_SERVER', 'sql203.epizy.com');
    define('DB_USERNAME', 'epiz_27106493');
    define('DB_PASSWORD', 'xGHEC6jV1X');
    define('DB_NAME', 'epiz_27106493_agentie_turism');

    $link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

    if (!$link) {
            echo "Error: Unable to connect to MySQL.";
            exit;
        }
     
    $username = $password = $confirm_password = $firstName = $secondName = "";
    $username_err = $password_err = $confirm_password_err = $firstName_err = $secondName_err = "";
     
    if(empty(trim($_POST["username"]))){
        $username_err = "Va rugam introduceti un username.";
    } else{
        $sql = "SELECT user_id FROM Users WHERE username = ?";
            
        if($stmt = mysqli_prepare($link, $sql)){
        
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            $param_username = trim($_POST["username"]);
       
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                    
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $username_err = "Username indisponibil.";
                } else{
                    $username = trim($_POST["username"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }
    }
        

    if (isset($_POST['secondName'])) {
        if(empty(trim($_POST["secondName"]))){
            $secondName_err = "Va rugam introduceti un nume.";
        } else{
            $secondName = filter_var($_POST['secondName'], FILTER_SANITIZE_STRING);
        }
    }

    if (isset($_POST['firstName'])) {
        if(empty(trim($_POST["firstName"]))){
            $firstName_err = "Va rugam introduceti un prenume.";
        } else {
            $firstName = filter_var($_POST['firstName'], FILTER_SANITIZE_STRING);
        }
    }

    if (isset($_POST['email'])) {
        if(empty(trim($_POST["email"]))){
            $email_err = "Va rugam introduceti un email."; 
        } else {
            $email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $email_err = "Email invalid"; 
            }

            $emailArray = explode("@", $email);

            //var_dump($emailArray);

            if (checkdnsrr(array_pop($emailArray), "MX")) {
                //print "valid email domain";
                //$email = $_POST["email"];
            } else {
                $email_err = "Invalid email format"; 
            }
        }
    }

    if (isset($_POST['password'])) {
        if(empty(trim($_POST["password"]))){
            $password_err = "Va rugam introduceti o parola.";     
        } elseif(strlen(trim($_POST["password"])) < 6){
            $password_err = "Parola trebuie sa aiba cel putin 6 caractere.";
        } else{
            $password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);;
        }
    }

    if (isset($_POST['confirm_password'])) {
        if(empty(trim($_POST["confirm_password"]))){
            $confirm_password_err = "Va rugam confirmati parola.";     
        } else{
            $confirm_password = filter_var($_POST['confirm_password'], FILTER_SANITIZE_STRING);
            if(empty($password_err) && ($password != $confirm_password)){
                $confirm_password_err = "Parola nu se potriveste.";
            }
        }
    }

    /*
    if (isset($_POST['tip'])) {
        $user_type = filter_var($_POST['tip'], FILTER_SANITIZE_STRING);
    }
    */

    if(empty($username_err) && empty($password_err) && empty($confirm_password_err) && empty($email_err)){


        if(isset($_POST['user_type'])) {
            $sql = "INSERT INTO Users (username, password_, email, nume, prenume, user_type) VALUES (?, ?, ?, ?, ?, ?)";
                 
            if($stmt = mysqli_prepare($link, $sql)){
                mysqli_stmt_bind_param($stmt, "ssssss", $param_username, $param_password, $param_email, $param_secondName, $param_firstName, $param_user_type);
      
                $param_username = $username;
                $param_email = $email;
                $param_password = password_hash($password, PASSWORD_DEFAULT);
                $param_secondName = $secondName;
                $param_firstName = $firstName;
                $param_user_type = $user_type;
                
                if(mysqli_stmt_execute($stmt)){
                    header("location: login.php");
                } else{
                    echo "Something went wrong. Please try again later.";
                }
                mysqli_stmt_close($stmt);
            }
        } else {
            $sql = "INSERT INTO Users (username, password_, email, nume, prenume, user_type) VALUES (?, ?, ?, ?, ?, ?)";

            if($stmt = mysqli_prepare($link, $sql)){
                mysqli_stmt_bind_param($stmt, "ssssss", $param_username, $param_password, $param_email, $param_secondName, $param_firstName, $param_user_type);
      
                $param_username = $username;
                $param_email = $email;
                $param_password = password_hash($password, PASSWORD_DEFAULT);
                $param_secondName = $secondName;
                $param_firstName = $firstName;
                $param_user_type = 'user';
                
                if(mysqli_stmt_execute($stmt)){
                    header("location: login.php");
                } else{
                    echo "Something went wrong. Please try again later.";
                }
                mysqli_stmt_close($stmt);
            }
        }
       mysqli_close($link);     
    }
    
?>

<html lang="en">
<head>
    <title>Sign Up</title>
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h1>Membru nou</h2>
        <p>Creati un cont nou completand formularul.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="<?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                <label><h3>Username</h3></label>
                <input type="text" name="username">
                <br>
                <span style="color: red;"><?php echo $username_err; ?></span>
            </div>  
            
            <div class="<?php echo (!empty($secondName_err)) ? 'has-error' : ''; ?>">
                <label><h3>Nume</h3></label>
                <input type="text" name="secondName">
                <br>
                <span style="color: red;"><?php echo $secondName_err; ?></span>
            </div>

            <div class="<?php echo (!empty($firstName_err)) ? 'has-error' : ''; ?>">
                <label><h3>Prenume</h3></label>
                <input type="text" name="firstName">
                <br>
                <span style="color: red;"><?php echo $firstName_err; ?></span>
            </div>  

            <div class="<?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                <label><h3>Email</h3></label>
                <input type="text" name="email">
                <br>
                <span style="color: red;"><?php echo $email_err; ?></span>
            </div>   

            <div class="<?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <label><h3>Parola</h3></label>
                <input type="password" name="password">
                <br>
                <span style="color: red;"><?php echo $password_err; ?></span>
            </div>

            <div class="<?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                <label><h3>Confirmare Parola</h3></label>
                <input type="password" name="confirm_password">
                <br>
                <span style="color: red;"><?php echo $confirm_password_err; ?></span>
            </div>

<!--
            <div>
                <label><h3>Client, Organizator, Angajat sau Supervizor?</h3></label>
                
                <select name="user_type">
                    <option value="client">Client</option>
                    <option value="org">Organizator</option>
                    <option value="angajat">Angajat</option>
                    <option value="supervizor">Supervizor</option>
                </select>
               
            </div>
 -->
            <br>
            
            <div>
                <input type="submit" value="Submit">
            </div>
            <p>Aveti deja cont? <a href="login.php">Login aici</a>.</p>
        </form>
    </div>    
</body>
</html>
