
<?php

function cori($query){
	
	$servername = "localhost";
	$username = "user2691071";
	$password = "anar0820";
	$dbname = "db2691071-main";
	
	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	
	$words = str_word_count($query,1);
	$Nc = 199;
	$sCollections = array();
	
	echo '<h2>All beliefs:</h2>';
	
	foreach ($words as $t){
		
		$sql = "SELECT words, count( collection ) FROM `indexer` WHERE words = '$t' GROUP BY words;";
		$sql1 = "SELECT * FROM indexer WHERE words = '$t';";
		$sql2 = "SELECT collection, count(words) as numWords FROM `indexer` GROUP BY collection";

		$retval = mysqli_query($conn, $sql2);
		if (! $retval )
		{
			die('Could not get data: ' . mysql_error());
		}
		
		$collections = array();
		$docs = array();
		
		$totalWords = 0;
		
		while($row = mysqli_fetch_assoc($retval))
		{
			$collections[$row['collection']] = $row['numWords'];
			$totalWords += intval($row['numWords']);
		}
		
		$avg_cw = $totalWords/count($collections);
				
		$retval = mysqli_query($conn, $sql1);
		
		if (! $retval )
		{
			die('Could not get data: ' . mysql_error());
		}
		
		while($row = mysqli_fetch_assoc($retval))
		{
			$docs[$row['collection']] = $row['documents'];
		}
		
		$beliefs = array();
		
		//echo $t.":".count($docs);
		
		if (count($docs) !== 0){
		
			foreach ($collections as $cName => $nWords){
				
				$T = 0;
				
				if(isset($docs[$cName])){
					$dfti = intval($docs[$cName]);
					$cwi = $nWords;
					
					$T = $dfti / ($dfti + 50 + 150 * $cwi/$avg_cw);
					
				} else 
					$T = 0;
				
				$cft = count($docs);
				
				$I = log( ($Nc+0.5) / $cft ) / log( $Nc + 1.0 );
				
				$b = 0.4;
				
				$P = $b + ( 1 - $b ) * $T * $I; // P(t|Ci) => belief
				$beliefs[$cName] = $P;
			}
			
			print_r($beliefs);
			
			$bestColls = array(); // selected 5 collections
			
			for ($i = 0; $i<5; $i++){
				
				$maxVal = max($beliefs); // taking max result from the list of results
				
				foreach ($beliefs as $cName1 => $P1){
					
					if ($maxVal === $P1){ // searching chosen max result to delete it
						
						$bestColls[$cName1] = $P1; // collecting max results into an array
						unset($beliefs[$cName1]); // removing chosen max result to choose second max result
						
						break; // exit from foreach loop
					}
				}			
			}
			//print_r($bestColls);
			
			$sCollections[$t] = $bestColls; // array of selected collections for each words
		}
	}
	echo '<br /><br /><h2>Chosen 5 collections for each word:</h2>';
	print_r($sCollections);
}

?>
