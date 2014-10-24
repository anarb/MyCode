<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html lang="en">
<head>
    <title>Search engine</title>
</head>
<body>

<?php

 
	$servername = "mysql8.000webhost.com";
	$username = "a4030143_anar";
	$password = "anar0820";
	$dbname = "a4030143_game";
	
	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($conn->connect_error) {
	    die("Connection failed: " . $conn->connect_error);
	}	

	$filename = "search.tar.gz";
	//$filename = "http://www.ux.uis.no/~bilegt/sample.tgz";
	
	$zd = gzopen($filename, "r", 2);
	
	$arrTitle = array();
	$arrDescr = array();
	$sumWord_frequencies = array();
	$sumDocWord_freq = array();
	
	$oldcollectionId = '';
	$k = 0;
	//for ($i = 0; $i < 1; $i++){
	while (!gzeof($zd)) {
		
		$contents = gzread($zd, 80000);
			
		$contents = strtolower($contents);
		$contents = preg_replace("/[\\n\\r\\t]+/", ' ', $contents);
		
		
		preg_match_all('@<snippet [^>]*?>.*?</snippet>@siu', $contents, $snippets);
		
		foreach ($snippets[0] as $snippet) {
								
			$collectionId = substr($snippet, 18, 4);
						
			preg_match_all('#<title>.*</title>#', $snippet, $arrTitle);
			
			$find = array("<title>","</title>", "Title:");
								
			$words1 = str_word_count(implode(' ', str_replace($find,"",$arrTitle[0])),1);
			
			
			
			preg_match_all('/<description>(.*?)<\\/description>/', $snippet, $arrDescr);
			
			$find = array("<description>","</description>");
			
			$words2 = str_word_count(implode(' ', str_replace($find,"",$arrDescr[0])),1);
			
			
			$sumWords = array_merge($words1,$words2);
			
			$word_frequencies = array_count_values($sumWords);
			
			if (strcmp($oldcollectionId, $collectionId) !== 0) {
				
				foreach ($sumWord_frequencies as $key1=>&$value1) {
				
				
					foreach ($value1 as $key=>&$value) {
						if (strlen($key) < 3) {
							unset($value1[$key]);
						} else {
														
							$sql = "INSERT INTO indexer (words, ".$oldcollectionId.") VALUES ('".$key."', '".$value[0].",".$value[1]."') ON DUPLICATE KEY UPDATE ".$oldcollectionId." = '".$value[0].",".$value[1]."';";
														
							if ($k == 0)
								echo $sql;
							if ($conn->query($sql) === TRUE) {
								//echo "New record created successfully";
							} 
							
							$k++;
						}
					}
									
				}
			
				$sumWord_frequencies = array();
			
				$oldcollectionId = $collectionId;
			}
						
			if (isset($sumWord_frequencies[$collectionId]) && isset($sumDocWord_freq[$collectionId])) {
				foreach ($word_frequencies as $key=>&$value) {
					if (isset($sumWord_frequencies[$collectionId][$key])) {
						
						$values = $sumWord_frequencies[$collectionId][$key];						
						$valArr = split (",", $values);
						
						$valArr[0] = intval($valArr[0]) + $value;
						if ($value > 0)
							$valArr[1] = intval($valArr[1]) + 1;
							
						
						$sumWord_frequencies[$collectionId][$key] = $valArr[0].",".$valArr[1];//$sumWord_frequencies[$collectionId][$key] + $value;
						
						if ($valArr[1] == ",")
							echo $sumWord_frequencies[$collectionId][$key];
					} else {
						try {							
							$sumWord_frequencies[$collectionId][$key] =  "$value,1";							
						} catch (Exception $e) {
							echo $e;
						}
						
					}
				}
			} else {
				$sumWord_frequencies[$collectionId] = $word_frequencies;
				$sumDocWord_freq[$collectionId] = $word_frequencies;
			}
			
		}
		
	}
	
	foreach ($sumWord_frequencies as $key1=>&$value1) {
	
	
		foreach ($value1 as $key=>&$value) {
			if (strlen($key) < 3) {
				unset($value1[$key]);
			} else {
					
				$sql = "INSERT INTO indexer (words, ".$oldcollectionId.") VALUES ('".$key."', '".$value[0].",".$value[1]."') ON DUPLICATE KEY UPDATE ".$oldcollectionId." = '".$value[0].",".$value[1]."';";
	
				if ($k == 0)
					echo $sql;
				if ($conn->query($sql) === TRUE) {
					//echo "New record created successfully";
				}
					
				$k++;
			}
		}
			
	}
	
	
	$conn->close();
		
	gzclose($zd);
	
?>
</body>
</html>