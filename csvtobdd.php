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

    /* méthode pour fermer la connexion à la base de données */
	public function disconnect()
	{
		$this->connect = null;
	}

    //FONCTION QUI RECUPERE LES RESULTATS DES REQUETES SQL
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
    //FONCTION POUR RECUPERER IBSN DES LIVRES
    function getIsbn($isbn){

        $sql="SELECT isbn FROM `livres` WHERE isbn LIKE '".$isbn."'";
        return $this->getResultat($sql);
        var_dump($sql);
    }

    //FONCTION POUR RECUPERER LES GENRES DE LIVRES
    function getGenre(){

        $sql="SELECT * FROM genres ";
        return $this->getResultat($sql);
    }

    //FONCTION POUR RECUPERER LES AUTEURS POUR LA DATALIST
    function getAuteurDatalist(){

        $sql = "SELECT nom_auteur, id_auteur FROM `auteurs` ;";
       
        return $this->getResultat($sql);

    }

    //FONCTION POUR RECUPERER LES NOMS DES AUTEURS 
    function getAuteursByName($name)
    {
        $sql = "SELECT nom_auteur, id_auteur FROM `auteurs` WHERE nom_auteur LIKE '".$name."';";
       
        return $this->getResultat($sql);
    }

    //FONCITON POUR RECUPERER LES GENRES
    function getGenreByName($name)
    {

        $sql = "SELECT nom_genre FROM `genres` WHERE nom_genre LIKE '".$name."';";
        return $this->getResultat($sql);
    }


    //LA FONCTION D'AJOUT DE LIVRES
    function ajoutLivre()
    {   
        //Variables qui recuperes les valeurs des POST
        $nom_auteur = $_POST['nom_auteur'];
        $genre = $_POST['genre'];
        $titreLivre = $_POST['titre_livre'];
        $dateParution = $_POST['date_parution'];
        $nombrePages = $_POST['nombrePage'];
        $isbn = $_POST['isbn'];

        //Verification de l'envoi par le bouton ajouter
        if (isset($_POST['btn_ajouter'])) {
       
            //Comptage des lignes a l'issu de la requete inclu dans la fonction "getAuteursByName" SI le resultat est 0, c'est que l'auteur n'existe pas dans la BDD 
            if (count($this->getAuteursByName($_POST['nom_auteur'])) == 0) {

                //j'insere dans la table auteur
                $sql = "INSERT INTO auteurs (`id_auteur`,`nom_auteur`) VALUES (NULL,'$nom_auteur')";
                $this->connect->query($sql);

                //je recupere l'ID de la derniere requete INSERT effecutée et je la stocke dans une variable pour pouvoir la réutiliser apres
                $last_id_auteur = $this->connect->lastInsertId();

                //j'insere dans la table livres 
                $sql1 = "INSERT INTO livres (`id_livre`,`titre_livre`,`isbn`,`date_parution`,`nombrePage`,`auteur_id`,`id_genre`) VALUES (NULL,'".$titreLivre."','".$isbn."','".$dateParution."','".$nombrePages."','".$last_id_auteur."','".$_POST['genre']."')";    
                $this->connect->query($sql1);

                //je recupere l'ID de la derniere requete INSERT effecutée et je la stocke dans une variable pour pouvoir la réutiliser apres
                $last_id_livre = $this->connect->lastInsertId();

                //J'insere dans la table livre_genre
                $sql3="INSERT INTO livre_genre (`id_livre`,`id_genre`) VALUES ('".$last_id_livre."','".$genre."')";
                $this->connect->query($sql3);
                
                //J'insere dans la table "livre auteur" l'id du dernier livre ajouté et, grace a la sous requete SELECT, l'id de l'auteur qui a été rentré précédemment dans la table auteur
                $sql4="INSERT INTO livre_auteur (`id_livre`,`id_auteur`) VALUES ('".$last_id_livre."',(SELECT id_auteur FROM auteurs WHERE nom_auteur LIKE '".$nom_auteur."'))";
                $this->connect->query($sql4);

                //J'insere dans la table stock le nombre de livres que l'on ajoute
                $sql5="INSERT INTO stock (`Nombre_livre`) VALUES ('".$_POST['quantity']."')";
                $this->connect->query($sql5);

                header('location:ajoutlivre.php');

                //Comptage des lignes a l'issu de la requete inclu dans la fonction "getIsbn" SI le resultat est 0, c'est que l'ISBN n'existe pas dans la BDD donc le livre peut etre ajouté
                } elseif (count($this->getIsbn(($_POST['isbn']))) == 0) {
    
                    $sql1 = "INSERT INTO livres (`id_livre`,`titre_livre`,`isbn`,`date_parution`,`nombrePage`,`auteur_id`,`id_genre`) VALUES (NULL,'".$titreLivre."','".$isbn."','".$dateParution."','".$nombrePages."',(SELECT id_auteur FROM auteurs WHERE nom_auteur LIKE '".$nom_auteur."'),'".$_POST['genre']."')";    
                    $this->connect->query($sql1);
    
                    $last_id_livre = $this->connect->lastInsertId();
    
                    $sql3="INSERT INTO livre_genre (`id_livre`,`id_genre`) VALUES ('".$last_id_livre."','".$genre."')";
                    $this->connect->query($sql3);
                    
                    $sql4="INSERT INTO livre_auteur (`id_livre`,`id_auteur`) VALUES ('".$last_id_livre."',(SELECT id_auteur FROM auteurs WHERE nom_auteur LIKE '".$nom_auteur."'))";
                    $this->connect->query($sql4);
    
                    $sql5="INSERT INTO stock (`Nombre_livre`) VALUES ('".$_POST['quantity']."')";
                    $this->connect->query($sql5);
                

                header('location:ajoutlivre.php');

                //Comptage des lignes a l'issu de la requete inclu dans la fonction "getIsbn" SI le resultat est différent de 0 , c'est que l'ISBN existe dans la BDD 
                } elseif (count($this->getIsbn(($_POST['isbn']))) != 0) {
                print("Le livre existe deja dans la BDD");
                }

                //Comptage des lignes a l'issu de la requete inclu dans la fonction "getAuteursByName" SI le resultat est différent de 0, c'est que l'auteur existe deja, donc on ajoute tout sauf l'auteur
                elseif (count($this->getAuteursByName($_POST['nom_auteur'])) != 0) {
                $sql1 = "INSERT INTO livres (`id_livre`,`titre_livre`,`isbn`,`date_parution`,`nombrePage`,`auteur_id`,`id_genre`) VALUES (NULL,'".$titreLivre."','".$isbn."','".$dateParution."','".$nombrePages."',(SELECT id_auteur FROM auteurs WHERE nom_auteur LIKE '".$nom_auteur."'), '".$_POST['genre']."')";
                $this->connect->query($sql1);

                $last_id_livre = $this->connect->lastInsertId();
                $sql3="INSERT INTO livre_genre (`id_livre`,`id_genre`) VALUES ('".$last_id_livre."','".$genre."')";
                $this->connect->query($sql3);

                $sql4="INSERT INTO livre_auteur (`id_livre`,`id_auteur`) VALUES ('".$last_id_livre."','".$nom_auteur."')";
                $this->connect->query($sql4);

                $sql5="INSERT INTO stock (`Nombre_livre`) VALUES ('".$_POST['quantity']."')";
                $this->connect->query($sql5);

                header('location:ajoutlivre.php');

            }   
        }     
    }
}

