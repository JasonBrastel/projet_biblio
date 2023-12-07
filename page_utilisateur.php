<?php

require_once("dao.php");
$dao = new DAO();
$dao->connexion();
$users = $dao->getUtilisateur();
$livres = $dao->getLivre();
$userLivreEmprunte = $dao->getUtilisateurLivreEmprunte();




if ($_POST) {
    if (isset($_POST['btn_add_user'])) {
        // Traitements pour le formulaire d'ajout d'utilisateur
        $nom_utilisateur = $_POST['nom_utilisateur'];
        $prenom_utilisateur = $_POST['prenom_utilisateur'];
        $mail_utilisateur = $_POST['mail_utilisateur'];
        $tel_utilisateur = $_POST['tel_utilisateur'];
        $message = $dao->ajoutUtilisateur($nom_utilisateur, $prenom_utilisateur, $mail_utilisateur, $tel_utilisateur);


        // Mise à jour du contenu de la div des messages
        echo '<script>document.getElementById("messageDiv").innerHTML = "' . $message . '";</script>';
    }
}



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des utilisateurs</title>
    
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="style/style.css">
</head>

<body>
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
<section>



<form method="POST">

<h2> Nouvelle Utilisateur </h2>

<input type="text" name="nom_utilisateur" placeholder="Nom" required />
<input type="text" name="prenom_utilisateur" placeholder="Prénom" required />
<input type="mail" name="mail_utilisateur" placeholder="Mail" required />
<input type="text" name="tel_utilisateur" placeholder="Téléphone" required />

<button name="btn_add_user" type="submit">Ajouter</button>

<div id="messageDiv">
    <?php echo isset($message) ? $message : ''; ?>
</div>

</form>


    <table id="tableUser" class="table display">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Identifiant client</th>
                <th>Fiche personnelle</th>
                <th>Supprimer</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user) { ?>
                <tr>
                    <td><?php echo $user['nom_utilisateur']; ?></td>
                    <td><?php echo $user['prenom_utilisateur']; ?></td>
                    <td><?php echo $user['identifiant_utilisateur']; ?></td>
                    <td>
                        <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#livreModal<?php echo $user['id_utilisateur']; ?>">
                            Fiche personnelle
                        </button>
                    </td>
                    <form method="POST" action="suppr.php">
                        <td>
                            <button <?php echo $user['id_utilisateur']; ?>" class="btn btn-danger" data-toggle="modal" data-target="#confirmModal">Supprimer</button>
                        </td>
                    </form>
                </tr>

                <div class="modal fade" id="livreModal<?php echo $user['id_utilisateur']; ?>" tabindex="-1" role="dialog" aria-labelledby="livreModalLabel<?php echo $user['id_utilisateur']; ?>">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title" id="livreModalLabel<?php echo $user['id_utilisateur']; ?>">
                                    <?php echo $user['nom_utilisateur'] . ' ' . $user['prenom_utilisateur']; ?>
                                </h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">

                            <?php $aucunLivreEmprunte = true; ?>
                            <?php foreach ($userLivreEmprunte as $LivreEmprunte) { ?>
                                <?php if ($LivreEmprunte['id_utilisateur'] == $user['id_utilisateur']) { ?>
                                    <p>Livre emprunté : <?php echo $LivreEmprunte['titre_livre']; ?></p>
                                    <p>date d'emprunt : <?php echo $LivreEmprunte['date_emprunt']; ?></p>
                                    <p>date de retour: <?php echo $LivreEmprunte['date_retour']; ?></p>
                                    <?php $aucunLivreEmprunte = false; ?>
                                <?php } ?>
                            <?php } ?>

                            <?php if ($aucunLivreEmprunte) { ?>
                                <p>Aucun livre emprunté</p>
                            <?php } ?>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                            </div>
                        </div>
                    </div>
                </div>

            <?php } ?>

        </tbody>
    </table>
    
      <!-- Footer -->
      <footer class="navbar navbar-expand-lg bg-dark text-white fixed-bottom">
    <div class="container-fluid d-flex justify-content-center">
        <span class="navbar-brand text-white fs-6 text"> MyBiblio - 2023 </span>
    </div>
</footer>

        
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script>
        $(document).ready(function () {
            $('#tableUser').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json',
                }
            });
        });
    </script>
</body>

</html>
