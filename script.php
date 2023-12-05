<?php

$dsn = 'mysql:dbname=biblio;host=127.0.0.1';
$user = 'root';
$password = '';

$dbh = new PDO($dsn, $user, $password);

/*
		function de lecture du fichier csv
		en paramètre le nom du fichier à lire (chemin)
	*/
	/*function readJson($filename) {
		$datas=array();
		//on ouvre le fichier en lecture
		if (($handle = fopen($filename, "r")) !== FALSE) {
			
			//on lit le fichier ligne par ligne
                
			while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
				
				//on ajoute la ligne à un tableau php
				//print_r($data);

				
			}
			fclose($handle);
		}
		
	}*/


	$json=json_decode(file_get_contents("books.json"));
	//var_dump($json);
    foreach ($json as $book) {
		$title = $book->title;
		$isbn = $book->isbn;
		$pageCount = $book->pageCount;
		$publishedDate = $book->publishedDate;
		$shortDescription = $book->shortDescription;
		$longDescription = $book->longDescription;
		$image = $book->thumbnailUrl;
		$authors = $book->authors;
		$genre=$book->categories;
		$status_dispo=$book->status;
	

	//Integration auteur dans bdd
	
	//Implose le tableau des auteurs en chaine de cararactere avec virgule en séparateur
		if (is_array($authors)) {
			$authors = implode(', ', $authors);
		}
	
		// Insertion des données dans la table "auteurs"
		$sqlAuteur = $dbh->prepare("INSERT INTO auteurs (nom_auteur) VALUES (:authors)");
		$sqlAuteur->bindParam(':authors', $authors, PDO::PARAM_STR);
		
		$sqlAuteur->execute();

		
	
		
		// Récupération de l'ID de l'auteur nouvellement inséré
		$idAuteur = $dbh->lastInsertId();

		// Insertion des données dans la table livres

		$sqlLivre = $dbh->prepare("INSERT INTO livres (titre_livre, isbn, pageCount, date_parution, shortDescription, longDescription, image, auteur_id) VALUES (:title, :isbn, :pageCount, :publishedDate, :shortDescription, :longDescription, :thumbnailUrl, :auteur_id)");
		$sqlLivre->bindParam(':title', $title, PDO::PARAM_STR);
		$sqlLivre->bindParam(':isbn', $isbn, PDO::PARAM_STR);
		$sqlLivre->bindParam(':pageCount', $pageCount, PDO::PARAM_INT);
	
		if (!empty($publishedDate)) {
			$formattedDate = date("Y-m-d", strtotime($book->publishedDate->dt_txt));
			$sqlLivre->bindParam(':publishedDate', $formattedDate, PDO::PARAM_STR);
		} else {
			$sqlLivre->bindValue(':publishedDate', null, PDO::PARAM_NULL);
		}

	
		$sqlLivre->bindParam(':shortDescription', $shortDescription, PDO::PARAM_STR);
		$sqlLivre->bindParam(':longDescription', $longDescription, PDO::PARAM_STR);
		$sqlLivre->bindParam(':thumbnailUrl', $image, PDO::PARAM_STR);
		$sqlLivre->bindParam(':auteur_id', $idAuteur, PDO::PARAM_INT);
		
		$sqlLivre->execute();
		

		$idLivre = $dbh->lastInsertId("id_livre");

		
		//insertion des genres 

	   if (is_array($genre)) {
        $genre = implode(', ', $genre);
    }
	 
	
	$sqlGenre = $dbh->prepare("INSERT INTO genres (nom_genre) VALUES (:categories)");
	
	if (!empty($genre)) {
		$sqlGenre->bindParam(':categories', $genre, PDO::PARAM_STR);
	} 
	else {
		$sqlGenre->bindValue(':categories', null, PDO::PARAM_NULL);
	}
		
		$sqlGenre->execute();
		
		
	
	$status_dispo = ($book->status === "PUBLISH") ? 1 : 2;
    // Insertion des données dans la table "disponibilités"
    $sqlDisponibilite = $dbh->prepare("INSERT INTO disponibilités (status_dispo) VALUES (:status_dispo)");
    $sqlDisponibilite->bindParam(':status_dispo', $status_dispo, PDO::PARAM_INT);
   

    $sqlDisponibilite->execute();

	}

	?>







