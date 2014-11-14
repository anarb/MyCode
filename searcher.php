<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Federated search engine | Vertical search</title>

<link rel="stylesheet" type="text/css" href="styles.css" />

</head>

<body>

<div id="page">

    <h1>Federated search engine</h1>
    
    <form id="searchForm" method="post" action="searcher.php">
		<fieldset>
        
           	<input id="s" name="query" type="text" />
            
            <input type="submit" value="Submit" name="Submit1" id="submitButton" />
            <input type="submit" value="Test queries" name="Submit2" id="submitButton" />
            <input type="submit" value="Baseline" name="Submit3" id="submitButton" />
                               
            <ul class="icons">
                <li class="web" title="Web Search" data-searchType="web">Web</li>
                <li class="images" title="Image Search" data-searchType="images">Images</li>
                <li class="news" title="News Search" data-searchType="news">News</li>
                <li class="videos" title="Video Search" data-searchType="video">Videos</li>
            </ul>
            
        </fieldset>
    </form>

    <div id="resultsDiv">
    
	    <pre>
			<?php 
			
				include("cori.php");
				include("testqueries.php");
				include("baseline.php");
			
				if (isset($_POST['Submit1'])) {
				
					$query = $_POST['query'];
					
					cori($query);	
				}
				
				if (isset($_POST['Submit2'])) {
				
					myQueries();
				}
				
				if (isset($_POST['Submit3'])) {
				
					baseline();
				}
			
			?>
		</pre>    
    
    </div>
    
</div>

</body>
</html>
