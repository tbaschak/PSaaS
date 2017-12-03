<?php

include("../inc/db.inc.php");

$scanid = $_GET['scanid'];
$finished = 0;

// Create connection
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
// Check connection
if (!$conn) {
	die("Connection failed: " . mysqli_connect_error());
}

$count = mysql_one_data(sprintf("SELECT count(id) AS count FROM `scans` WHERE scanid='%s'", $scanid), $conn);

if($count > 0) {
	$results = mysql_one_array(sprintf("SELECT * FROM `scans` WHERE scanid = '%s'", $scanid ), $conn );
	$output = sprintf("<h2>ScanID</h2><p>%s</p>\n", $scanid);
	$output .= sprintf("<h2>Target</h2><p>%s</p>\n", $results['target']);
	$output .= sprintf("<h3>NMAP Command</h3><p>%s</p>\n", $results['scanoptions']);
	$output .= sprintf("<h3>Submitted</h3><p>%s</p>\n", $results['submitted']);
	if($results['started'] != '0000-00-00 00:00:00') {
		$output .= sprintf("<h3>Started</h3><p>%s</p>\n", $results['started']);
		if($results['finished'] != '0000-00-00 00:00:00') {
			$finished = 1;
			$output .= sprintf("<h3>Finished</h3><p>%s</p>\n", $results['finished']);
			$output .= sprintf("<h3>Results</h3><pre>%s</pre>\n", $results['results']);
		} else {
			$output .= "<h2>Scan still running...</h2><p>This page will reload automatically</p>\n";
		}
	} else {
		$output .= "<h2>Scan not yet started...</h2><p>This page will reload automatically</p>\n";
	}
} else {
	$output = "<h3>ScanID Not Found</h3>";
}

mysqli_close($conn);

//end if

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>PSaaS - Scan Results</title>
		<meta charset="utf-8">
<?php
// if started but not finished yet, refresh again in 30
if($finished == 0) {
	print '<meta http-equiv="refresh" content="30">';
}
?>
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

		<ul class="breadcrumb">
			<li><a href="/">Port Scan as a Service</a></li>
			<li class="active"><?php echo $scanid; ?></li>
		</ul>

		<div class="container">
<?php echo $output; ?>
		</div>

		<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
		<!-- Latest compiled and minified JavaScript -->
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

	</body>
</html>
