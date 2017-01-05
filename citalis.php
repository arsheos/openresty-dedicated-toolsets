<?php

if (!empty($_PUT) or !empty($_DELETE)) exit();

function addQuotes($str) { return '"' . $str . '"'; }

function stripSingleQuote($str) { 
	$str = trim($str); 
	if (substr($str, -1) == "'") $str = substr($str, 0, -2); 
	if (substr($str, 0) == "'") $str = substr($str, 1, -1); 
	return $str; 
}

function vignette_template ($tparam1, $tparam2) {
	$random_a = rand(1, 8); $random_b = rand(1, 100) + 270;
  return "<article class=" . addQuotes("item thumb") . " data-width=" . addQuotes($random_b) . " data-value=" . addQuotes(str_replace('"', "'", $tparam2) . "</br >" . 
  	$tparam1) . "><div class=" . addQuotes("citation") . " style=" . addQuotes("background-image: url(images/thumbs/0" . $random_a . ".jpg)") . ">" . 
  	str_replace('"', "'", $tparam2) . "</div><h7><a href=" . addQuotes("#") . " onclick=" . addQuotes("javascript:$('.image').click();") . " class=" .  
  	addQuotes("icon fa-arrows-alt") . "><span class=" . addQuotes("label") . ">Detail</span></a></h7>" . "<h2><input class=" . addQuotes("ccategories") . 
  	" type=" . addQuotes("submit") . " name=" . addQuotes("search") . " value=" . addQuotes($tparam1) . "></h2><a href=" . addQuotes("images/fulls/0" . $random_a . ".jpg") . 
  	" class=" . addQuotes("image") . " alt=" . addQuotes("") . "><img src=" . addQuotes("images/thumbs/0" . $random_a . ".jpg") . " alt=" . addQuotes("") . "></a></article>";
}

$activeDB = 0; // no official HHVM PostgreSQL PDO support //

if ($activeDB == 1) {
    $db = new PDO('pgsql:host=localhost;dbname=citalis;client_encoding=UTF8', 'plbc', 'plbc');
} else { $db = new PDO('mysql:host=localhost;dbname=citalis', 'root', 'plbc'); }
$db->setAttribute(PDO::ATTR_TIMEOUT, 300);
$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
if ($activeDB != 1) { $db->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, 'SET NAMES UTF8'); }
//$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

//$redis = new Redis();
//$redis->connect('127.0.0.1', 6379);

$numrows = 0; $response = ""; // requis par PHP7

if (!empty($_POST)) 	{

	$headers = $_POST; $postIn = "method=_POST";
	while (list($header, $value) = each($headers)) { $postIn .= "&$header=$value"; }
	
	if ($postIn == "method=_POST&jsonadd=Ajouter des enregistrements de test") {
	
		if (isset($_FILES['filebox']))	{
		 
		  $file_name = $_FILES['filebox']['name']; $file_size =$_FILES['filebox']['size'];
		  $file_tmp =$_FILES['filebox']['tmp_name']; $file_type=$_FILES['filebox']['type'];
		  $file_ext=strtolower(end(explode('.',$_FILES['filebox']['name'])));
		  
		  if (strlen($file_name) == 0) $retour = ' Fichier non sélectionné.';
		  else if ($file_ext != "json") $retour = "Extension non autorisée. Merci de sélectionner un ficher JSON.";
		  else if ($file_size > 2097152) $retour = 'Le fichier ne doit pas dépasser 2 Mo';
		  else if ($file_size == 0) $retour = 'Fichier vide.';
		  
		  if (strlen($retour) == 0) { 
		     
		  	move_uploaded_file($file_tmp, "jsonfiles/".$file_name); 
		  	$jsondata = file_get_contents($file_tmp, $use_include_path = true);
		  	$array = json_decode($jsondata, true); $firstLineKeys = false;
		  	
			foreach ($array as $record) {
				$response .= "(";
				$numcol = 0;
		  		foreach ($record as $col) {
		  			$numcol = $numcol + 1;
		  			if ($numcol > 1) $response .= "'" . utf8_decode($col) . "', ";
		   		}
		  		$response .= " '100     test'),";
		  		$numrows = $numrows + 1;
		  	}
	   		
			$req = $db->query("INSERT INTO citations (titre, citation, auteur, comment, concept) VALUES " . substr(str_replace("\n\n", " ", $response), 0, -1));
			$req->closeCursor();
						
		  	$dbresponse = vignette_template("...", "<p align='center'>" . 
		  		utf8_decode($numrows . " enregistrements viennent d'être ajoutés à la base Citalis avec succès.") . "</p>") . "\r\n";
		  	
		  } else $dbresponse = vignette_template("...", "<p align='center'>" . utf8_decode($retour) . "</p>") . "\r\n";
 
		} else $dbresponse = vignette_template("...", "<p align='center'>" . utf8_decode("OOps ! Une erreur de connexion est survenue.") . "</p>") . "\r\n";
			
	} else if ($postIn == "method=_POST&jsonrem=Supprimer les enregistrements de test") {
	
		$req = $db->query("DELETE FROM citations WHERE concept = '100     test'");
		$req->closeCursor();
		$dbresponse = vignette_template("...", "<p align='center'>" . utf8_decode("Les enregistrements de test viennent d'être supprimés de la base Citalis.") . "</p>") . "\r\n";

	} else if ($postIn == "method=_POST&concept=extract") {
	
	    /*
	    $preprq = "SELECT DISTINCT concept FROM citations ORDER BY concept";
	    $key = "sql_cache:" . md5($preprq);
	    // If $key exists get and unserialize it, otherwise set $data to empty array
        $dbresponse = $redis->exists($key) 
            ? unserialize($redis->get($key)) 
            : array();
        if (empty($dbresponse)) { 
		    //$req = $db->query($preprq);
		    */
		    
		    $req = $db->query("SELECT DISTINCT concept FROM citations ORDER BY concept");
		    while($row = $req->fetch(PDO::FETCH_ASSOC)) {
			    $numrows = $numrows + 1;
			    if ($activeDB == 1) {
			        $req2 = $db->query("SELECT citation, auteur, titre, date, comment FROM citations WHERE concept = '" . $row['concept'] . "' ORDER BY RANDOM() LIMIT 1"); }
			    else { $req2 = $db->query("SELECT citation, auteur, titre, date, comment FROM citations WHERE concept = " . addQuotes($row['concept']) . " ORDER BY RAND() LIMIT 1"); }
			    while($row2 = $req2->fetch(PDO::FETCH_ASSOC)) 
				    $dbresponse .= vignette_template($row['concept'], $row2['citation'] . "<br />" . $row2['auteur'] . "<br />" . $row2['titre'] . "<br />" . 
					    $row2['date'] . "<br />" . $row2['comment']) . "\r\n";
		    }
		    
		    /*
		    Put data into cache for 1 hour                   
            $redis->set($key, serialize($dbresponse));                  
            $redis->expire($key, 3600);              
            */
            
		    $req2->closeCursor(); $req->closeCursor();
	    //}
		
	} else if ($postIn == "method=_POST&concept=random") {
	
	    if ($activeDB == 1) {
		    $req = $db->query("SELECT concept FROM citations ORDER BY RANDOM() LIMIT 1"); }
		else { $req = $db->query("SELECT concept FROM citations ORDER BY RAND() LIMIT 1"); }
		while($row = $req->fetch(PDO::FETCH_ASSOC)) {
		    if ($activeDB == 1) {
			    $req2 = $db->query("SELECT citation, auteur, titre, date, comment FROM citations WHERE concept = '" . $row['concept'] . "' ORDER BY auteur, titre"); }
			else { $req2 = $db->query("SELECT citation, auteur, titre, date, comment FROM citations WHERE concept = " . addQuotes($row['concept']) . " ORDER BY auteur, titre"); }
			while($row2 = $req2->fetch(PDO::FETCH_ASSOC)) { 
				$numrows = $numrows + 1;
				$dbresponse .= vignette_template($row['concept'], $row2['citation'] . "<br />" . $row2['auteur'] . "<br />" . $row2['titre'] . "<br />" . 
					$row2['date'] . "<br />" . $row2['comment']) . "\r\n";  
				}
		}
		$req2->closeCursor(); $req->closeCursor();
		
	} else if ($postIn == "method=_POST&auteur=random") {
	
	    if ($activeDB == 1) {
		    $req = $db->query("SELECT auteur FROM citations ORDER BY RANDOM() LIMIT 1"); }
		else { $req = $db->query("SELECT auteur FROM citations ORDER BY RAND() LIMIT 1"); }
		while($row = $req->fetch(PDO::FETCH_ASSOC)) {
		    if ($activeDB == 1) {
			    $req2 = $db->query("SELECT citation, auteur, titre, date, comment FROM citations WHERE auteur = '" . $row['auteur'] . "' ORDER BY auteur, titre"); }
			else { $req2 = $db->query("SELECT citation, auteur, titre, date, comment FROM citations WHERE auteur = " . addQuotes($row['auteur']) . " ORDER BY auteur, titre"); }
			while($row2 = $req2->fetch(PDO::FETCH_ASSOC)) { 
				$numrows = $numrows + 1;
				$dbresponse .= vignette_template($row['auteur'], $row2['citation'] . "<br />" . $row2['auteur'] . "<br />" . $row2['titre'] . "<br />" . 
					$row2['date'] . "<br />" . $row2['comment']) . "\r\n";  
			}
		}
		$req2->closeCursor(); $req->closeCursor();
			
	} else if ($postIn == "method=_POST&auteur=extract") {
	
		$req = $db->query("SELECT DISTINCT auteur FROM citations ORDER BY auteur");
		while($row = $req->fetch(PDO::FETCH_ASSOC)) {
			$numrows = $numrows + 1;
			if ($activeDB == 1) {
			    $req2 = $db->query("SELECT citation, auteur, titre, date, comment FROM citations WHERE auteur = '" . $row['auteur'] . "' ORDER BY RANDOM() LIMIT 1"); }
			else { $req2 = $db->query("SELECT citation, auteur, titre, date, comment FROM citations WHERE auteur = " . addQuotes($row['auteur']) . " ORDER BY RAND() LIMIT 1"); }
			while($row2 = $req2->fetch(PDO::FETCH_ASSOC)) {
				$dbresponse .= vignette_template($row['auteur'], $row2['citation'] . "<br />" . $row2['auteur'] . "<br />" . $row2['titre'] . "<br />" . 
					$row2['date'] . "<br />" . $row2['comment']) . "\r\n";
			}
		}
		$req2->closeCursor(); $req->closeCursor();
		
	} else if ($postIn == "method=_POST&date=extract") {
	
		$req = $db->query("SELECT DISTINCT date FROM citations WHERE date != '' ORDER BY date");
		while($row = $req->fetch(PDO::FETCH_ASSOC)) {
			$numrows = $numrows + 1;
			if ($activeDB == 1) {
			    $req2 = $db->query("SELECT citation, auteur, titre, date, comment FROM citations WHERE date = '" . $row['date'] . "' ORDER BY RANDOM() LIMIT 1"); }
			else { $req2 = $db->query("SELECT citation, auteur, titre, date, comment FROM citations WHERE date = " . addQuotes($row['date']) . " ORDER BY RAND() LIMIT 1"); }
			while($row2 = $req2->fetch(PDO::FETCH_ASSOC)) {
				$dbresponse .= vignette_template($row['date'], $row2['citation'] . "<br />" . $row2['auteur'] . "<br />" . $row2['titre'] . "<br />" . 
					$row2['date'] . "<br />" . $row2['comment']) . "\r\n";
			}
		}
		$req2->closeCursor(); $req->closeCursor();
			
	} else if ($postIn == "method=_POST&date=random") {
	
	    if ($activeDB == 1) {
		    $req = $db->query("SELECT date FROM citations WHERE date != '' ORDER BY RANDOM() LIMIT 1"); }
		else { $req = $db->query("SELECT date FROM citations WHERE date != '' ORDER BY RAND() LIMIT 1"); }
		while($row = $req->fetch(PDO::FETCH_ASSOC)) {
		    if ($activeDB == 1) {
			    $req2 = $db->query("SELECT citation, auteur, titre, date, comment FROM citations WHERE date = '" . $row['date'] . "' ORDER BY auteur, titre"); }
			else { $req2 = $db->query("SELECT citation, auteur, titre, date, comment FROM citations WHERE date = " . addQuotes($row['date']) . " ORDER BY auteur, titre"); }
			while($row2 = $req2->fetch(PDO::FETCH_ASSOC)) { 
				$numrows = $numrows + 1;
				$dbresponse .= vignette_template($row['date'], $row2['citation'] . "<br />" . $row2['auteur'] . "<br />" . $row2['titre'] . "<br />" . 
					$row2['date'] . "<br />" . $row2['comment']) . "\r\n";  
			}
		}
		$req2->closeCursor(); $req->closeCursor();
	
	} else if (substr($postIn, 0, 20) == "method=_POST&search=") {
	
		$searchString = trim(substr($postIn, 20));
		$searchPattern = stripSingleQuote($searchString);
		if (strlen($searchPattern) > 4 or (is_int(intval($searchPattern)) == 1 and strlen($searchPattern) > 3)) {
		
			$req = $db->query("SELECT citation, auteur, titre, date, comment, concept FROM citations WHERE UPPER(citation) LIKE '%" . strtoupper($searchPattern) . "%' " .
		  	"OR UPPER(auteur) LIKE '%" . strtoupper($searchPattern) . "%' OR UPPER(concept) LIKE '%" . strtoupper($searchPattern) . "%' OR UPPER(titre) LIKE '%" .
		  	strtoupper($searchPattern) . "%' " . "OR UPPER(comment) LIKE '%" . strtoupper($searchPattern) . "%' OR UPPER(date) LIKE '%" . strtoupper($searchPattern) . 
		  	"%' ORDER BY concept, titre");
			while($row = $req->fetch(PDO::FETCH_ASSOC))	{
				$numrows = $numrows + 1;
					$dbresponse .= vignette_template($searchString, $row['citation'] . "<br />" . $row['auteur'] . "<br />" . $row['titre'] . "<br />" . 
						$row['date'] . "<br />" . $row['comment']) . "\r\n"; 
				}
				$req->closeCursor();
			
			} else { $dbresponse = vignette_template("...", "<p align='center'>" . utf8_decode("Critères de recherche inadéquats. Merci de corriger votre demande avant de poursuivre.") . 
					"</p>") . "\r\n"; }

		} else { $dbresponse = vignette_template("...", "<p align='center'>" . utf8_decode("Requête erronée. Merci de corriger votre demande avant de poursuivre.") . 
				"</p>") . "\r\n"; }

	} else {

		$req = $db->query("SELECT DISTINCT concept FROM citations ORDER BY concept");
		while($row = $req->fetch(PDO::FETCH_ASSOC)) {
			$numrows = $numrows + 1;
			if ($activeDB == 1) {
			    $req2 = $db->query("SELECT citation, auteur, titre, date, comment FROM citations WHERE concept = '" . $row['concept'] . "' ORDER BY RANDOM() LIMIT 1"); }
			else { $req2 = $db->query("SELECT citation, auteur, titre, date, comment FROM citations WHERE concept = " . addQuotes($row['concept']) . " ORDER BY RAND() LIMIT 1"); }
			while($row2 = $req2->fetch(PDO::FETCH_ASSOC)) 
				$dbresponse .= vignette_template($row['concept'], $row2['citation'] . "<br />" . $row2['auteur'] . "<br />" . $row2['titre'] . "<br />" . 
					$row2['date'] . "<br />" . $row2['comment']) . "\r\n";
	}
	$req2->closeCursor(); $req->closeCursor();
	
}

if ($numrows < 2) { $numrows .= " sentence"; } else { $numrows .= " sentences"; }

if ($activeDB == 1) {
    echo utf8_decode(str_replace("{{numrows}}", $numrows, str_replace("{{elapsedtime}}", substr(microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"], 0, 5) . " s", str_replace("{{dbresponse}}", $dbresponse, str_replace("{{title}}", "PHP :: Citalis", file_get_contents('citalis_php.html'))))));
} else {
    echo str_replace("{{numrows}}", $numrows, str_replace("{{elapsedtime}}", substr(microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"], 0, 5) . " s", str_replace("{{dbresponse}}", $dbresponse, str_replace("{{title}}", "PHP :: Citalis", file_get_contents('citalis_php.html')))));
}

