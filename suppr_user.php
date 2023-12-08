<?php 
ob_start();

session_start();                                                       //on démarre la session pour pouvoir utiliser les variables de session
require_once("dao.php");                                          //on fait la jonction avec le fichier DAO
                                    
$dao = new DAO();                                                        //on crée une nouvelle instance de DAO
$dao->connexion();                                                       //on se connecte à la BDD            



if (isset($_POST['btn_suppr_user'])){ 


    $dao->suppr_utilisateur($_POST['btn_suppr_user']); }
header('location:page_utilisateur.php');    


$dao->disconnect();  

ob_end_flush();
?>