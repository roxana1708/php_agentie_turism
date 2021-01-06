<?php
	include 'db_connections.php';
	
	session_start();

	if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
        header("location: login.php");
        exit;
    }


	function check_min_max($value, $min_, $max_) {
		if($value >= $min_ && $value <= $max_ && ((float)($value) - (int)($value) == 0)) {
			return true;
		} else {
			return false;
		}

	}
	
	function verify_nr_pers_client($user_type, $nr_pers) {
		if($user_type == 'org') {
			if(!check_min_max($nr_pers, 5, 40)) {
	            return "Please select a valid value from 5 to 40";
	        }
		} else {
			if(!check_min_max($nr_pers, 1, 4)) {
	            return "Please select a valid value from 1 to 4";
	        }
	    }
	}

	function verify_nr_pers($tip_user, $nr_pers) {//$_SESSION['err_msg']['nr_pers_err'],
		$_SESSION['err_msg']['nr_pers_err'] = verify_nr_pers_client($tip_user, $nr_pers);
	}


	function verify_destinatie($dest) {
		/*
		$link = db_Connect();
		$query = "SELECT nume_destinatie FROM Destinatii"; 
    	$result = $link->query($query);
    	//$ok = 0;
		*/
		$result = show_dest_list();
    	while($row = $result->fetch_assoc()) {
            if($row['nume_destinatie'] == $dest) {
                //$ok = 1;
                //break;
                return 1;
            }
        }

        return 0;
	}

	function verify_orase($plecare_post, $destinatie_post, &$plecare, &$destinatie){//, $_SESSION['err_msg']['departure_err'], $_SESSION['err_msg']['arrival_err']) {
		if($plecare_post == $destinatie_post) {
            $_SESSION['err_msg']['departure_err'] = "Alegeti orase diferite.";
            $_SESSION['err_msg']['arrival_err'] = "Alegeti orase diferite.";
        } else {
            if(verify_destinatie($plecare_post) == 0) {
                $_SESSION['err_msg']['departure_err'] = "Please select a valid destination1";
                //header('location: rezerva.php');
                    //exit;
            } else if (verify_destinatie($destinatie_post) == 0) {
                $_SESSION['err_msg']['arrival_err'] = "Please select a valid destination2";
                //header('location: rezerva.php');
                    //exit;
            } else {
            	$plecare = $plecare_post;
            	$destinatie = $destinatie_post;
            }
        }
	}

	function verify_date($data_) {
		//$date_ = new DateTime($data_);
		$now = new DateTime();
		if($data_ < $now) {
			return false;
		} else {
			return true;
		}
	}

	function verify_dates($data_plecare_post, $data_intoarcere_post, &$data_plecare, &$data_intoarcere) {//, $_SESSION['err_msg']['date_err']
	        if($data_plecare_post > $data_intoarcere_post) {
	            $_SESSION['err_msg']['date_err'] = "Va rugam alegi date calendaristice valide";
	            //header('location: rezerva.php');
	            //exit;
	        } else {
	        	$date1 = new DateTime($data_plecare_post);
	        	$date2 = new DateTime($data_intoarcere_post);

	        	if (!verify_date($date1) or !verify_date($date2)) {
	        		$_SESSION['err_msg']['date_err'] = "Va rugam alegi date calendaristice valide    a";
	            	//header('location: rezerva.php');
	                //exit;
	        	} else {
	                $data_plecare = $data_plecare_post;
	                $data_intoarcere = $data_intoarcere_post;
	            }
	        }
	}

	function verify_username_client($username, &$id_client, $user_type, &$client_user_type) {
		$link = db_Connect();

		$query = "SELECT username, user_type, user_id FROM Users where username = '$username'"; 
	    $result = $link->query($query);



	    if($result->num_rows == 1){
	    	$row = $result->fetch_assoc();
	    	if($user_type == 'admin' or ($row['user_type'] != 'admin' and $row['user_type'] != 'angajat')) {
	    		$id_client = $row['user_id'];
	    		$client_user_type = $row['user_type'];
	    	} else {
	    		$_SESSION['err_msg']['username_client_err'] = "Nu ai drepturi sa accesezi acest client";
	    	}

	    } else {
	    	$_SESSION['err_msg']['username_client_err'] = "Acest username nu exista";

	    }
	}
	
?>