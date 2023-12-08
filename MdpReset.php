<?php
session_start();                                                       //on démarre la session pour pouvoir utiliser les variables de session
require_once("dao.php");                                               //on fait la jonction avec le fichier DAO

$dao = new DAO();                                                      //on crée une nouvelle instance de DAO
$dao->connexion();                                                     //on se connecte à la BDD            

$messageErrorMDP = "";                                                 //on crée une variable pour stocker les messages d'erreur si les mots de passe ne correspondent pas



function valid_donnees($donnees)
{                                         //on crée une fonction pour sécuriser les données du formulaire                        
    $donnees = htmlentities(stripslashes(trim($donnees)));                //on enlève les espaces, les antislashs et les caractères spéciaux
    return $donnees;                                                       //on retourne les données sécurisées                                           
}

$token= $_GET['token'];                                                 //on récupère le token dans l'url
$token_hash= hash('sha256', $token);                                    //on hash le token
$sql = "SELECT * FROM utilisateurs WHERE reset_token_hash = ?";
// $stmt = $dao->prepare($sql);  //FONCTIONNE PAS
$stmt->bind_param("s", $token_hash);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
                                                                       // Le token est valide, et les informations de l'utilisateur sont récupérées
    $user = $result->fetch_assoc();
} else {
                                                                       // Le token est invalide ou a expiré, gérez l'erreur (par exemple, redirigez vers une page d'erreur)
    die("Token invalide ou expiré");
}                                    

if (isset($_POST['button_validation']) && ($_SERVER['REQUEST_METHOD'] === 'POST')) {

    // Mettre à jour le mot de passe de l'utilisateur
    $new_password_hash = password_hash($_POST['pass'], PASSWORD_ARGON2ID);
    $user_id = $user['id_utilisateur'];

    $sqlUpdate = "UPDATE utilisateurs SET mdp_utilisateur = ? WHERE id_utilisateur = ?";
    // $stmtUpdate = $dao->prepare($sqlUpdate); //FONCTIONNE PAS
    $stmtUpdate->bind_param("ss", $new_password_hash, $user_id);
    
    if ($stmtUpdate->execute()) {
        // Mot de passe mis à jour avec succès

        exit();
    } else {
        // Erreur lors de la mise à jour du mot de passe
        die("Erreur lors de la mise à jour du mot de passe");
    }
}

?>
<script>
    //fonction pour montrer les mots de passe:
    function filtreMdp() {

        let pass1 = document.getElementById("pass"); //on crée une variable pour le premier mot de passe
        let pass2 = document.getElementById("pass2"); //on crée une variable pour le deuxième mot de passe

        if (pass1.type === "password" && pass2.type === "password") { //si les mots de passe sont cachés, on les affiche:
            pass1.type = "text"; //on change le type de l'input pour afficher le mot de passe
            pass2.type = "text";
        } else {
            pass1.type = "password"; //sinon, on les cache:                       
            pass2.type = "password";
        }
    }
</script>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation du mot de passe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,500,0,200" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital@1&display=swap" rel="stylesheet">
    <style>
        .boutonInsc {
            background: #DDD6C4;
        }

        .boutonInsc:hover {
            background: #BF9C72;
        }

        body {
            background-image: url('images/FondPageInscription.jpg');
            background-size: cover;
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }

        section {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .mask {
            flex: 1;
            display: flex;
            align-items: center;
        }

        footer {
            background-color: #343a40;
            /* Couleur de la navbar */
            color: white;
            text-align: center;
            padding: 10px;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg bg-dark mb-5">
        <div class="container-fluid">
            <a class="navbar-brand text-white" >MyBiblio</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse " id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active text-white" aria-current="page" ></a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link text-white" ></a>
                    </li>
                </ul>

                <a style="color:white;" href="inscription.php">Inscription</a>

            </div>
        </div>
    </nav>

    <div class="mask d-flex align-items-center h-100 gradient-custom-3">

<div class="container">

    <div class="row d-flex justify-content-center align-items-center h-100">

        <div class="col-12 col-md-9 col-lg-7 col-xl-6">
            <div class="card shadow-lg p-3 mb-5 bg-body rounded" style="border-radius: 15px;">

                <div class="card-body p-5">

                    <h2 style="font-family: 'Poppins', sans-serif; " class="text-uppercase text-center mb-5 fw-bolder">Réinitialisation du mot de passe</h2>

                    
                        <!-- formulaire pour réinitialiser le mot de passe: -->
                    <form method="POST">

                        <div class="col-auto mb-5">
                            <div class="input-group">
                                <div style="border: none;" class="input-group-text"><span class="material-symbols-outlined">key</span></div>
                                <input style="border: none;" type="password" class="form-control" id="pass" name="pass" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}" title="Votre mot de passe doit contenir au moins un chiffre, une majuscule, une minuscule et au moins 6 caractères" placeholder="Nouveau mot de passe" required>
                            </div>
                            <!-- on affiche le message d'erreur si les mots de passe ne correspondent pas: -->
                            <span style="color:red;"><?php echo  $messageErrorMDP  ?></span>
                        </div>

                        <div class="col-auto mb-5">
                            <div class="input-group">
                                <div style="border: none;" class="input-group-text"><span class="material-symbols-outlined">lock</span></div>
                                <input style="border: none;" type="password" class="form-control" id="pass2" name="pass2" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}" title="Veuillez confirmer votre mot de passe" placeholder="Confirmez le mot de passe" required>
                            </div>
                             <!-- on affiche le message d'erreur si les mots de passe ne correspondent pas: -->
                            <span style="color:red;"><?php echo  $messageErrorMDP  ?></span> <br>
                            <input type="checkbox" class="mt-5 ms-3" onclick="filtreMdp()"> Afficher les mots de passe
                        </div>

                        

                        <!-- bouton pour valider le changement de mdp: -->

                        <div class=" d-flex justify-content-center mt-3">
                            <button type="submit" name="button_validation" class="boutonInsc btn btn btn-lg gradient-custom-4 text-body  ">Valider</button>
                        </div>
                        <!-- lien pour se connecter si on a déjà un compte: -->
                        <p class="text-center text-muted mt-4 mb-0">Vous avez déjà un compte ? <a href="index.php" class="fw-bold text-body"><u>Se connecter</u></a></p>

                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
</div>
</section>

<!-- Footer -->
<footer class="navbar navbar-expand-lg bg-dark text-white mt-5 ">
<div class="container-fluid d-flex justify-content-center ">
<span class="navbar-brand text-white fs-6 text"> MyBiblio - 2023  </span>
</div>
</footer>















    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>

</html>