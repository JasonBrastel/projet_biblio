<?php


class DAO
{

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

}


?>