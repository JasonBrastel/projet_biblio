<?php

$dsn = 'mysql:dbname=biblio;host=127.0.0.1';
$user = 'root';
$password = '';

$dbh = new PDO($dsn, $user, $password);

/*
		function de lecture du fichier csv
		en paramètre le nom du fichier à lire (chemin)
	*/
	function readJson($filename) {
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
		
	}



		$json=json_decode(file_get_contents("books.json"));
	//var_dump($json);
    foreach ($json as $book) {
        $title = $book->title;
        $isbn = $book->isbn;
        $pageCount = $book->pageCount;
     
    
        // Utilisation de la préparation de requête
        $sql = $dbh->prepare("INSERT INTO livres (titre_livre, isbn, pageCount,date_parution) VALUES (:title, :isbn, :pageCount; :publishedDate)");
        $sql->bindParam(':title', $title, PDO::PARAM_STR);
        $sql->bindParam(':isbn', $isbn, PDO::PARAM_STR);
        $sql->bindParam(':pageCount', $pageCount, PDO::PARAM_INT);
        $sql->bindParam(':publishedDate', $publishedDate,PDO ::PARAM_STR);
   
    
        // Exécution de la requête
        $sql->execute();
    }
	

?>






