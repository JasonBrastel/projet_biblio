<script>
    //fonction pour montrer les mots de passe:
    function filtreMdp() {

        let pass1 = document.getElementById("pass");

        if (pass1.type === "password") { //si le type du mot de passe est "password" (donc caché), 
            pass1.type = "text"; //on le change en "text" (donc visible)                 
        } else {
            pass1.type = "password";
        } //sinon, on le change en "password" (donc caché)

    }
</script>

<?php
session_start();                                                          //on démarre la session pour pouvoir utiliser les variables de session
require_once("dao.php");                                             //on fait la jonction avec le fichier DAO
$dao = new DAO();                                                         //on crée une nouvelle instance de DAO
$dao->connexion();                                                        //on se connecte à la BDD

$IdentifiantsErr="";                                                      //on crée une variable pour stocker les messages d'erreur si les identifiants sont incorrects
$IdentifiantsInexistant="";                                               //on crée une variable pour stocker les messages d'erreur si les identifiants n'existent pas

function valid_donnees($donnees)
{                                         //on crée une fonction pour sécuriser les données du formulaire                        
    $donnees = htmlentities(stripslashes(trim($donnees)));                //on enlève les espaces, les antislashs et les caractères spéciaux
    return $donnees;                                                       //on retourne les données sécurisées                                           
}

if (isset($_POST['button_register']) && (($_SERVER['REQUEST_METHOD'] === 'POST'))) {   //si on clique sur le bouton "se connecter" et que la méthode utilisée est POST                          
    $email = valid_donnees($_POST['email']);                              //on sécurise les données du formulaire
    $pass = valid_donnees($_POST['pass']);                                //on sécurise les données du formulaire

    $result = $dao->checkMail(valid_donnees($_POST['email']));          //on stocke le résultat de la fonction checkMail dans une variable

    if ($result > 0) {                                                  //si l'email existe dans la BDD c'est-à-dire si le résultat de la fonction checkMail est supérieur à 0
        if ($email == $result['mail_utilisateur'] && password_verify($pass, $result['mdp_utilisateur'])) {
            if ($result['type_utilisateur'] != 1) {                    //si l'utilisateur n'a pas les droits nécessaires pour se connecter
                $IdentifiantsErr = "<div class='text-center'>Vous n'avez pas les droits nécessaires pour vous connecter.<br>Veuillez contacter l'administrateur.</div>";
            } else {
                // Continuer le processus de connexion car l'utilisateur a les droits nécessaires
                $_SESSION['email'] = $email;                         //on stocke l'email dans une variable de session   
                header('location: index.php');                       //on redirige l'utilisateur vers la page d'accueil
            }
        } else {
            $IdentifiantsErr = "Vos identifiants sont incorrects. Veuillez réessayer.";
        }
    } else {
        $IdentifiantsInexistant = "Cet email n'existe pas. Veuillez vous inscrire.";
    }
}







?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
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

    <section>

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
                            <a class="nav-link text-white" href="index.php">Livres</a>
                        </li>
                    <?php } ?>
                    </ul>
                
                    <?php if (isset($_SESSION['email']) == false) { ?>
                   <a style="color:white;" href="inscription.php">Inscription</a>
                   <?php }else{ ?>
                    <a style="color:red;" class="d-flex justify-content-center " title="Cliquez ici pour vous déconnecter"href='deco.php'>Déconnexion</a>

                    <?php } ?>
                </div>
            </div>
        </nav>


        <div class="mask d-flex align-items-center h-100 gradient-custom-3">

            <div class="container">

                <div class="row d-flex justify-content-center align-items-center h-100">

                    <div class="col-12 col-md-9 col-lg-7 col-xl-6">
                        <div class="card shadow-lg p-3 mb-5 bg-body rounded" style="border-radius: 15px;">

                            <div class="card-body p-5">

                                <h2 style="font-family: 'Poppins', sans-serif; " class="text-uppercase text-center mb-5 fw-bolder">Connexion</h2>

                               

                                <form method="POST">

                                    

                                    <div class="col-auto mb-5">
                                        <div class="input-group">
                                            <div style="border: none;" class="input-group-text"><span class="material-symbols-outlined">mail</span></div>
                                            <input style="border: none;" type="email" class="form-control" id="email" name="email" title="Veuillez indiquer votre adresse mail" placeholder="Adresse mail..." required>
                                        </div>

                                    </div>



                                    <div class="col-auto mb-5">
                                        <div class="input-group">
                                            <div style="border: none;" class="input-group-text"><span class="material-symbols-outlined">lock</span></div>
                                            <input style="border: none;" type="password" class="form-control" id="pass" name="pass" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Votre mot de passe doit contenir au moins un chiffre, une majuscule, une minuscule et au moins 6 caractères" placeholder="Mot de passe..." required>
                                        </div>
                                        <input type="checkbox" class="mt-5 ms-3" onclick="filtreMdp()"> Afficher le mot de passe
                                        
                                    </div>

                                    <div style="color:red;" class="d-flex justify-content-center" id="messIdentInex">
                                        <?php
                                        if ($IdentifiantsInexistant) {
                                            print $IdentifiantsInexistant;
                                        }
                                        ?>
                                    </div>
                                    <div style="color:red;" class="d-flex justify-content-center" id="messIdentError">
                                        <?php
                                        if ($IdentifiantsErr) {
                                            print $IdentifiantsErr;
                                        }
                                        ?>
                                    </div>

                                    <?php if (isset($_SESSION['email']) == true) { ?>  <!-- si l'utilisateur est connecté, on affiche le bouton de déconnexion -->
                                     <p style="color:green;" class="d-flex justify-content-center ">✔ Connexion établie, bienvenue <?php echo $_SESSION['email']  ?> !</p>
                                       
                                    <?php } ?>

                                    <!-- bouton pour s'inscrire: -->
                                    <?php if (isset($_SESSION['email']) == false) { ?>
                                    <div class=" d-flex justify-content-center mt-4">
                                        <button type="submit" name="button_register" class="boutonInsc btn btn btn-lg gradient-custom-4 text-body  ">Se connecter</button>
                                    </div>
                                    <?php } ?>

                                    <?php if (isset($_SESSION['email']) == false) { ?>
                                    <p class="text-center text-muted mt-4 mb-0">Vous n'avez pas de compte ? <a href="inscription.php" class="fw-bold text-body"><u>S'enregistrer</u></a></p>    
                                    <div class="Mdp-oublie">
                                        <a class="d-flex justify-content-center text-muted mt-4 mb-0 fw-2 text-body" href="MdpForgot.php">Mot de passe oublié ? </a>
                                    </div>
                                    <?php } ?>

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






</body>

</html>