<?php

require_once("dao.php");

$dao = new DAO();
$dao->connexion();
$livres = $dao->getLivre();

// Récupère les statuts de disponibilité des livres
$dispoStatus = $dao->statusDispo();


$dao->disconnect();

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des livres</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="style/style.css">
</head>

<body>

    <table id="example" class="table">
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
                    <td><?php echo $livre['titre_livre']; ?></td>
                    <td><?php echo $livre['isbn']; ?></td>
                    <td><?php echo $livre['shortDescription']; ?></td>

                    <td>
                        <!-- Bouton détails -->
                        <button class="btn btn-info details-btn "  data-bs-toggle="modal" data-bs-target="#livreModal<?php echo $livre['id_livre']; ?>">Voir détails</button>
                        <!-- Bouton supprimer -->
                        <form method="POST" action="suppr.php">
                            <button id="btn_suppr" type="submit" name="btn_suppr" value="<?php echo $livre['id_livre']; ?>" class="btn btn-danger details-btn" data-bs-toggle="modal" data-bs-target="#confirmModal">Supprimer</button>
                        </form>
                    </td>

                    <td class="<?php echo $dispoStatus[$livre['id_livre']] == 0 ? 'dispo' : 'non-dispo'; ?>">
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

</body>

</html>