<?php


class DAO
{


    /* Paramètres de connexion à la base de données 
	Dans l'idéal, il faudrait écrire les getters et setters correspondants pour pouvoir en modifier les valeurs
	au cas où notre serveur change
	*/

    private $host = "127.0.0.1";
    private $user = "root";
    private $password = "";
    private $database = "biblio";
    private $charset = "utf8";

    //instance courante de la connexion
    private $connect;

    //stockage de l'erreur éventuelle du serveur mysql
    private $error;

    public function __construct()
    {
    }

    /* méthode de connexion à la base de donnée */
    public function connexion()
    {

        try {
            // On se connecte à MySQL
            $this->connect = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->database . ';charset=' . $this->charset, $this->user, $this->password);
        } catch (Exception $e) {
            // En cas d'erreur, on affiche un message et on arrête tout
            $this->error = 'Erreur : ' . $e->getMessage();
        }
    }

    public function getResultat($requete)
    {

        $resultat = array();

        $declaration = $this->connect->query($requete);

        if (!$declaration) {

            $this->error = $this->connect->erreurInformation();
            return false;
        } else {

            return $declaration->fetchAll();
        }
    }


    function getAuteursByName($name)
    {
        $sql = "SELECT nom_auteur FROM `auteurs` WHERE nom_auteur LIKE '" . $name . "';";
        var_dump($sql);
        return $this->getResultat($sql);
    }

    function getGenreByName($name)
    {

        $sql = "SELECT nom_genre FROM `genres` WHERE nom_genre LIKE '" . $name . "';";
        return $this->getResultat($sql);
    }



    function ajoutLivre()
    {
       
        var_dump($_POST['nom_auteur']);


        $nom_auteur = $_POST['nom_auteur'];
        $genre = $_POST['genre'];
        $titreLivre = $_POST['titre_livre'];
        $dateParution = $_POST['date_parution'];
        $nombrePages = $_POST['nombrePage'];


        if (isset($_POST['btn_ajouter'])) {

            var_dump(count($this->getAuteursByName($_POST['nom_auteur'])));
            var_dump(count($this->getAuteursByName($_POST['genre'])));


            if ($this->getAuteursByName($_POST['nom_auteur']) != $nom_auteur) {
                $sql = "INSERT INTO auteurs (`id_auteur`, `nom_auteur`) VALUES (NULL, ' $nom_auteur ')  ";
                $sql1 = "INSERT INTO livres (`id_livre`, `titre_livre`, `date_parution`,  `nombrePage`) VALUES (NULL, '" . $titreLivre . "', '" . $dateParution . "', '" . $nombrePages . "' )";
                $sql3 = "INSERT INTO genres (`id_genre`, `nom_genre`) VALUES (NULL, '" . $genre . "' )";
                $this->connect->query($sql);
                $this->connect->query($sql1);
                $this->connect->query($sql3);
                // header('location:ajoutlivre.php');
                print("Livre ajouté1");
            } elseif ($this->getGenreByName($_POST['genre']) != $genre) {
                $sql = "INSERT INTO auteurs (`id_auteur`, `nom_auteur`) VALUES (NULL, ' $nom_auteur ')  ";
                $sql1 = "INSERT INTO livres (`id_livre`, `titre_livre`, `date_parution`,  `nombrePage`) VALUES (NULL, '" . $titreLivre . "', '" . $dateParution . "', '" . $nombrePages . "' )";
                $sql3 = "INSERT INTO genres (`id_genre`, `nom_genre`) VALUES (NULL, '" . $genre . "' )";
                $this->connect->query($sql);
                $this->connect->query($sql1);
                $this->connect->query($sql3);
                // header('location:ajoutlivre.php');
                print("Livre ajouté2");
            } elseif ($this->getAuteursByName($_POST['nom_auteur']) == $genre) {
                $sql1 = "INSERT INTO livres (`id_livre`, `titre_livre`, `date_parution`,  `nombrePage`) VALUES (NULL, '" . $titreLivre . "', '" . $dateParution . "', '" . $nombrePages . "' )";
                $sql3 = "INSERT INTO genres (`id_genre`, `nom_genre`) VALUES (NULL, '" . $genre . "' )";
                $this->connect->query($sql1);
                $this->connect->query($sql3);
                print("Livre ajouté3");
            } elseif ($this->getGenreByName($_POST['genre']) == $nom_auteur) {
                $sql = "INSERT INTO auteurs (`id_auteur`, `nom_auteur`) VALUES (NULL, '  $nom_auteur  ') ";
                $sql1 = "INSERT INTO livres (`id_livre`, `titre_livre`, `date_parution`,  `nombrePage`) VALUES (NULL, '" . $titreLivre . "', '" . $dateParution . "', '" . $nombrePages . "' )";
                $this->connect->query($sql);
                $this->connect->query($sql1);
                print("Livre ajouté4");
            }
        }
    }
}

/*
		function de lecture du fichier csv
		en paramètre le nom du fichier à lire (chemin)
	*/
function readCsv($filename)
{
    $datas = array();
    //on ouvre le fichier en lecture
    if (($handle = fopen($filename, "r")) !== FALSE) {

        //on lit le fichier ligne par ligne
        while (($datas = fgetcsv($handle, 1000, ",")) !== FALSE) {


            //on ajoute la ligne à un tableau php
            print_r($datas);
        }


        fclose($handle);
    }


    return $datas;
}

// //on fait la jonction avec le fichier DAO
// require_once("csvtobdd.php");
//     $dao = new DAO();
//     $dao->connexion();
//     $author="";
//     $remplir = $dao->remplirAuteurs($author);



// $json=json_decode(file_get_contents("books.json"));

// 	foreach($json as $book) {
// 		print $book->title;
// 		foreach($book->authors as $author) {


//             print $remplir;
// 		}
// 	}
