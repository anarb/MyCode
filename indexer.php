<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html lang="en">
<head>
    <title>Search engine</title>
</head>
<body>

<?php
 
	/*$servername = "mysql8.000webhost.com";
	$username = "a4030143_anar";
	$password = "anar0820";
	$dbname = "a4030143_game";*/
	
	//set_time_limit(60);
	
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

	$filename = "sample.tgz";
	//$filename = "http://www.ux.uis.no/~bilegt/sample.tgz";
	
	$zd = gzopen($filename, "r", 2);
	
	//$fp = fopen("compress.zlib://http://www.ux.uis.no/~bilegt/sample.tgz", "r");
	
	$arrTitle = array();
	$arrDescr = array();
	$sumWord_frequencies = array();
	
	$oldcollectionId = '';
	$collectionId = '';
	$k = 0;
	
	while (!gzeof($zd)) {
				
		//$contents = fgets($fp, 80000); //fread($fp, 80000)
		
		$contents = gzread($zd, 80000);
			
		$contents = strtolower($contents);
		$contents = preg_replace("/[\\n\\r]+/", ' ', $contents);
		
		
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
							$valArr = split (",", $value);
							$sql = "INSERT INTO indexer (collection, words, frequency, documents) VALUES ('".$oldcollectionId."', '".$key."', '".$valArr[0]."', '".$valArr[1]."');";
														
							if ($k == 0)
								echo $sql;
							if ($conn->query($sql) !== TRUE) {
								echo "Query execution is failed!";
							} 
							
							$k++;
						}
					}
				}
			
				$sumWord_frequencies = array();
			
				$oldcollectionId = $collectionId;
			}
						
			if (isset($sumWord_frequencies[$collectionId])) {
				foreach ($word_frequencies as $key=>&$value) {
					if (isset($sumWord_frequencies[$collectionId][$key])) {
						
						$values = $sumWord_frequencies[$collectionId][$key];						
						$valArr = split (",", $values);
												
						//echo $key." => ".$values." + ".$value;						
						
						$valArr[0] = intval($valArr[0]) + $value;
						if ($value > 0)
							$valArr[1] = intval($valArr[1]) + 1;							
						
						$sumWord_frequencies[$collectionId][$key] = $valArr[0].",".$valArr[1];
												
					} else {
						try {							
							$sumWord_frequencies[$collectionId][$key] =  "$value,1";							
						} catch (Exception $e) {
							echo $e;
						}						
					}
				}
			} else {
				foreach ($word_frequencies as $key=>&$value) {
					$sumWord_frequencies[$collectionId][$key] =  "$value,1";				
				}
			}			
		}		
	}
	
	foreach ($sumWord_frequencies as $key1=>&$value1) {
			
		foreach ($value1 as $key=>&$value) {
			if (strlen($key) < 3) {
				unset($value1[$key]);
			} else {
											
				$valArr = split (",", $value);
				$sql = "INSERT INTO indexer (collection, words, frequency, documents) VALUES ('".$oldcollectionId."', '".$key."', '".$valArr[0]."', '".$valArr[1]."');";
				
				if ($conn->query($sql) !== TRUE) {
					echo "Query execution is failed!";
				}
			}
		}
	}
	
	$conn->close();
		
	gzclose($zd);
	
?>
</body>
</html>