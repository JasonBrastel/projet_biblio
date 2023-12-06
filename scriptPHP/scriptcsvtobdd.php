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
    private $bdd;

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
            $this->bdd = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->database . ';charset=' . $this->charset, $this->user, $this->password);
        } catch (Exception $e) {
            // En cas d'erreur, on affiche un message et on arrête tout
            $this->error = 'Erreur : ' . $e->getMessage();
        }
    }



    function readCsv($filename)
    {
        $datas = array();
        //on ouvre le fichier en lecture
        if (($handle = fopen($filename, "r")) !== FALSE) {

            //on lit le fichier ligne par ligne
            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {


                // Ajoute chaque ligne à ton tableau
                $datas[] = $data;
            }
            fclose($handle);
        }

        return $datas;
    }


    public function insertFromCsv($csvData)
    {
        // Utilisez un indicateur pour suivre si c'est la première ligne
        $firstLine = true;

        foreach ($csvData as $data) {
            // Vérifiez si c'est la première ligne
            if ($firstLine) {
                // Passez à la ligne suivante sans effectuer d'opération
                $firstLine = false;
                continue;
            }

            $titreLivre = $data[0];
            $isbn = $data[1];
            // Extraction de la partie de la date sans le fuseau horaire et la partie de la journée
            $dateParution = date('Y-m-d', strtotime($data[3]));
            $shortDesc = $data[5];
            $longDesc = $data[6];
            $img = $data[4];
            $nbpages = $data[2];

            $genre = $data[11];

            $nomAuteur = $data[8];

            // Vérifier si l'auteur existe déjà
            $idAuteur = $this->getAuteurId($nomAuteur);

            // Si l'auteur n'existe pas, l'insérer
            if (!$idAuteur) {
                $idAuteur = $this->insertAuteur($nomAuteur);
            }

            // Insérer le livre avec l'ID de l'auteur
            $livreId = $this->insertLivre($titreLivre, $idAuteur, $isbn, $dateParution, $shortDesc, $longDesc, $img, $nbpages, $nomAuteur);

            // Insérer le genre avec l'ID du livre
            $this->insertGenre($genre, $livreId);

            // Insérer la relation livre-auteur
            $this->insertLivreAuteur($livreId, $idAuteur);
        }
    }


    public function getAuteurId($nomAuteur)
    {
        // Requête pour obtenir l'ID de l'auteur par son nom
        $query = "SELECT id_auteur FROM auteurs WHERE nom_auteur = :nomAuteur LIMIT 1";
        $stmt = $this->bdd->prepare($query);
        $stmt->bindParam(':nomAuteur', $nomAuteur, PDO::PARAM_STR);

        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);


        // Retourne l'ID de l'auteur ou null s'il n'existe pas
        return $result ? $result['id_auteur'] : null;
    }

    public function insertAuteur($nomAuteur)
    {
        // Requête pour insérer un nouveau livre avec l'ID de l'auteur
        $query = "INSERT INTO auteurs (nom_auteur) VALUES (:nomAuteur)";
        $stmt = $this->bdd->prepare($query);

        $stmt->bindParam(':nomAuteur', $nomAuteur, PDO::PARAM_STR);

        try {
            $stmt->execute();
            echo "Enregistrement Auteur inséré avec succès.\n";

            // Retourne l'ID du dernier enregistrement inséré
            return $this->bdd->lastInsertId();
        } catch (PDOException $e) {
            echo "Erreur lors de l'insertion de l'enregistrement Auteur : " . $e->getMessage() . "\n";
            return null;
        }
    }

    public function insertLivre($titreLivre, $idAuteur, $isbn, $dateParution, $shortDesc, $longDesc, $img, $nbpages, $nomAuteur)
    {
        // Requête pour insérer un nouveau livre avec l'ID de l'auteur, la date de parution et l'ISBN
        $query = "INSERT INTO livres (titre_livre, auteur_id, date_parution, isbn, shortDescription, longDescription, Image, nombrePage) VALUES (:titreLivre, :idAuteur, :dateParution, :isbn, :shortDesc, :longDesc, :img, :nbpages)";
        $stmt = $this->bdd->prepare($query);

        $stmt->bindParam(':titreLivre', $titreLivre, PDO::PARAM_STR);
        $stmt->bindParam(':idAuteur', $idAuteur, PDO::PARAM_INT);
        $stmt->bindParam(':isbn', $isbn, PDO::PARAM_STR);
        $stmt->bindValue(':dateParution', $dateParution, PDO::PARAM_STR);
        $stmt->bindParam(':shortDesc', $shortDesc, PDO::PARAM_STR);
        $stmt->bindParam(':longDesc', $longDesc, PDO::PARAM_STR);
        $stmt->bindParam(':img', $img, PDO::PARAM_STR);
        $stmt->bindParam(':nbpages', $nbpages, PDO::PARAM_STR);

        try {
            $stmt->execute();
            echo "Enregistrement Livre inséré avec succès.\n";

            // Retourne l'ID du dernier enregistrement inséré
            return $this->bdd->lastInsertId();
        } catch (PDOException $e) {
            echo "Erreur lors de l'insertion de l'enregistrement Livre : " . $e->getMessage() . "\n";
            return null;
        }
    }

    public function insertLivreAuteur($livreId, $idAuteur)
    {
        // Requête pour insérer la relation livre-auteur
        $query = "INSERT INTO livre_auteur (id_livre, id_auteur) VALUES (:livreId, :idAuteur)";
        $stmt = $this->bdd->prepare($query);
        $stmt->bindParam(':livreId', $livreId, PDO::PARAM_INT);
        $stmt->bindParam(':idAuteur', $idAuteur, PDO::PARAM_INT);

        try {
            $stmt->execute();
            echo "Relation Livre-Auteur insérée avec succès.\n";
        } catch (PDOException $e) {
            echo "Erreur lors de l'insertion de la relation Livre-Auteur : " . $e->getMessage() . "\n";
        }
    }

    public function getGenreId($nomGenre)
    {
        // Requête pour obtenir l'ID du genre par son nom
        $query = "SELECT id_genre FROM genres WHERE nom_genre = :nomGenre LIMIT 1";
        $stmt = $this->bdd->prepare($query);
        $stmt->bindParam(':nomGenre', $nomGenre, PDO::PARAM_STR);

        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Retourne l'ID du genre ou null s'il n'existe pas
        return $result ? $result['id_genre'] : null;
    }

    public function insertGenre($nomGenre, $idLivre)
    {
        // Requête pour obtenir l'ID du genre par son nom
        $idGenre = $this->getGenreId($nomGenre);

        // Si le genre n'existe pas, l'insérer
        if (!$idGenre) {
            // Requête pour insérer un nouveau genre
            $query = "INSERT INTO genres (nom_genre) VALUES (:nomGenre)";
            $stmt = $this->bdd->prepare($query);

            $stmt->bindParam(':nomGenre', $nomGenre, PDO::PARAM_STR);

            try {
                $stmt->execute();
                echo "Enregistrement Genre inséré avec succès.\n";

                // Récupère l'ID du dernier enregistrement inséré
                $idGenre = $this->bdd->lastInsertId();
            } catch (PDOException $e) {
                echo "Erreur lors de l'insertion de l'enregistrement Genre : " . $e->getMessage() . "\n";
                return null;
            }
        } else {
            echo "Le genre existe déjà.\n";
        }


        // Insérer la relation entre le genre et le livre
        $this->insertGenreLivreRelation($idGenre, $idLivre);
    }

    public function insertGenreLivreRelation($idGenre, $idLivre)
    {
        // Requête pour insérer la relation entre le genre et le livre
        $query = "INSERT INTO livres_genres (genre_id, livre_id) VALUES (:idGenre, :idLivre)";
        $stmt = $this->bdd->prepare($query);

        $stmt->bindParam(':idGenre', $idGenre, PDO::PARAM_INT);
        $stmt->bindParam(':idLivre', $idLivre, PDO::PARAM_INT);

        try {
            $stmt->execute();
            echo "Relation Genre-Livre insérée avec succès.\n";
        } catch (PDOException $e) {
            echo "Erreur lors de l'insertion de la relation Genre-Livre : " . $e->getMessage() . "\n";
        }
    }



    public function close()
    {
        $this->bdd = null;
    }
}

$dao = new DAO();
$dao->connexion();
$csvData = $dao->readCsv("books.csv");


// Insérer les données dans la table Auteurs
$dao->insertFromCsv($csvData);

// Fermez la connexion à la base de données
$dao->close();
