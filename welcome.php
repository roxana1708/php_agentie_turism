<?php
    session_start();
 
    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
        header("location: login.php");
        exit;
    }
    elseif ($_SESSION["user_type"] == client) {
        header("location: meniu_client.php");
        exit;
    }
    elseif ($_SESSION["user_type"] == org) {
        header("location: meniu_org.php");
        exit;
    }
    elseif ($_SESSION["user_type"] == angajat) {
        header("location: meniu_ang.php");
        exit;
    }
    elseif ($_SESSION["user_type"] == admin) {
        header("location: meniu_admin.php");
        exit;
    }
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Welcome</title>
    <style type="text/css">
        body { 
            font: 14px sans-serif; 
            text-align: center; 
        }

        .welcome {
            margin-top: 200px;
        }

    </style>
</head>
<body>
    <div class="welcome">
        <div>
            <h1>Buna, <b><?php echo htmlspecialchars($_SESSION["user_type"]); ?></b>. Bine ai venit.</h1>
        </div>
        <p>
            <a href="logout.php">Sign Out of Your Account</a>
        </p>
    </div>
    <?php include 'index.php';?>
</body>
</html>
