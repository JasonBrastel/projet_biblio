<?php
//on fait la jonction avec le fichier DAO
require_once("../csvtobdd.php");
$dao = new DAO();
$dao->connexion();
// var_dump($_POST);
// var_dump($_POST['nom_auteur']);
if ($_POST) {
    $dao->ajoutLivre();
    $dao->getAuteursbyName($_POST['nom_auteur']);
$dao->getGenreByName($_POST['genre']);
var_dump($dao->getAuteursbyName($_POST['nom_auteur']));
}



?>



<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajout de livres</title>
</head>

<header>


<h1> AJOUT DE LIVRES </h1>

</header>

<body>



<form method="POST">


<input type="text" name="titre_livre" placeholder="Titre du livre" />
<input type="text" name="nom_auteur" placeholder="Nom de l'auteur" />
<input type="text" name="date_parution" placeholder="Date de parution" />
<input type="text" name="nombrePage" placeholder="Nombre de pages" />
<input type="text" name="long_description" placeholder="Description longue" />
<input type="text" name="short_description" placeholder="Description courte" />
<input type="text" name="genre" placeholder="Genre" />


<button name="btn_ajouter" type="submit">Ajouter</button>

</form>


</body>

<footer>





</footer>
</html>