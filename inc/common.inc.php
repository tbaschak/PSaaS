<?php

// returns single result
function mysql_one_data($query, $connection)
{
	$one=mysqli_query($connection, $query);
	$r=mysqli_fetch_row($one);
	return($r[0]);
}

// returns single array
function mysql_one_array($query, $connection)
{
	$one=mysqli_query($connection, $query) or die (mysql_error());
	$r=mysqli_fetch_array($one);
	return($r);
}



?>
