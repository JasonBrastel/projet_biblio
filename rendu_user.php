
<?php 
session_start();                                                       //on démarre la session pour pouvoir utiliser les variables de session
require_once("dao.php");                                          //on fait la jonction avec le fichier DAO
                                    
$dao = new DAO();                                                        //on crée une nouvelle instance de DAO
$dao->connexion();                                                       //on se connecte à la BDD            

$id_livress = $dao->get_livre_rendu(["inputTitre" => $_POST['liste_livre_rendu']]);

if (isset($_POST['btn_rendu_user'])){ 

    $dao->rendu_livre($id_livress['id_livre']); 
   

}
header('location:page_utilisateur.php'); 

$dao->disconnect();  
?>

