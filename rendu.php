

<?php 
session_start();                                                       //on démarre la session pour pouvoir utiliser les variables de session
require_once("dao.php");                                          //on fait la jonction avec le fichier DAO
                                    
$dao = new DAO();                                                        //on crée une nouvelle instance de DAO
$dao->connexion();                                                       //on se connecte à la BDD            



if (isset($_POST['btn_rendu'])){ 
    $dao->rendu_livre($_POST['btn_rendu']); 
   

}
header('location:index.php'); 

$dao->disconnect();  
?>