<?php

class DAO {
	/* Paramètres de connexion à la base de données 
	Dans l'idéal, il faudrait écrire les getters et setters correspondants pour pouvoir en modifier les valeurs
	au cas où notre serveur change
	*/
	
	private $host="127.0.0.1";
	private $user="root";
	private $password="";
	private $database="biblio";
	private $charset="utf8";
	
	//instance courante de la connexion
	private $bdd;
	
	//stockage de l'erreur éventuelle du serveur mysql
	private $error;
	
	public function __construct() {
	
	}
	
	/* méthode de connexion à la base de donnée */
	public function connexion() {
		
		try
		{
			// On se connecte à MySQL
			$this->bdd = new PDO('mysql:host='.$this->host.';dbname='.$this->database.';charset='.$this->charset, $this->user, $this->password);
		}
		catch(Exception $e)  //Capture avec la classe Exception  la variable $e permet d'obtenir la proprieté de la classe ???? telle que le méssage d'érreur
		{
			// En cas d'erreur, on affiche un message et on arrête tout
				$this->error='Erreur : '.$e->getMessage();
		}
	}
	
	/* méthode qui renvoit tous les résultats sous forme de tableau de la requête passée en paramètre */
	public function getResults($query) {
		$results=array();
		
		$stmt = $this->bdd->query($query);
		
		if (!$stmt) {
			$this->error=$this->bdd->errorInfo();
			return false;
		} else {
			return $stmt->fetchAll();
		}
		
	}




	
	
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
	
	

	

	/* méthode pour fermer la connexion à la base de données */
	public function disconnect() {
		$this->bdd=null;
	}
	
	/* méthode pour récupérer la dernière erreur fournie par le serveur mysql */
	public function getLastError() {
		return $this->error;
	}
	
}

?>
