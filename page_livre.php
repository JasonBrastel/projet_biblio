<?php
session_start();                                    //on démarre la session pour pouvoir utiliser les variables de session
if (! isset($_SESSION['email'])) {                  //si la variable de session n'existe pas ( si l'utilisateur n'est pas connecté)
    header('Location: index.php');              //on redirige vers la page de connexion
}
require_once("dao.php");

$dao = new DAO();
$dao->connexion();
$livres = $dao->getLivre();
$userLivreEmprunte = $dao->getLivreEmprunteParUser();

// Récupère les statuts de disponibilité des livres
$dispoStatus = $dao->statusDispo();

$id_livre = $dao->get_livre();
$liste_utilisateur = $dao->getUtilisateur();

if ($_POST) {
    $dao->ajoutLivre();
    $dao->getAuteursbyName($_POST['nom_auteur']);
    $dao->getGenreByName($_POST['genre']);
    
    $dao->getIsbn($_POST['isbn']);
   

}
$selectGenre = $dao-> getGenre();
$selectAuteur = $dao-> getAuteurDatalist();


?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des livres</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="style/style.css">
</head>


<body>

    <nav class="navbar navbar-expand-lg bg-dark mb-5">
        <div class="container-fluid">
            <a class="navbar-brand text-white" href="">MyBiblio</a>

            <div class="collapse navbar-collapse " id="navbarSupportedContent">

                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <?php if (isset($_SESSION['email']) == true) { ?>
                        <li class="nav-item">
                            <a class="nav-link active text-white" aria-current="page" href="page_utilisateur.php">Membres</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link text-secondary" href="page_livre.php">Livres</a>
                        </li>
                    <?php } ?>
                </ul>

                <?php if (isset($_SESSION['email']) == false) { ?>
                    <a style="color:white;" href="inscription.php">Inscription</a>
                <?php } else { ?>
                    <a style="color:red;" class="d-flex justify-content-center " title="Cliquez ici pour vous déconnecter" href='deco.php'>Déconnexion</a>

                <?php } ?>
            </div>
        </div>
    </nav>

    <section class="container mt-5">
    <h1 class="text-center mb-4">Ajout de livres :</h1>
    <form method="POST">
        <div class="row mb-3">
            <div class="col-md-3">
                <input type="text" name="titre_livre" class="form-control" placeholder="Titre du livre" required />
            </div>
            <div class="col-md-3">
                <input type="text" name="isbn" class="form-control" placeholder="ISBN" required />
            </div>
            <div class="col-md-3">
                <input type="text" list="choix_auteur" name="nom_auteur" class="form-control" placeholder="Nom de l'auteur" required />
                <datalist id="choix_auteur">
                    <?php foreach ($selectAuteur as $row) { ?>
                        <option value="<?php print $row['nom_auteur']; ?>"><?php print $row['nom_auteur']; ?></option>
                    <?php } ?>
                </datalist>
            </div>
            <div class="col-md-3">
                <input type="date" name="date_parution" class="form-control" required />
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-3">
                <input type="text" name="nombrePage" class="form-control" placeholder="Nombre de pages" required />
            </div>
            <div class="col-md-3">
                <input type="text" name="long_description" class="form-control" placeholder="Description longue" />
            </div>
            <div class="col-md-3">
                <input type="text" name="short_description" class="form-control" placeholder="Description courte" />
            </div>
            <div class="col-md-3">
                <select name="genre" class="form-control">
                    <?php foreach ($selectGenre as $livre) { ?>
                        <option value="<?php print $livre["id_genre"] ?>"><?php print $livre["nom_genre"] ?> </option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 text-center mt-2">
                <button class="btn btn-dark " name="btn_ajouter" type="submit" >Ajouter</button>
            </div>
        </div>

    </form>
</section>
</section>
    <div class="container mt-3 ">

        <table id="example" class="table ">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Titre</th>
                    <th>ISBN</th>
                    <th>Genre</th>
                    <th>Auteur</th>
                    <th>Bouton</th>
                    <th>Disponibilité</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($livres as $livre) { ?>

                    <tr id="<?php print $livre['id_livre'] ?>">
                        <td><img src="<?php echo $livre['image']; ?>" alt="Image du livre"></td>
                        <td class="dispo-col"><?php echo $livre['titre_livre']; ?></td>
                        <td class="dispo-col"><?php echo $livre['isbn']; ?></td>
                        <td class="dispo-col"><?php echo $livre['nom_genre']; ?></td>
                        <td class="dispo-col"><?php echo $livre['nom_auteur']; ?></td>

                        <td class="dispo-col">
                            <!-- Bouton détails -->
                            <button class="btn btn-secondary details-btn " data-bs-toggle="modal" data-bs-target="#livreModal<?php echo $livre['id_livre']; ?>">Voir détails</button>
                            <!-- Bouton supprimer -->
                            <form method="POST" action="suppr.php">
                                <button id="btn_suppr" type="submit" name="btn_suppr" value="<?php echo $livre['id_livre']; ?>" class="btn btn-dark details-btn" data-bs-toggle="modal" data-bs-target="#confirmModal">Supprimer</button>
                            </form>
                        </td>

                        <td class="<?php echo $dispoStatus[$livre['id_livre']] == 0 ? 'dispo' : 'non-dispo'; ?> dispo-col">
                   <span class="<?php echo $dispoStatus[$livre['id_livre']] == 0 ? 'text-success fw-bold' : 'text-danger fw-bold'; ?>">
                            <?php echo $dispoStatus[$livre['id_livre']] == 0 ? 'Disponible' : 'Pas disponible'; ?>
                   </span>
  </td>


                    </tr>

                    <!-- Modal -->
                    <div class="modal fade" id="livreModal<?php echo $livre['id_livre']; ?>" tabindex="-1" aria-labelledby="livreModalLabel<?php echo $livre['id_livre']; ?>">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title" id="livreModalLabel<?php echo $livre['id_livre']; ?>"><?php echo $livre['titre_livre']; ?></h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <img src="<?php echo $livre['image']; ?>" alt="Image du livre">
                                    <p>ISBN: <?php echo $livre['isbn']; ?></p>
                                    <p>Description: <?php echo $livre['shortDescription']; ?></p>
                                

                                <?php $aucunLivreEmprunte = true; ?>
                                
                                <?php foreach ($userLivreEmprunte as $LivreEmprunte) { ?> 
                                   
                                    <?php if ($LivreEmprunte['id_livre'] == $livre['id_livre']) { ?>
                                        <p>Livre emprunté par : <?php echo $LivreEmprunte['nom_utilisateur']. ' ' .$LivreEmprunte['prenom_utilisateur']; ?></p>
                                       
                                    <?php } ?>
                                <?php } ?>
                                
                                <?php $aucunLivreEmprunte = false; ?>
                                <?php if ($aucunLivreEmprunte) { ?> 
                                     <p>Aucun livre emprunté</p>
                                   
                                <?php } ?>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php } ?>
            </tbody>
        </table>

  

</div>

<section class="container mt-5">
    <div class="row">
        <article class="col-md-6">
            <form method="POST" action="emprunt.php" class="d-flex justify-content-center align-items-center flex-column">
                <select name="utilisateur" class="form-select mb-3">

                    <?php foreach ($liste_utilisateur as $utilisateur) { ?>
                        <option value="<?php print $utilisateur["id_utilisateur"] ?>"><?php print $utilisateur["nom_utilisateur"];
                                                                                        print " ";
                                                                                        print $utilisateur["prenom_utilisateur"] ?> </option>
                    <?php } ?>
                </select>



                <input type="text" list="choix_livre_emprunt" name="liste_livre_emprunt" class="form-control mb-3" placeholder="Titre du livre"required>


                <datalist id="choix_livre_emprunt">
                    <?php foreach ($id_livre as $book) {
                        if ($book['disponibilite_id'] == 0) { ?>
                            <option value="<?php print $book['titre_livre'] ?>"><?php print $book['titre_livre'] ?></option>

                    <?php  }
                    } ?>
                </datalist>

                
                <button type="submit" id="btn_emprunt" name="btn_emprunt" class="btn btn-dark">Valider l'emprunt</button>
           
                 </form>
        </article>

        <article class="col-md-6">
            <form method="POST" action="rendu.php" class="d-flex justify-content-center align-items-center flex-column">
                <select name="utilisateur" class="form-select mb-3">

                    <?php foreach ($liste_utilisateur as $utilisateur) { ?>
                        <option value="<?php print $utilisateur["id_utilisateur"] ?>"><?php print $utilisateur["nom_utilisateur"];
                                                                                        print " ";
                                                                                        print $utilisateur["prenom_utilisateur"] ?> </option>
                    <?php } ?>
                </select>


                <input type="text" list="choix_livre_rendu" name="liste_livre_rendu" class="form-control mb-3" placeholder="Titre du livre" required>


                <datalist id="choix_livre_rendu">
                    <?php foreach ($id_livre as $book) {
                        if ($book['disponibilite_id'] == 1) { ?>
                            <option value="<?php print $book['titre_livre'] ?>"><?php print $book['titre_livre'] ?></option>
                    <?php  }

                    } ?>
                </datalist>

                <button type="submit" id="btn_rendu" name="btn_rendu" class="btn btn-dark">Valider le retour</button>
            </form>
        </article>
    </div>
</section>
      
      <!-- Footer -->
       <footer class="navbar navbar-expand-lg bg-dark text-white mt-5 ">
           <div class="container-fluid d-flex justify-content-center ">
            <span class="navbar-brand text-white fs-6 text"> MyBiblio - 2023 </span>
            </div>
        </footer>


        <script src="./script/script.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

        <script>
            $(document).ready(function() {
                $('#example').DataTable({
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json',
                    }
                });
            });
        </script>
        <?php $dao->disconnect(); ?>


</body>


</html>