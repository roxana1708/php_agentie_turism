<?php 
	include 'functions.php';
    //include 'db_connections.php';
	session_start();

	if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
        header("location: login.php");
        exit;
    }

    $id_client = $_SESSION["id"];
    $facuta_de = $_SESSION["id"];

    $link = db_Connect();


    //verif nr oaspeti
    if (isset($_POST['nr_pers'])) {
        $nr_pers = filter_var($_POST['nr_pers'], FILTER_SANITIZE_STRING);
        verify_nr_pers(/*$_SESSION['err_msg']['nr_pers_err'],*/$_SESSION['user_type'], $nr_pers);
        if(!empty($_SESSION['err_msg']['nr_pers_err'])) {
            header('location: rezervare_hotel.php');
            exit;
        }
    } else {
            $_SESSION['err_msg']['nr_pers_err'] = "Please select a valid value";
            header('location: rezervare_hotel.php');
                exit;
    }

    echo $nr_pers;
    //verif destinatie
    $oras = '';

    if(isset($_POST['destinatie'])) {
        if(verify_destinatie($_POST['destinatie'])) {
        	$oras = $_POST['destinatie'];
        } else {
        	$_SESSION['err_msg']['departure_err'] = "Va rugam alegeti un oras valid";
        	header('location: rezervare_hotel.php');
            exit;
        }
    } else {
        $_SESSION['err_msg']['departure_err'] = "Va rugam alegeti un oras valid";
            header('location: rezervare_hotel.php');
            exit;
    }

    
    echo $oras;

    //verif date calendaristice
    $data_plecare = $data_intoarcere = '';
   
    if(isset($_POST['data_plecare']) && isset($_POST['data_intoarcere'])) {
        verify_dates($_POST['data_plecare'], $_POST['data_intoarcere'], $data_plecare, $data_intoarcere/*, $_SESSION['err_msg']['date_err']*/);

        if(!empty($_SESSION['err_msg']['date_err'])) {
            header('location: rezervare_hotel.php');
            exit;
        }
    } else {
        $_SESSION['err_msg']['date_err'] = "Va rugam alegi date calendaristice valide";
            header('location: rezervare_hotel.php');
            exit;
    }

    echo "as;dlfha;vsdnka;sbn;dshg";
    echo $data_plecare . $data_intoarcere;

    //verif nr stele hotel
    if (isset($_POST['nr_stele'])) {
        $nr_stele = filter_var($_POST['nr_stele'], FILTER_SANITIZE_STRING);
        if($nr_stele != 3 && $nr_stele != 5) {
            $_SESSION['err_msg']['nr_stele_err'] = "Please select a valid value";
            header('location: rezervare_hotel.php');
            exit;
        }
    } else {
            $_SESSION['err_msg']['nr_stele_err'] = "Please select a valid value";
            header('location: rezervare_hotel.php');
                exit;
    }

    //verif daca clientul a mai facut rzv pt acelasi hotel in aceeasi perioada
    //echo "\n\n\nzzzzzzz";
    //echo $oras . $data_plecare . $data_intoarcere . $id_client . $nr_stele;
    if(verify_rzv_hotel($oras, $data_plecare, $data_intoarcere, $id_client, $nr_stele)) {
        //
    } else {
        $_SESSION['err_msg']['duplicate_err'] = "Ati mai facut rezervare pentru acest hotel in aceasta perioada";
            header('location: rezervare_hotel.php');
            exit;
    }
    //echo "aaa";

    //verif nr max oaspeti hotel in acea perioada
    //echo $oras . $data_plecare . $data_intoarcere . $nr_pers . $nr_stele. "a a a a ";
    if(verify_availability_hotel($oras, $data_plecare, $data_intoarcere, $nr_pers, $nr_stele)) {
        //
    } else {
        
        $_SESSION['err_msg']['availability_err'] = "Nu mai sunt suficiente locuri disponibile pentru acest hotel in perioada aleasa";
            header('location: rezervare_hotel.php');
            exit;
        
    }

    //facem rezervarea
    $id_rezervare = db_insert_rezervare($facuta_de, 'h', $nr_pers, $id_client);
    //echo $id_rezervare;
    db_insert_sejur($id_rezervare, $data_plecare, $data_intoarcere, $oras, $nr_stele);




    $desc = " " . $oras . " Hotel " . $nr_stele . "  " . $data_plecare . "  -  " . $data_intoarcere . " ";

    
    $d = date("Y/m/d");
    //echo "kkkkkkkk";
    $type = "Sejur Hotel";

    $europa = ["Stockholm", "Hanovra", "Roma", "Geneva", "Londra", "Oslo", "Copenhaga", "Budapesta", "Viena", "Atena", "Amsterdam", "Lyon", "Luxemburg"];
    $romania = ["Bucuresti", "Cluj", "Brasov", "Iasi", "Timisoara"];
    $america = ["New York"];

    if(in_array($oras, $europa)) {
        if($nr_stele == 3){
            $price_per_pers = 80;
        } else {
            $price_per_pers = 120;
        }
        
    } elseif (in_array($oras, $america)) {
        if($nr_stele == 3){
            $price_per_pers = 100;
        } else {
            $price_per_pers = 200;
        }
    } elseif (in_array($oras, $romania)) {
        if($nr_stele == 3){
            $price_per_pers = 30;
        } else {
            $price_per_pers = 90;
        }
    } 

    make_invoice($id_client, $d, $id_rezervare, $type, $desc, $nr_pers, $price_per_pers);
?>