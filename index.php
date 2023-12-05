<?php

require_once("dao.php");

$dao = new DAO();
$dao->connexion();
$livres = $dao->getLivre();



$dao->disconnect();

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des livres</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    
    
   
</head>

<body>

    <table id="example" class="display">
        <thead>
            <tr>
                <th>Image</th>
                <th>Titre</th>
                <th>ISBN</th>
                <th>Résumé</th>
                <th>Action</th>
                <th>Bouton</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($livres as $livre) { ?>
 
        <tr id="<?php print $livre['id_livre']?>">
            <td><img src="<?php echo $livre['image']; ?>" alt="Image du livre"></td>
            <td><?php echo $livre['titre_livre']; ?></td>
            <td><?php echo $livre['isbn']; ?></td>
            <td><?php echo $livre['shortDescription']; ?></td>
            <td><button class="btn btn-info" data-toggle="modal" data-target="#livreModal<?php echo $livre['id_livre']; ?>">Voir détails</button></td>

            <form method="POST" action="suppr.php">
            <td><button id="btn_suppr" type="submit" name="btn_suppr"  
            value="<?php echo $livre['id_livre']; ?>" class="btn btn-danger" data-toggle="modal" data-target="#confirmModal">Supprimer</button>
            </td>
            </form>
            </tr>
       

            <!-- Modal -->
            <div class="modal fade" id="livreModal<?php echo $livre['id_livre']; ?>" tabindex="-1" role="dialog" aria-labelledby="livreModalLabel<?php echo $livre['id_livre']; ?>">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        
                        <div class="modal-header">
                            <h1 class="modal-title" id="livreModalLabel<?php echo $livre['id_livre']; ?>"><?php echo $livre['titre_livre']; ?></h1>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <div class="modal-body">
                            <img src="<?php echo $livre['image']; ?>" alt="Image du livre">
                            <p>ISBN: <?php echo $livre['isbn']; ?></p>
                            <p>Description: <?php echo $livre['shortDescription']; ?></p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                        </div>
                    </div>
                </div>
            </div>

<div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="modal-title" id="confirmModalLabel">Confirmation de suppression</h3>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer cet élément ?</p>
            </div>
            <div class="modal-footer">
                <a href="#" class="btn btn-default" id="confirmModalNo">Non</a>
                <a href="#" class="btn btn-primary" id="confirmModalYes">Oui</a>
            </div>
        </div>
    </div>
</div>
</div>



        <?php } ?>
        </tbody>
    </table>


    <script src="./script/script.js" > </script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

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




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <title>Document</title>
</head>

<header>
</header>

<!-- PARTIE BODY  -->
<body>


<!-- PARTIE FORMULAIRE -->
<form>

</form>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>

<footer>


</footer>
</html>

