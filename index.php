<?php
session_start();
require_once("dao.php");
$dao = new DAO();
$dao->connexion();
$livres = $dao->getLivre();

// Récupère les statuts de disponibilité des livres
$dispoStatus = $dao->statusDispo();

$id_livre = $dao->get_livre();
$liste_utilisateur = $dao->getUtilisateur();




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





    <nav class="navbar navbar-expand-lg bg-dark mb-5">
        <div class="container-fluid">
            <a class="navbar-brand text-white" href="LoginPage.php">MyBiblio</a>

            <div class="collapse navbar-collapse " id="navbarSupportedContent">

                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <?php if (isset($_SESSION['email']) == true) { ?>
                        <li class="nav-item">
                            <a class="nav-link active text-white" aria-current="page" href="#">Membres</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link text-secondary" href="#">Livres</a>
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

    <div class="container mt-3 ">
        <table id="example" class="table ">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Titre</th>
                    <th>ISBN</th>
                    <th>Résumé</th>
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
                        <td class="dispo-col"><?php echo $livre['shortDescription']; ?></td>

                        <td class="dispo-col">
                            <!-- Bouton détails -->
                            <button class="btn btn-info details-btn " data-bs-toggle="modal" data-bs-target="#livreModal<?php echo $livre['id_livre']; ?>">Voir détails</button>
                            <!-- Bouton supprimer -->
                            <form method="POST" action="suppr.php">
                                <button id="btn_suppr" type="submit" name="btn_suppr" value="<?php echo $livre['id_livre']; ?>" class="btn btn-danger details-btn" data-bs-toggle="modal" data-bs-target="#confirmModal">Supprimer</button>
                            </form>
                        </td>

                        <td class="<?php echo $dispoStatus[$livre['id_livre']] == 0 ? 'dispo' : 'non-dispo'; ?> dispo-col">
                            <?php echo $dispoStatus[$livre['id_livre']] == 0 ? 'Disponible' : 'Pas disponible'; ?>
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
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    <h3 class="modal-title" id="confirmModalLabel">Confirmation de suppression</h3>
                                </div>
                                <div class="modal-body">
                                    <p>Êtes-vous sûr de vouloir supprimer cet élément ?</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Non</button>
                                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Oui</button>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php } ?>
            </tbody>
        </table>

        <section>


            <form method="POST" action="emprunt.php">
                <select name="utilisateur">
                    <?php foreach ($liste_utilisateur as $utilisateur) { ?>
                        <option value="<?php print $utilisateur["id_utilisateur"] ?>"><?php print $utilisateur["nom_utilisateur"];
                                                                                        print " ";
                                                                                        print $utilisateur["prenom_utilisateur"] ?> </option>
                    <?php } ?>
                </select>
                <input type="text" list="choix_livre_emprunt" name="liste_livre">
                <datalist id="choix_livre_emprunt">
                    <?php foreach ($id_livre as $book) {
                        if ($book['disponibilite_id'] == 0) { ?>
                            <option value="<?php print $book['id_livre'] ?>"><?php print $book['titre_livre'] ?></option>
                    <?php  }
                    } ?>

                </datalist>
                <button type="submit" id="btn_emprunt" name="btn_emprunt">Valider l'emprunt</button>

            </form>




            <form method="POST" action="rendu.php">
                <select name="utilisateur">
                    <?php foreach ($liste_utilisateur as $utilisateur) { ?>
                        <option value="<?php print $utilisateur["id_utilisateur"] ?>"><?php print $utilisateur["nom_utilisateur"];
                                                                                        print " ";
                                                                                        print $utilisateur["prenom_utilisateur"] ?> </option>
                    <?php } ?>
                </select>


                <input type="text" list="choix_livre_rendu" name="liste_livre">
                <datalist id="choix_livre_rendu">
                    <?php foreach ($id_livre as $book) {
                        if ($book['disponibilite_id'] == 1) { ?>
                            <option value="<?php print $book['id_livre'] ?>"><?php print $book['titre_livre'] ?></option>
                    <?php  } else {
                        }
                    } ?>

                </datalist>
                <button type="submit" id="btn_rendu" name="btn_rendu">Valider le retour</button>

            </form>


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
                var theHREF;

                $(".confirmModalLink").click(function(e) {
                    e.preventDefault();
                    theHREF = $(this).attr("href");
                    $("#confirmModal").modal("show");
                });

                $("#confirmModalNo").click(function(e) {
                    $("#confirmModal").modal("hide");
                });

                $("#confirmModalYes").click(function(e) {
                    window.location.href = theHREF;
                });
            });
        </script>
        <?php $dao->disconnect(); ?>
    </div>

</body>

</html>