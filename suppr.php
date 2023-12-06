

<?php 
session_start();                                                       //on démarre la session pour pouvoir utiliser les variables de session
require_once("dao.php");                                          //on fait la jonction avec le fichier DAO
                                    
$dao = new DAO();                                                        //on crée une nouvelle instance de DAO
$dao->connexion();                                                       //on se connecte à la BDD            



if (isset($_POST['btn_suppr'])){ 

    $dao->suppr_user($_POST['btn_suppr']); }
header('location:page_utilisateur.php');    

    $dao->suppr_livre($_POST['btn_suppr']); }
header('location:index.php');    


$dao->disconnect();  


?>