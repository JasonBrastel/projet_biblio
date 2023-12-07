

<?php 
session_start();                                                       //on démarre la session pour pouvoir utiliser les variables de session
require_once("dao.php");                                          //on fait la jonction avec le fichier DAO
                                    
$dao = new DAO();                                                        //on crée une nouvelle instance de DAO
$dao->connexion();                                                       //on se connecte à la BDD            


$id_livress = $dao->get_livre_emprunt(["inputTitre" => $_POST['liste_livre_emprunt']]);



if (isset($_POST['btn_emprunt'])){ 

    $dao->emprunt_livre($id_livress['id_livre']); }



header('location:index.php');

$dao->disconnect(); 
?>