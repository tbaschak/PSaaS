<?php
require_once '../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;


$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

$channel->queue_declare('nmap', false, true, false, false);


$callback = function($msg) {
	include("../inc/db.inc.php");

	// Create connection
	$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
	// Check connection
	if (!$conn) {
		die("Connection failed: " . mysqli_connect_error());
	}

	$sql = sprintf("SELECT id,scanid,scanoptions FROM `scans` WHERE scanid='%s'", $msg->body);
	$result = mysqli_query($conn, $sql);
	if (mysqli_num_rows($result) > 0) {
		// do stuff with data from each row
		while($row = mysqli_fetch_assoc($result)) {
			$id = $row['id'];
			$scanoutput = '';

			// update started time
			$sql = sprintf("UPDATE `scans` SET `started`=NOW() WHERE `id`=%d", $id);
			mysqli_query($conn, $sql);

			// run scan and store results
			printf("%s - %s: running sudo %s\n", date('r'), $row['scanid'], $row['scanoptions']);
			$fp=popen('sudo ' . $row['scanoptions'], 'r');
			while(!feof($fp)) {
				$buffer = fgets($fp, 4096);
				$scanoutput .= $buffer;
			}
			pclose($fp);
			//print $scanoutput;

			/* create a prepared statement */
			if ($stmt = mysqli_prepare($conn, "UPDATE `scans` SET `results`=? WHERE `id`=?")) {

				/* bind parameters for markers */
				mysqli_stmt_bind_param($stmt, 'sd', $scanoutput, $id);

				/* execute query */
				$status = mysqli_stmt_execute($stmt);
				/* BK: always check whether the execute() succeeded */
				if ($status === false) {
					// shit went bad, don't update finished b/c write didn't happen
				}
				$affected_rows =  mysqli_stmt_affected_rows($stmt);
				/* close statement */
				mysqli_stmt_close($stmt);
			}

			// update finished time
			$sql = sprintf("UPDATE `scans` SET `finished`=NOW() WHERE `id`=%d", $id);
			mysqli_query($conn, $sql);
			$msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
			printf("%s - %s: finished\n", date('r'), $row['scanid']);
		}
	}
	mysqli_close($conn);
};

$channel->basic_consume('nmap', '', false, false, false, false, $callback);

while(count($channel->callbacks)) {
		    $channel->wait();
}

$channel->close();
$connection->close();


//end if

?>
