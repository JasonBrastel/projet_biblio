<?php
session_start();                                         //on démarre la session pour pouvoir utiliser les variables de session
unset($_SESSION['email']);                               //on détruit la variable de session

if (ini_get("session.use_cookies")) {                    //on vérifie si les cookies sont activés
    setcookie(session_name(), '', time() - 3600);        //on détruit le cookie de session
}

session_destroy();                                       //on détruit la session
header('location: index.php');                       //on redirige vers la page de connexion

?>