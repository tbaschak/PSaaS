<?php

include("../inc/db.inc.php");

// Create connection
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
// Check connection
if (!$conn) {
	die("Connection failed: " . mysqli_connect_error());
}

$sql = "SELECT scanid,target,scanoptions,submitted,started,finished from `scans`";

$result = mysqli_query($conn, $sql);

$output = "<table class='table table-hover'>\n";

$output .= "<thead><tr><th>ScanID</th><th>Target IP</th><th>Added</th><th>Started</th><th>Finished</th></tr></thead>\n";

$output .= "<tbody>\n";

if (mysqli_num_rows($result) > 0) {
	// output data of each row
	while($row = mysqli_fetch_assoc($result)) {
		$output .= sprintf("<tr><td><a href='/scan/%s'>%s</a></td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>\n", $row['scanid'], $row['scanid'], $row['target'], $row['submitted'], $row['started'], $row['finished'] );
	}
} else {
	$output .= "<tr><td colspan=5>0 results</td></tr>\n";
}

$output .= "</tbody>\n";
$output .= "</table>\n";

mysqli_close($conn);

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>PSaaS - Scan Results</title>
		<meta charset="utf-8">
		<meta http-equiv="refresh" content="90">
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
			<li class="active">List of Scans</li>
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
