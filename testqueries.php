<?php

function testQueries($queryId, $query){

	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "fsearch";
	
	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}

	$words = str_word_count($query,1);
	
	$sql = "SELECT collection, SUM(documents) as docs FROM `indexer`";
	
	$params = "";
	
	foreach ($words as $t){
		if(strcmp($params, '') == 0)
			$params = ' WHERE words = "'.$t.'"';
		else
			$params = $params . ' or words = "'.$t.'"';
	}
	
	$sql = $sql.$params." GROUP BY collection;";
	
	//echo $sql;
	
	$retval = mysqli_query($conn, $sql);
	if (! $retval )
	{
		echo $sql;
		die('Could not get data: ' . mysql_error());
	}
	
	$file = fopen('fedweb13.qrels.txt', 'a');
	
	//$file = 'fedweb13.qrels.txt';
	
	while($row = mysqli_fetch_assoc($retval))
	{		
		$newRow = $queryId.' 0 FW13-'.$row['collection'].' '.$row['docs'];
		
		//file_put_contents($file, $newRow . "\n");
		
		fwrite($file, $newRow . "\n");
	}
	
	fclose($file);
	
	$conn->close();
}

function myQueries(){
	
	$handle = @fopen("fedweb13.queries.txt", "r");
	if ($handle) {
		while (($buffer = fgets($handle, 4096)) !== false) {
			
			$myQuery = substr($buffer, 5, strlen(trim($buffer)) - 5);
			$myQueryId = substr($buffer, 0, 4);
			testQueries($myQueryId, $myQuery);
		}
		if (!feof($handle)) {
			echo "Error: unexpected fgets() fail\n";
		}
		fclose($handle);
	}
}

?>
