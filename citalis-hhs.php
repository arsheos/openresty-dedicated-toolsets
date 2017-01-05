<?hh // strict

namespace citalisstrict; // string\string\...
use PDO;
require_once 'citalis-hhl.php';

function httpd_active(): Pair<string, string> {
  if (strpos(active_platform()[0], 'nginx') > -1) { $httpd_values = Pair{"NGINX", "http://openresty.org/en/"}; }
  else { $httpd_values = Pair{"Apache", "https://httpd.apache.org/"}; }
  return $httpd_values;
}

function language_active(): Vector<string> {
  if (strpos(active_platform()[1], 'hhvm') > -1) { $language_values = Vector{"Hack-" . strtoupper(phpversion()), "https://docs.hhvm.com/hack/", "Hack :: Citalis"}; }
  else { $language_values = Vector{"PHP-". strtoupper(phpversion()), "https://secure.php.net/", "PHP :: Citalis"}; }
  return $language_values;
}

function dns_info(): string { return active_platform()[2]; }

function addQuotes(string $str): string { return '"' . $str . '"'; }

function stripSingleQuote(string $str): string {
	$str = trim($str);
	if (substr($str, -1) == "'") $str = substr($str, 0, -2);
	if (substr($str, 0) == "'") $str = substr($str, 1, -1);
	return $str;
}

/* XHP © Facebook; both PHP5/7 and Hack modules available */

function vignette_template (string $tparam1, string $tparam2): string {
	$random_a = random_int(1, 8); $random_b = random_int(1, 100) + 270; // Hack/PHP7 type
  return "<article class='item thumb' data-width={$random_b} data-value=" . addQuotes(str_replace('"', "'", $tparam2)) . "<br />" .
  	 addQuotes($tparam1) . "><div class='citation' style='background-image: url(images/thumbs/0{$random_a}.jpg'>" .
  	str_replace('"', "'", $tparam2) . "</div><h7><a href='#' onclick='javascript:$('.image').click();' class=" .
  	"'icon fa-arrows-alt'><span class='label'>Detail</span></a></h7><h2><input class='ccategories'" .
  	" type='submit' name='search' value=" . addQuotes($tparam1) . "></h2><a href='images/fulls/0{$random_a}.jpg'" .
  	" class='image' alt=''><img src='images/thumbs/0{$random_a}.jpg' alt=''></a></article>";
}

function main(): string {

  #echo "OK"; exit();

	$startTime = microtime(true);

  if (requestMethod() == "PUT" || requestMethod() == "DELETE") exit(); // black hats hacking attempts sandbox

	/* mandatory vars init (PHP7 / Hack) <- no autoinfered */
	$retour = ""; $numrows = 0; $response = ""; $dbresponse = "";

	$activeDB = 1; /* MySQL = 0, PostgreSQL = 1 */

	if ($activeDB == 1) $db = new PDO('pgsql:host=localhost; dbname=citalis;client_encoding=UTF8', 'plbc', 'plbc');
  else {
    $db = new PDO('mysql:host=localhost; dbname=citalis', 'plbc', 'plbc');
    $db->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, 'SET NAMES UTF8');
  }
	$db->setAttribute(PDO::ATTR_TIMEOUT, 300);
	$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

	if (requestMethod() === "POST") {

    $postIn = "method=_POST"; $kv = POSTin_params();
    foreach ($kv as $k => $v) $postIn .= "&$k=$v";

		if ($postIn == "method=_POST&jsonadd=Ajouter les enregistrements") {

      if (FILESin_params()["name"] != '') {

        $file_name = FILESin_params()["name"]; $file_size = FILESin_params()["size"];
        $file_tmp =  FILESin_params()["tmp_name"]; $file_type = FILESin_params()["type"];
        $file_ext =  strtolower(end(explode('.', FILESin_params()["name"])));

			  if (strlen($file_name) == 0) $retour = "Fichier non sélectionné.";
			  else if ($file_ext != "json") $retour = "Extension non autorisée. Merci de sélectionner un ficher JSON.";
			  else if ($file_size > 2048000) $retour = "Le fichier ne doit pas dépasser 2 Mo";
			  else if ($file_size == 0) $retour = "Fichier vide.";

			  if (strlen($retour) == 0) {

			  	move_uploaded_file($file_tmp, "jsonfiles/".$file_name);
			  	$jsondata = file_get_contents($file_tmp, $use_include_path = true);
			  	$array = json_decode($jsondata, true); $firstLineKeys = false;

					foreach ($array as $record) {
						$response .= "(";
						$numcol = 0;
			  		foreach ($record as $col) {
			  			$numcol = $numcol + 1;
			  			if ($numcol > 1) $response .= "'" . htmlentities($col) . "', ";
			   		}
			  		$response .= " '100     test'),";
			  		$numrows = $numrows + 1;
			  	}

					$req1 = $db->query("INSERT INTO citations (titre, citation, auteur, comment, concept) VALUES " . substr(str_replace("\n\n", " ", $response), 0, -1));
					$req1->closeCursor();

			  	$dbresponse = vignette_template("...", "<p align='center'>" .
			  		htmlentities($numrows . " enregistrements viennent d'être ajoutés à la base Citalis avec succès.") . "</p>") . "\r\n";

			  } else $dbresponse = vignette_template("...", "<p align='center'>" . htmlentities($retour) . "</p>") . "\r\n";

			} else $dbresponse = vignette_template("...", "<p align='center'>" . htmlentities("Ooops ! Une erreur de connexion est survenue.") . "</p>") . "\r\n";

		} else if ($postIn == "method=_POST&jsonrem=Supprimer les enregistrements de test") {

			$req1 = $db->query("DELETE FROM citations WHERE concept = '100     test'");
			$req1->closeCursor();
			$dbresponse = vignette_template("...", "<p align='center'>" .
				htmlentities("Les enregistrements de test viennent d'être supprimés de la base Citalis.") . "</p>") . "\r\n";

	} else if ($postIn == "method=_POST&concept=extract") {

    $req1 = $db->query("SELECT DISTINCT concept FROM citations ORDER BY concept");
    while($row = $req1->fetch(PDO::FETCH_ASSOC)) {
	    $numrows = $numrows + 1;
	    if ($activeDB == 1) {
	        $req2 = $db->query("SELECT citation, auteur, titre, date, comment FROM citations WHERE concept = '" . $row['concept'] . "' ORDER BY RANDOM() LIMIT 1"); }
	    else { $req2 = $db->query("SELECT citation, auteur, titre, date, comment FROM citations WHERE concept = " . addQuotes($row['concept']) . " ORDER BY RAND() LIMIT 1"); }
	    while($row2 = $req2->fetch(PDO::FETCH_ASSOC)) {
		    $dbresponse .= vignette_template($row['concept'], $row2['citation'] . "<br />" . $row2['auteur'] . "<br />" . $row2['titre'] . "<br />" .
					$row2['date'] . "<br />" . $row2['comment']) . "\r\n";
			}
    }
   	$req1->closeCursor();

	} else if ($postIn == "method=_POST&concept=random") {

    if ($activeDB == 1) {
		    $req1 = $db->query("SELECT concept FROM citations ORDER BY RANDOM() LIMIT 1"); }
		else { $req1 = $db->query("SELECT concept FROM citations ORDER BY RAND() LIMIT 1"); }
		while($row = $req1->fetch(PDO::FETCH_ASSOC)) {
	    if ($activeDB == 1) {
			    $req2 = $db->query("SELECT citation, auteur, titre, date, comment FROM citations WHERE concept = '" . $row['concept'] . "' ORDER BY auteur, titre"); }
			else { $req2 = $db->query("SELECT citation, auteur, titre, date, comment FROM citations WHERE concept = " . addQuotes($row['concept']) . " ORDER BY auteur, titre"); }
			while($row2 = $req2->fetch(PDO::FETCH_ASSOC)) {
				$numrows = $numrows + 1;
				$dbresponse .= vignette_template($row['concept'], $row2['citation'] . "<br />" . $row2['auteur'] . "<br />" . $row2['titre'] . "<br />" .
					$row2['date'] . "<br />" . $row2['comment']) . "\r\n";
			}
		}
		$req1->closeCursor();

	} else if ($postIn == "method=_POST&auteur=random") {

    if ($activeDB == 1) {
		    $req1 = $db->query("SELECT auteur FROM citations ORDER BY RANDOM() LIMIT 1"); }
		else { $req1 = $db->query("SELECT auteur FROM citations ORDER BY RAND() LIMIT 1"); }
		while($row = $req1->fetch(PDO::FETCH_ASSOC)) {
		    if ($activeDB == 1) {
			    $req2 = $db->query("SELECT citation, auteur, titre, date, comment FROM citations WHERE auteur = '" . $row['auteur'] . "' ORDER BY auteur, titre"); }
			else { $req2 = $db->query("SELECT citation, auteur, titre, date, comment FROM citations WHERE auteur = " . addQuotes($row['auteur']) . " ORDER BY auteur, titre"); }
			while($row2 = $req2->fetch(PDO::FETCH_ASSOC)) {
				$numrows = $numrows + 1;
				$dbresponse .= vignette_template($row['auteur'], $row2['citation'] . "<br />" . $row2['auteur'] . "<br />" . $row2['titre'] . "<br />" .
					$row2['date'] . "<br />" . $row2['comment']) . "\r\n";
			}
		}
		$req1->closeCursor();

	} else if ($postIn == "method=_POST&auteur=extract") {

		$req1 = $db->query("SELECT DISTINCT auteur FROM citations ORDER BY auteur");
		while($row = $req1->fetch(PDO::FETCH_ASSOC)) {
			$numrows = $numrows + 1;
			if ($activeDB == 1) {
			    $req2 = $db->query("SELECT citation, auteur, titre, date, comment FROM citations WHERE auteur = '" . $row['auteur'] . "' ORDER BY RANDOM() LIMIT 1"); }
			else { $req2 = $db->query("SELECT citation, auteur, titre, date, comment FROM citations WHERE auteur = " . addQuotes($row['auteur']) . " ORDER BY RAND() LIMIT 1"); }
			while($row2 = $req2->fetch(PDO::FETCH_ASSOC)) {
				$dbresponse .= vignette_template($row['auteur'], $row2['citation'] . "<br />" . $row2['auteur'] . "<br />" . $row2['titre'] . "<br />" .
					$row2['date'] . "<br />" . $row2['comment']) . "\r\n";
			}
		}
		$req1->closeCursor();

	} else if ($postIn == "method=_POST&date=extract") {

		$req1 = $db->query("SELECT DISTINCT date FROM citations WHERE date != '' ORDER BY date");
		while($row = $req1->fetch(PDO::FETCH_ASSOC)) {
			$numrows = $numrows + 1;
			if ($activeDB == 1) {
			    $req2 = $db->query("SELECT citation, auteur, titre, date, comment FROM citations WHERE date = '" . $row['date'] . "' ORDER BY RANDOM() LIMIT 1"); }
			else { $req2 = $db->query("SELECT citation, auteur, titre, date, comment FROM citations WHERE date = " . addQuotes($row['date']) . " ORDER BY RAND() LIMIT 1"); }
			while($row2 = $req2->fetch(PDO::FETCH_ASSOC)) {
				$dbresponse .= vignette_template($row['date'], $row2['citation'] . "<br />" . $row2['auteur'] . "<br />" . $row2['titre'] . "<br />" .
					$row2['date'] . "<br />" . $row2['comment']) . "\r\n";
			}
		}
		$req1->closeCursor();

	} else if ($postIn == "method=_POST&date=random") {

    if ($activeDB == 1) {
		    $req1 = $db->query("SELECT date FROM citations WHERE date != '' ORDER BY RANDOM() LIMIT 1"); }
		else { $req1 = $db->query("SELECT date FROM citations WHERE date != '' ORDER BY RAND() LIMIT 1"); }
		while($row = $req1->fetch(PDO::FETCH_ASSOC)) {
	    if ($activeDB == 1) {
			    $req2 = $db->query("SELECT citation, auteur, titre, date, comment FROM citations WHERE date = '" . $row['date'] . "' ORDER BY auteur, titre"); }
			else { $req2 = $db->query("SELECT citation, auteur, titre, date, comment FROM citations WHERE date = " . addQuotes($row['date']) . " ORDER BY auteur, titre"); }
			while($row2 = $req2->fetch(PDO::FETCH_ASSOC)) {
				$numrows = $numrows + 1;
				$dbresponse .= vignette_template($row['date'], $row2['citation'] . "<br />" . $row2['auteur'] . "<br />" . $row2['titre'] . "<br />" .
					$row2['date'] . "<br />" . $row2['comment']) . "\r\n";
			}
		}
		$req1->closeCursor();

	} else if (substr($postIn, 0, 20) == "method=_POST&search=") {

		$searchString = trim(substr($postIn, 20));
		$searchPattern = stripSingleQuote($searchString);
		if (strlen($searchPattern) > 4 || (is_int(intval($searchPattern)) == 1 && strlen($searchPattern) > 3)) {

			$req1 = $db->query("SELECT citation, auteur, titre, date, comment, concept FROM citations WHERE UPPER(citation) LIKE '%" . strtoupper($searchPattern) . "%' " .
		  	"OR UPPER(auteur) LIKE '%" . strtoupper($searchPattern) . "%' OR UPPER(concept) LIKE '%" . strtoupper($searchPattern) . "%' OR UPPER(titre) LIKE '%" .
		  	strtoupper($searchPattern) . "%' " . "OR UPPER(comment) LIKE '%" . strtoupper($searchPattern) . "%' OR UPPER(date) LIKE '%" . strtoupper($searchPattern) .
		  	"%' ORDER BY concept, titre");
			while($row = $req1->fetch(PDO::FETCH_ASSOC))	{
				$numrows = $numrows + 1;
				$dbresponse .= vignette_template($searchString, $row['citation'] . "<br />" . $row['auteur'] . "<br />" . $row['titre'] . "<br />" .
					$row['date'] . "<br />" . $row['comment']) . "\r\n";
			}
			$req1->closeCursor();

		} else { $dbresponse = vignette_template("...", "<p align='center'>" .
				htmlentities("Critères de recherche inadéquats. Merci de corriger votre demande avant de poursuivre.") . "</p>") . "\r\n"; }

	} else { $dbresponse = vignette_template("...", "<p align='center'>" .
			htmlentities("Requête erronée. Merci de corriger votre demande avant de poursuivre.") . "</p>") . "\r\n"; }

	} else {

		$req1 = $db->query("SELECT DISTINCT concept FROM citations ORDER BY concept");
		while($row = $req1->fetch(PDO::FETCH_ASSOC)) {
			$numrows = $numrows + 1;
			if ($activeDB == 1) {
			    $req2 = $db->query("SELECT citation, auteur, titre, date, comment FROM citations WHERE concept = '" . $row['concept'] . "' ORDER BY RANDOM() LIMIT 1"); }
			else { $req2 = $db->query("SELECT citation, auteur, titre, date, comment FROM citations WHERE concept = " . addQuotes($row['concept']) . " ORDER BY RAND() LIMIT 1"); }
			while($row2 = $req2->fetch(PDO::FETCH_ASSOC)) {
				$dbresponse .= vignette_template($row['concept'], $row2['citation'] . "<br />" . $row2['auteur'] . "<br />" . $row2['titre'] . "<br />" .
					$row2['date'] . "<br />" . $row2['comment']) . "\r\n";
			}
		}
		$req1->closeCursor();

	}

  $numrows .= " sentence" . ($numrows > 1 ? 's' : '');

	if ($activeDB == 1) {
	    return utf8_decode(str_replace("{{elapsedtime}}", substr(microtime(true) - $startTime, 0, 5) . " s", str_replace("{{dbresponse}}", $dbresponse,
        str_replace("{{numrows}}", $numrows, str_replace("{{httpd_url}}", httpd_active()[1], str_replace("{{httpd_name}}", httpd_active()[0],
        str_replace("{{dns_info}}", dns_info(), str_replace("{{title}}", language_active()[2], str_replace("{{language_url}}", language_active()[1],
        str_replace("{{language_name}}", language_active()[0], str_replace("{{db_url}}", "https://www.postgresql.org/", str_replace("{{db_name}}", "PostgreSQL",
        file_get_contents('citalis_php.html')))))))))))));
	} else {
	    return str_replace("{{elapsedtime}}", substr(microtime(true) - $startTime, 0, 5) ." s", str_replace("{{dbresponse}}", $dbresponse,
        str_replace("{{numrows}}", $numrows, str_replace("{{httpd_url}}", httpd_active()[1], str_replace("{{httpd_name}}", httpd_active()[0],
        str_replace("{{dns_info}}", dns_info(), str_replace("{{title}}", language_active()[2], str_replace("{{language_url}}", language_active()[1],
        str_replace("{{language_name}}", language_active()[0], str_replace("{{db_url}}", "http://dev.mysql.com/downloads/", str_replace("{{db_name}}", "MySQL",
        file_get_contents('citalis_php.html'))))))))))));
	}
}
