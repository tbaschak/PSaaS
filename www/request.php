<!DOCTYPE html>
<html lang="en">
	<head>
		<title>PSaaS</title>
		<meta charset="utf-8">

		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
		<!-- Optional theme -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

		<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
			<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->

	</head>

	<body>

		<div class="container">
			<h1>Port Scan as a Service</h1>

<?php

require_once '../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;


// error check data before doing anything with it

if(!isset($_POST['ports'])) {
	$error = 1;
	$errormsg .= "Ports to scan needs to be selected.\n";
} elseif ($_POST['ports'] == 'top50') {
	$ports = 'top50';
} elseif ($_POST['ports'] == 'top100') {
	$ports = 'top100';
} elseif ($_POST['ports'] == 'custom' && isset($_POST['customports'])) {
	if(preg_match('/[^0-9TU,-:]/', $_POST['customports']) == TRUE) {
		$error = 1;
		$errormsg .= "Invalid custom ports.\n";
	} else {
		$ports = 'custom';
		$customports = $_POST['customports'];
	}
} else {
	$error = 1;
	$errormsg .= "Impossible ports.\n";
}

if($_POST['tcp'] == 'on' && $_POST['udp'] == 'on') {
	$tcp = TRUE;
	$udp = TRUE;
} elseif($_POST['tcp'] == 'on') {
	$tcp = TRUE;
	$udp = FALSE;
} elseif($_POST['udp'] == 'on') {
	$tcp = FALSE;
	$udp = TRUE;
} else {
	$error = 1;
	$errormsg .= "TCP or UDP needs to be selected.\n";
}

if(!isset($_POST['speed'])) {
	$error = 1;
	$errormsg .= "Speed to scan needs to be selected.\n";
} elseif ($_POST['speed'] == 'slow') {
	$speed = 1;
} elseif ($_POST['speed'] == 'medium') {
	$speed = 3;
} elseif ($_POST['speed'] == 'fast') {
	$speed = 5;
} else {
	$error = 1;
	$errormsg .= "Impossible speed selected.\n";
}

if($_POST['extrainfo'] == 'on') {
	$extrainfo = TRUE;
} elseif (!isset($_POST['extrainfo'])) {
	$extrainfo = FALSE;
} else {
	$error = 1;
	$errormsg .= "Impossible info selected.\n";
}

if($_POST['authorized'] == 'on') {
	$authorized = TRUE;
} else {
	$error = 1;
	$errormsg .= "Not Authorized.\n";
}

if($error == 1) {
	print "<h2>Form Data Problems</h2>" . "\n";
	print "<pre>" . $errormsg . "</pre>" . "\n";
	print "<p><a href='javascript:history.go(-1)'>[Go Back]</a></p>" . "\n";
} else {

	// checks passed, do something with the data


	$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
	$channel = $connection->channel();

	// durable = #3 param
	$channel->queue_declare('nmap', false, true, false, false);

	include("../inc/db.inc.php");

	// Create connection
	$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
	// Check connection
	if (!$conn) {
		die("Connection failed: " . mysqli_connect_error());
	}

	$uniqid = uniqid('', TRUE);
	$nmapoptions = '-Pn';
	$target = $_SERVER['REMOTE_ADDR'];

	// build nmap options

	// ports
	if($ports == 'top50') {
		$nmapoptions .= ' --top-ports=50 ';
	} elseif($ports == 'top100') {
		$nmapoptions .= ' --top-ports=100 ';
	} elseif($ports == 'custom') {
		$nmapoptions .= ' -p ' . $customports . ' ';
	}

	// scan speed
	$nmapoptions .= ' -T' . $speed . ' ';

	// tcp, udp
	if($tcp == TRUE && $udp == TRUE) {
		$nmapoptions .= ' -sTUV ';
	} elseif($tcp == TRUE && $udp == FALSE) {
		$nmapoptions .= ' -sTV ';
	} elseif($tcp == FALSE && $udp == TRUE) {
		$nmapoptions .= ' -sUV ';
	}

	// extra info (-A)
	if($extrainfo == TRUE) {
		$nmapoptions .= ' -A ';
	}

	$sql = sprintf("INSERT INTO `scans` VALUES (NULL, '%s', '%s', '%s', now(), '0000-00-00 00:00:00', '0000-00-00 00:00:00', '')", $uniqid, $target, "nmap $nmapoptions $target");
	mysqli_query($conn, $sql);

	printf("<p>Scan submitted.</p><p><a href='/scan/%s'>Look at this scan...</a></p>\n", $uniqid);

	printf("<script language='javascript'>window.location.replace('/scan/%s');</script>", $uniqid);

	$msg = new AMQPMessage($uniqid);
	$channel->basic_publish($msg, '', 'nmap');
	$channel->close();
	$connection->close();

	mysqli_close($conn);

	//end if
}

?>
		</div>


		<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
		<!-- Latest compiled and minified JavaScript -->
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

	</body>
</html>
