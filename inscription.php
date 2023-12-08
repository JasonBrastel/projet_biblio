<?php
ob_start();

session_start();                                                          //on démarre la session pour pouvoir utiliser les variables de session
require_once("dao.php");
if (isset($_SESSION['email']) == true) {
    header('location: page_livre.php');                                      
}                                       
$dao = new DAO();                                                         //on crée une nouvelle instance de DAO
$dao->connexion();                                                        //on se connecte à la BDD

$messageErrorMail = "";                                                   // on crée une variable pour afficher les messages d'erreur si l'email est déjà utilisé
$messageInvalidMail = "";                                                 // on crée une variable pour afficher les messages d'erreur si l'email n'est pas valide
$messageErrorMDP = "";                                                    // on crée une variable pour afficher les messages d'erreur pour les mdp

$LongueurMaxPrenom = 40;                                                  // on crée une variable pour la longueur maximale du prénom
$messageInvalidPrenom = "";                                               // on crée une variable pour afficher les messages d'erreur si le prénom est trop long ou s'il contient des chiffres

$LongueurMaxNom = 40;                                                     // on crée une variable pour la longueur maximale du nom
$messageInvalidNom = "";                                                  // on crée une variable pour afficher les messages d'erreur si le nom est trop long ou s'il contient des chiffres

$succes = "";                                                             // on crée une variable pour afficher un message de validation de l'inscription

$pattern = "/^[a-zA-ZÀ-ÖØ-öø-ÿ \'-]+$/";                                  // on crée une variable pour vérifier que le nom et le prénom ne contiennent pas de chiffres


function valid_donnees($donnees)
{                                         //on crée une fonction pour sécuriser les données du formulaire                        
    $donnees = htmlentities(stripslashes(trim($donnees)));                //on enlève les espaces, les antislashs et les caractères spéciaux
    return $donnees;                                                       //on retourne les données sécurisées                                           
}


// Si les données ont été écrites, et que l'on appuie sur le bouton pour s'enregistrer:
if (isset($_POST['button_register']) && ($_SERVER['REQUEST_METHOD'] === 'POST')) {   //on vérifie aussi que la méthode utilisée est bien POST


    $mail = $dao->checkMail(valid_donnees($_POST['email']));                         //on vérifie si l'email existe déjà dans la BDD en utilisant la fonction checkMail
    //création de variables pour stocker chaque donnée du formulaire:
    $nom = valid_donnees($_POST['nom']);                                             //on sécurise les données du formulaire en utilisant la fonction valid_donnees
    $prenom = valid_donnees($_POST['prenom']);
    $email = valid_donnees($_POST['email']);
    $pass = valid_donnees(password_hash($_POST['pass'], PASSWORD_ARGON2ID));         //hashage du mot de passe pour plus de sécurité (on utilise l'algorithme ARGON2ID)




    if ($mail && $mail['mail_utilisateur'] == $email) {                                                  //si l'email existe déjà dans la BDD, on affiche un message d'erreur:
        $messageErrorMail = "Cet email est déjà utilisé.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {                                              //si l'email n'est pas valide, on affiche un message d'erreur:
        $messageInvalidMail = "L'email n'est pas valide. Assurez-vous d'indiquer une adresse email valide.";
    } elseif (strlen($nom) > $LongueurMaxNom) {                                                          //si le nom est trop long, on affiche un message d'erreur:
        $messageInvalidNom = "Le nom ne doit pas dépasser 40 caractères.";
    } elseif (strlen($prenom) > $LongueurMaxPrenom) {                                                    //si le prénom est trop long, on affiche un message d'erreur:                     
        $messageInvalidPrenom = "Le prénom ne doit pas dépasser 40 caractères.";
    } elseif (!preg_match($pattern, $nom)) {                                                             //si le nom contient des chiffres, on affiche un message d'erreur:
        $messageInvalidNom = "Le nom ne doit pas contenir de chiffres.";
    } elseif (!preg_match($pattern, $prenom)) {                                                          //si le prénom contient des chiffres, on affiche un message d'erreur:
        $messageInvalidPrenom = "Le prénom ne doit pas contenir de chiffres.";
    } else {
        if ($_POST['pass'] == $_POST['pass2']) {                                                        // on vérifie que les mots de passe lors de l'inscription correspondent :
            $dao->addUsers($nom, $prenom, $email, $pass);                                               // si oui, on ajoute l'utilisateur dans la BDD en utilisant la fonction addUsers
            $succes = "✔ Votre inscription a bien été validée";                                          // on affiche un message de validation de l'inscription                                            

        } else {                                                                                        // sinon, on affiche un message d'erreur:                                       
            $messageErrorMDP = "Les mots de passe ne correspondent pas.";
        }
    }
}
ob_end_flush();
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
    <title>Inscription</title>
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
        }
        </style>



</head>

<body>




    <!-- dans le body, on met le formulaire d'inscription: -->
    <section >


        <nav class="navbar navbar-expand-lg bg-dark mb-5">
            <div class="container-fluid">
                <a class="navbar-brand text-white" href="#">MyBiblio</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse " id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link active text-white" aria-current="page" href="#"></a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link text-white" href="#"></a>
                        </li>
                    </ul>

                   <a style="color:white;" href="index.php">se connecter</a>

                </div>
            </div>
        </nav>

        <div class="mask d-flex align-items-center h-100 gradient-custom-3">

            <div class="container">

                <div class="row d-flex justify-content-center align-items-center h-100">

                    <div class="col-12 col-md-9 col-lg-7 col-xl-6">
                        <div class="card shadow-lg p-3 mb-5 bg-body rounded" style="border-radius: 15px;">

                            <div class="card-body p-5">

                                <h2 style="font-family: 'Poppins', sans-serif; " class="text-uppercase text-center mb-5 fw-bolder">Inscription</h2>

                                <!-- formulaire d'inscription: -->

                                <form method="POST">

                                    <!-- on crée un input pour chaque donnée du formulaire -->



                                    <div class="col-auto mb-5">
                                        <div class="input-group">
                                            <div style="border: none;" class="input-group-text"><span class="material-symbols-outlined">person</span></div>
                                            <input style="border: none;" type="text" class="form-control" id="nom" name="nom" placeholder="Nom" title="Veuillez indiquer votre nom" required pattern="^[a-zA-ZÀ-ÖØ-öø-ÿ \'-]+$" maxlength=`$LongueurMaxNom`>
                                            <!--on ajoute la longueur maximale du nom et on vérifie que le nom ne contient pas de chiffres-->
                                        </div>
                                        <span style="color:red;"><?php echo $messageInvalidNom ?></span>



                                    </div>

                                    <div class="col-auto mb-5">
                                        <div class="input-group">
                                            <div style="border: none;" class="input-group-text"><span class="material-symbols-outlined">badge</span></div>
                                            <input style="border: none;" type="text" class="form-control" id="prenom" name="prenom" placeholder="Prénom" title="Veuillez indiquer votre prénom" required pattern="^[a-zA-ZÀ-ÖØ-öø-ÿ \'-]+$" maxlength=`$LongueurMaxPrenom`>
                                        </div>
                                        <!-- on affiche le message d'erreur si le prénom est trop long: -->
                                        <span style="color:red;"><?php echo $messageInvalidPrenom ?></span>
                                    </div>

                                    <div class="col-auto mb-5">
                                        <div class="input-group">
                                            <div style="border: none;" class="input-group-text"><span class="material-symbols-outlined">mail</span></div>
                                            <input style="border: none;" type="email" class="form-control" id="email" name="email" title="Veuillez indiquer votre adresse mail" placeholder="Email" required>
                                        </div>
                                        <!-- on affiche le message d'erreur si l'email existe déjà dans la BDD: -->
                                        <span style="color:red;"><?php echo $messageErrorMail  ?></span>
                                        <span style="color:red;"><?php echo $messageInvalidMail ?></span>
                                    </div>

                                    <div class="col-auto mb-5">
                                        <div class="input-group">
                                            <div style="border: none;" class="input-group-text"><span class="material-symbols-outlined">key</span></div>
                                            <input style="border: none;" type="password" class="form-control" id="pass" name="pass" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}" title="Votre mot de passe doit contenir au moins un chiffre, une majuscule, une minuscule et au moins 6 caractères" placeholder="Mot de passe" required>
                                        </div>
                                        <!-- on affiche le message d'erreur si les mots de passe ne correspondent pas: -->
                                        <span style="color:red;"><?php echo  $messageErrorMDP  ?></span>
                                    </div>

                                    <div class="col-auto mb-5">
                                        <div class="input-group">
                                            <div style="border: none;" class="input-group-text"><span class="material-symbols-outlined">lock</span></div>
                                            <input style="border: none;" type="password" class="form-control" id="pass2" name="pass2" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}" title="Veuillez confirmer votre mot de passe" placeholder="Confirmez le mot de passe" required>
                                        </div>
                                        <span style="color:red;"><?php echo  $messageErrorMDP  ?></span> <br>
                                        <input type="checkbox" class="mt-5 ms-3" onclick="filtreMdp()"> Afficher les mots de passe
                                        <!-- on affiche le message d'erreur si les mots de passe ne correspondent pas: -->
                                    </div>

                                    <div class="d-flex justify-content-center" style="color:green;" id="messValidInscrip">
                                        <?php
                                        if ($succes) {
                                            print $succes;
                                        }
                                        ?>
                                    </div>



                                    <!-- bouton pour s'inscrire: -->

                                    <div class=" d-flex justify-content-center mt-3">
                                        <button type="submit" name="button_register" class="boutonInsc btn btn btn-lg gradient-custom-4 text-body  ">S'inscrire</button>
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