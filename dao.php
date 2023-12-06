<?php

class DAO
{
    /* Paramètres de connexion à la base de données 
	Dans l'idéal, il faudrait écrire les getters et setters correspondants pour pouvoir en modifier les valeurs
	au cas où notre serveur change
	*/
    //paramètres de connexion à la base de donnée

    private $host = "127.0.0.1";
    private $user = "root";
    private $password = "";
    private $database = "biblio";
    private $charset = "utf8";

    //instance courante de la connexion
    private $bdd;

    //stockage de l'erreur éventuelle du serveur mysql
    private $error;

    //constructeur de la classe

    public function __construct()
    {
    }


    //méthode pour récupérer les résultats d'une requête SQL
    public function getResults($query) {
        $results=array();

        $stmt = $this->bdd->query($query);

        if (!$stmt) {
            $this->error=$this->bdd->errorInfo();
            return false;
        } else {
            // fetch uniquement PDO associative 
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

    }

    /* méthode de connexion à la base de donnée */
    public function connexion()
    {

        try {

            // On se connecte à MySQL
          
            $this->bdd = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->database . ';charset=' . $this->charset, $this->user, $this->password);
        } catch (Exception $e) {
            // En cas d'erreur, on affiche un message et on arrête tout
            $this->error = 'Erreur : ' . $e->getMessage();
        }

    }

    /* méthode pour fermer la connexion à la base de données */
	public function disconnect()
	{
		$this->bdd = null;
	}


    //CLEMENT
    
    //FONCTION QUI RECUPERE LES RESULTATS DES REQUETES SQL
    public function getResultat($requete)
    {

        $resultat = array();

        $declaration = $this->bdd->query($requete);

        if (!$declaration) {

            $this->error = $this->bdd->erreurInformation();
            return false;
        } else {
            return $declaration->fetchAll();
        }
    }

    public function getAlone($requete, $param)
    {

        $resultat = array();

        $declaration = $this->bdd->prepare($requete);

        if (!$declaration) {

            $this->error = $this->bdd->erreurInformation();
            return false;
        } else {
            $declaration->execute($param);
            return $declaration->fetch();
        }
    }
    //FONCTION POUR RECUPERER IBSN DES LIVRES
    function getIsbn($isbn){

        $sql="SELECT isbn FROM `livres` WHERE isbn LIKE '".$isbn."'";
        return $this->getResultat($sql);
        var_dump($sql);
    }



    function get_livre(){

        $sql="SELECT id_livre, titre_livre, disponibilite_id FROM `livres`";
        return $this->getResultat($sql);
        
    }


    function get_livre_emprunt($param = []){

        $sql="SELECT * FROM `livres` where titre_livre = :inputTitre";
        return $this->getAlone($sql, $param);
        
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



    function get_livre_utilisateur($name){

        $sql="SELECT id_livre FROM `livre_utilisateur`";
        return $this->getResultat($sql);

    }
    function verif_dispo_livre($id_livre){

        $sql="SELECT disponibilite_id FROM livres WHERE id_livre LIKE  $id_livre";
        return $this->getResultat($sql);

    }

    function getUtilisateur(){

        $sql= "SELECT * FROM `utilisateurs`";
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
                $sql = "INSERT INTO auteurs (`id_auteur`,`nom_auteur`) VALUES (?,?)";

                $query = $this->bdd->prepare($sql);
                $query ->execute([NULL,$nom_auteur]);


                //je recupere l'ID de la derniere requete INSERT effecutée et je la stocke dans une variable pour pouvoir la réutiliser apres
                $last_id_auteur = $this->bdd->lastInsertId();

                //j'insere dans la table livres 
                $sql1 = "INSERT INTO livres (`id_livre`,`titre_livre`,`isbn`,`date_parution`,`nombrePage`,`auteur_id`,`id_genre`) VALUES (?,?,?,?,?,?,?)";    
                $query = $this->bdd->prepare($sql1);
                $query->execute([NULL,"$titreLivre","$isbn","$dateParution","$nombrePages","$last_id_auteur","$_POST[genre]"]);

                //je recupere l'ID de la derniere requete INSERT effecutée et je la stocke dans une variable pour pouvoir la réutiliser apres
                $last_id_livre = $this->bdd->lastInsertId();

                //J'insere dans la table livres_genres
                $sql3="INSERT INTO livres_genres (`livre_id`,`genre_id`) VALUES (?,?)";

                $query = $this->bdd->prepare($sql3);
                $query->execute([$last_id_livre,$genre]);
                
                //J'insere dans la table "livre auteur" l'id du dernier livre ajouté et, grace a la sous requete SELECT, l'id de l'auteur qui a été rentré précédemment dans la table auteur
                $sql4="INSERT INTO livre_auteur (`id_livre`,`id_auteur`) VALUES ('".$last_id_livre."',(SELECT id_auteur FROM auteurs WHERE nom_auteur LIKE '".$nom_auteur."'))";
                $this->bdd->query($sql4);

                //J'insere dans la table stock le nombre de livres que l'on ajoute
                $sql5="INSERT INTO stock (`Nombre_livre`) VALUES (?)";
                $query =$this->bdd->prepare($sql5);
                $query->execute([$_POST['quantity']]);

                header('location:index.php');

                //Comptage des lignes a l'issu de la requete inclu dans la fonction "getIsbn" SI le resultat est 0, c'est que l'ISBN n'existe pas dans la BDD donc le livre peut etre ajouté
                } elseif (count($this->getIsbn(($_POST['isbn']))) == 0) {
    
                    $sql1 = "INSERT INTO livres (`id_livre`,`titre_livre`,`isbn`,`date_parution`,`nombrePage`,`auteur_id`,`id_genre`) VALUES (NULL,'".$titreLivre."','".$isbn."','".$dateParution."','".$nombrePages."',(SELECT id_auteur FROM auteurs WHERE nom_auteur LIKE '".$nom_auteur."'),'".$_POST['genre']."')";    
                    $this->bdd->query($sql1);
    
                    $last_id_livre = $this->bdd->lastInsertId();
    
                    $sql3="INSERT INTO livres_genres (`livre_id`,`genre_id`) VALUES (?,?)";
                    $query =$this->bdd->prepare($sql3);
                    $query->execute([$last_id_livre,$genre]);
                    
                    $sql4="INSERT INTO livre_auteur (`id_livre`,`id_auteur`) VALUES ('".$last_id_livre."',(SELECT id_auteur FROM auteurs WHERE nom_auteur LIKE '".$nom_auteur."'))";
                    $this->bdd->query($sql4);
    
                    $sql5="INSERT INTO stock (`Nombre_livre`) VALUES (?)";
                    $query =$this->bdd->prepare($sql5);
                    $query->execute([$_POST['quantity']]);
                

                header('location:index.php');

                //Comptage des lignes a l'issu de la requete inclu dans la fonction "getIsbn" SI le resultat est différent de 0 , c'est que l'ISBN existe dans la BDD 
                } elseif (count($this->getIsbn(($_POST['isbn']))) != 0) {
                
                }

                //Comptage des lignes a l'issu de la requete inclu dans la fonction "getAuteursByName" SI le resultat est différent de 0, c'est que l'auteur existe deja, donc on ajoute tout sauf l'auteur
                elseif (count($this->getAuteursByName($_POST['nom_auteur'])) != 0) {
                $sql1 = "INSERT INTO livres (`id_livre`,`titre_livre`,`isbn`,`date_parution`,`nombrePage`,`auteur_id`,`id_genre`) VALUES (NULL,'".$titreLivre."','".$isbn."','".$dateParution."','".$nombrePages."',(SELECT id_auteur FROM auteurs WHERE nom_auteur LIKE '".$nom_auteur."'), '".$_POST['genre']."')";
                $this->bdd->query($sql1);

                $last_id_livre = $this->bdd->lastInsertId();
                $sql3="INSERT INTO livres_genres (`id_livre`,`id_genre`) VALUES (?,?)";
                $query = $this->bdd->prepare($sql3);
                $query->execute([$last_id_livre,$genre]);

                $sql4="INSERT INTO livre_auteur (`id_livre`,`id_auteur`) VALUES (?,?)";
                $query = $this->bdd->prepare($sql4);
                $query->execute([$last_id_livre,$nom_auteur]);

                $sql5="INSERT INTO stock (`Nombre_livre`) VALUES (?)";
                $query =$this->bdd->prepare($sql5);
                $query->execute([$_POST['quantity']]);

                header('location:index.php');

            }   
        }     
    }




    function emprunt_livre($param){


        if(isset($_POST['liste_livre_emprunt'])){

           
        
            $idlivre = $param;
            $sql="UPDATE livres SET disponibilite_id = 1 WHERE id_livre = $idlivre";
            $this->bdd->query($sql);
            $id_util = $_POST['utilisateur'];
            $sql1="INSERT INTO livre_utilisateur (`id_livre`,`id_utilisateur`) VALUES (?,?)";
            $query = $this->bdd->prepare($sql1);
            $query->execute([$idlivre, $id_util]);

            
            }
               
        }
    
        function rendu_livre($param){


            if(isset($_POST['liste_livre_rendu'])){
   
                $idlivre = $param;
                $sql="UPDATE livres SET disponibilite_id = 0 WHERE id_livre = $idlivre";
                $this->bdd->query($sql);
                $id_util = $_POST['utilisateur'];

                $sql1="DELETE FROM livre_utilisateur WHERE id_livre =  $idlivre AND id_utilisateur = $id_util";
                $this->bdd->query($sql1);

                }
                   
            }

        
    function suppr_livre($idlivre){

        $sql="DELETE FROM livres WHERE id_livre LIKE $idlivre";
        $this->bdd->query($sql);


    }

//JASON
	/* méthode qui renvoit tous les résultats sous forme de tableau
	*/
	public function getLivre() {
		$sql="SELECT image, titre_livre, isbn, shortDescription,id_livre FROM livres;";
		return $this->getResults($sql);
	}
	
	public function getDelete() {
		$sql="SELECT image, titre_livre, isbn, shortDescription,id_livre FROM livres;";
		return $this->getResults($sql);
	}
	




//DAVID
    //fonction pour ajouter des utilisateurs depuis le formulaire d'inscription:
        
    //mettre en paramètre les données stockées en POST    
    public function addUsers($nom, $prenom, $email, $pass) {                                                         
        $sql = "INSERT INTO utilisateurs (nom_utilisateur, prenom_utilisateur, mail_utilisateur, mdp_utilisateur)  
                VALUES (?, ?, ?, ?)";   // (les ? sont des paramètres) permet d'éviter les injections SQL
        
        $query = $this->bdd->prepare($sql); 
        $query->execute([$nom, $prenom, $email, $pass]);
    
        // Redirection après l'ajout de l'utilisateur
        // header('location:inscription.php');
    }
    //fonction pour ajouter des tokens dans la BDD quand l'utilisateur clique sur le bouton "mot de passe oublié":
    public function addToken($email, $token_hash, $expiry) {                                                         
        $sql = "UPDATE utilisateurs
                SET reset_token_hash= ?, 
                reset_token_expires_at= ?
                WHERE mail_utilisateur= ?";                                             
        
        $query = $this->bdd->prepare($sql); 
        $query->execute([$token_hash, $expiry, $email]);
        
    }
    // Fonction pour envoyer un e-mail de réinitialisation de mot de passe
    public function sendMail($email, $token_hash){
    $to = $email; // Destinataire
    $subject = 'Réinitialisation de votre mot de passe'; // Sujet du mail
    
    // Message du mail avec le lien de réinitialisation
    $message = 'Bonjour, vous avez demandé la réinitialisation de votre mot de passe. Veuillez cliquer sur le lien suivant : <br><br>' .
               '<a href="http://localhost/Projet%20Biblio/MdpReset.php?token=' . $token_hash . '">Je réinitialise mon mot de passe</a><br><br>';

    // Headers du mail
    $header = "From:johnwish54000@gmail.com \r\n";         //on met l'adresse mail de l'expéditeur
    $header .= "MIME-Version: 1.0\r\n";                    //on met la version MIME qui est une version de HTML 
    $header .= "Content-type: text/html\r\n";              //on met le type de contenu qui est du texte en HTML

    $ReturnValeurs = mail($to, $subject, $message, $header);

    return $ReturnValeurs;
}


      
    public function getMailMdp($query) {                //fonction pour récupérer et parcourir les emails et les mots de passe de la BDD
        $results=array();
        
        $stmt = $this->bdd->query($query);              //on exécute la requête SQL

        if (!$stmt) {                                   //si la requête ne s'exécute pas, on affiche l'erreur
            $this->error=$this->bdd->errorInfo();       //stockage de l'erreur dans la variable error
            return false;                               //on retourne false
        } else {                                        //sinon, on retourne le résultat de la requête
             // fetch uniquement PDO associative 
            return $stmt->fetch(PDO::FETCH_ASSOC);      //on retourne le résultat de la requête
        }

    }

    //fonction pour vérifier si l'email existe déjà dans la BDD:
    public function checkMail($email) {                                             //mettre en paramètre l'email stocké en POST    
        $sql = "SELECT * FROM utilisateurs WHERE mail_utilisateur = '$email'";      //requête SQL pour sélectionner l'email dans la BDD
        return $this->getMailMdp($sql);                                             //on retourne le résultat de la requête                                  
    }

   



 //PAUL 

   // fonction changment de status de la dispo dans datatable 
   public function statusDispo()
{
    // Requête SQL pour sélectionner les ID des livres et leurs statuts de disponibilité
    $sql = "SELECT id_livre, disponibilite_id FROM livres";

    // Récupérer les résultats de la requête
    $results = $this->getResults($sql);

    // Initialiser un tableau pour stocker les statuts de disponibilité des livres
    $statusArray = array();

    // Parcourir les résultats de la requête pour construire le tableau des statuts
    foreach ($results as $result) {
        // Utiliser l'ID du livre comme clé et le statut de disponibilité comme valeur dans le tableau
        $statusArray[$result['id_livre']] = $result['disponibilite_id'];
    }

    // Retourner le tableau des statuts de disponibilité
    return $statusArray;
}

//fonction pour ajouter des utilisateur
public function ajoutUtilisateur($nom, $prenom, $email) {
    
    // Utilise une requête SQL pour insérer l'utilisateur
    $query = "INSERT INTO utilisateur (nom_utilisateur, prenom_utilisateur, mail_utilisateur) 
              VALUES (:nom, :prenom, :email)";

    $params = array(
        ":nom" => $nom,
        ":prenom" => $prenom,
        ":email" => $email,
    );

    $stmt = $this->bdd->prepare($query);
    $stmt->execute($params);
}

}
?>

