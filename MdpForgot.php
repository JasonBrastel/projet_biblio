<?php 
session_start();                                                          //on démarre la session pour pouvoir utiliser les variables de session
require_once("dao.php");                                             //on fait la jonction avec le fichier DAO
$dao = new DAO();                                                         //on crée une nouvelle instance de DAO
$dao->connexion();                                                        //on se connecte à la BDD

$ErrorMail="";                                                            //on crée une variable pour stocker les messages d'erreur si l'email n'existe pas
$SendMailValide="";                                                             //on crée une variable pour stocker les messages de confirmation si l'email existe

function valid_donnees($donnees)
{   //on crée une fonction pour sécuriser les données du formulaire                                                              
    $donnees = htmlentities(stripslashes(trim($donnees)));                //on enlève les espaces, les antislashs et les caractères spéciaux
    return $donnees;                                                      //on retourne les données sécurisées                                           
}


if (isset($_POST['button_send']) && (($_SERVER['REQUEST_METHOD'] === 'POST'))) {   //si on clique sur le bouton "se connecter" et que la méthode utilisée est POST                          
    $email = valid_donnees($_POST['email']);                                       //on sécurise les données du formulaire
    $token = bin2hex(random_bytes(16));                                            //on crée un token aléatoire de 16 caractères en hexadécimal et on le stocke dans une variable
    $token_hash= hash('sha256', $token);                                           //on hash le token

    $expiry = date("Y-m-d H:i:s", time() + 60 * 30);                               //on crée une date d'expiration pour le token de 30 minutes

    $result = $dao->checkMail(valid_donnees($_POST['email']));                     //on stocke le résultat de la fonction checkMail dans une variable

if ($result > 0)  {                                                                //si l'email existe dans la BDD c'est-à-dire si le résultat de la fonction checkMail est supérieur à 0                         
    $dao->addToken($email, $token_hash, $expiry);                                  //on ajoute le token dans la BDD avec la fonction addToken
    // $dao->sendMail($email, $token, $expiry);                                    //on envoie le mail avec le token et la fonction mail 
    $SendMailValide = "Un mail vous a été envoyé, vérifiez votre adresse.";                                       //on affiche un message de confirmation                                                                           
  } else {
    $ErrorMail = "Aucun compte est associé à cette adresse électronique";          //on affiche un message d'erreur si l'email n'existe pas dans la BDD
  }

}



?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié </title>
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
            background-color: #343a40; /* Couleur de la navbar */
            color: white;
            text-align: center;
            padding: 10px;
        }
        
    </style>

</head>
<body>
    
<section>

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



        <div class="mask gradient-custom-3">

            <div class="container">

                <div class="row d-flex justify-content-center align-items-center h-100">

                    <div class="col-12 col-md-9 col-lg-7 col-xl-6">
                        <div class="card shadow-lg p-3 mb-5 bg-body rounded" style="border-radius: 15px;">

                            <div class="card-body p-5">

                                <h2 style="font-family: 'Poppins', sans-serif; " class="text-uppercase text-center mb-5 fw-bolder">Mot de passe oublié </h2>

                                

                                <form method="POST">

                                   

                                    <div class="col-auto mb-4">
                                        <div class="input-group">
                                            <div  class="input-group-text"><span class="material-symbols-outlined">mail</span></div>
                                            <input  type="email" class="form-control" id="email" name="email" title="Veuillez indiquer votre adresse mail" placeholder="Mail" required>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-center " style="color:red;" id="messEmailIntrouvable">
                                        <?php if ($ErrorMail) {
                                             print $ErrorMail;} 
                                        ?>                              
                                    </div>
                                    <div class="d-flex justify-content-center " style="color:green;" id="messEmailValide">
                                        <?php if ($SendMailValide) {
                                             print $SendMailValide;} 
                                        ?>                              
                                    </div>
                                    



                                    <div class=" d-flex justify-content-center mt-4">
                                        <button type="submit" name="button_send" class="boutonInsc btn btn btn-lg gradient-custom-4 text-body  ">Envoyer</button>
                                    </div>
                                  
                                </form>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>

    <footer class="navbar navbar-expand-lg bg-dark text-white mt-5">
    <div class="container-fluid d-flex justify-content-center">
        <span class="navbar-brand text-white fs-6 " >MyBiblio - 2023</span>
    </div>
</footer>












         <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>