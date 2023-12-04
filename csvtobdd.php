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

    function getIsbn($isbn){

        $sql="SELECT isbn FROM `livres` WHERE isbn LIKE '".$isbn."'";
        return $this->getResultat($sql);
        var_dump($sql);
    }


    function getGenre(){

        $sql="SELECT * FROM genres ";
        return $this->getResultat($sql);
    }


    function getAuteurDatalist(){

        $sql = "SELECT nom_auteur, id_auteur FROM `auteurs` ;";
       
        return $this->getResultat($sql);

    }

    function getAuteursByName($name)
    {
        $sql = "SELECT nom_auteur, id_auteur FROM `auteurs` WHERE nom_auteur LIKE '".$name."';";
       
        return $this->getResultat($sql);
    }

    function getGenreByName($name)
    {

        $sql = "SELECT nom_genre FROM `genres` WHERE nom_genre LIKE '".$name."';";
        return $this->getResultat($sql);
    }



    function ajoutLivre()
    {
        $nom_auteur = $_POST['nom_auteur'];
        $genre = $_POST['genre'];
        $titreLivre = $_POST['titre_livre'];
        $dateParution = $_POST['date_parution'];
        $nombrePages = $_POST['nombrePage'];
        $isbn = $_POST['isbn'];

        

        if (isset($_POST['btn_ajouter'])) {
       
            if (count($this->getAuteursByName($_POST['nom_auteur'])) == 0) {
                $sql = "INSERT INTO auteurs (`id_auteur`,`nom_auteur`) VALUES (NULL,'$nom_auteur')";
                $this->connect->query($sql);
                $last_id = $this->connect->lastInsertId();
                $sql1 = "INSERT INTO livres (`id_livre`,`titre_livre`,`isbn`,`date_parution`,`nombrePage`,`auteur_id`,`id_genre`) VALUES (NULL,'".$titreLivre."','".$isbn."','".$dateParution."','".$nombrePages."','".$last_id."','".$_POST['genre']."')";    
                $this->connect->query($sql1);
                $sql2="INSERT INTO livre_auteur (`id_auteur`) VALUES ('".$last_id."')";
               
           
                $this->connect->query($sql2);
            
                header('location:ajoutlivre.php');
                

            } elseif (count($this->getIsbn(($_POST['isbn']))) == 0) {
                $sql1 = "INSERT INTO livres (`id_livre`,`titre_livre`,`isbn`,`date_parution`,`nombrePage`,`id_genre`) VALUES (NULL,'".$titreLivre."','".$isbn."','".$dateParution."','".$nombrePages."', '".$_POST['genre']."')";
                $this->connect->query($sql1);
                header('location:ajoutlivre.php');
          } elseif (count($this->getIsbn(($_POST['isbn']))) != 0) {
               
            print("Le livre existe deja dans la BDD");
         }

            elseif (count($this->getAuteursByName($_POST['nom_auteur'])) != 0) {
                $sql1 = "INSERT INTO livres (`id_livre`,`titre_livre`,`isbn`,`date_parution`,`nombrePage`,`id_genre`) VALUES (NULL,'".$titreLivre."','".$isbn."','".$dateParution."','".$nombrePages."', '".$_POST['genre']."')";
                $this->connect->query($sql1);
                header('location:ajoutlivre.php');

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
