<?php
require_once("backend/functions.php");
dbconn(true);
require_once('template/header.php');
require_once('template/menu.php');

$today = date("Y-m-d H:i:s");
$action = $_POST["action"];

if($action == "ajouter") {
    //$avatar = $_POST["avatar"];
    //$avatarname = $_FILES["name"];
    //if (!$avatarname){
        $avatarname = "nc";
    //}
$erreur = "";
$username =$_POST["username"];
if (!$username) {
$erreur .= "il manque votre pseudo";
}


$nom = $_POST["nom"];
if (!$nom) {
$erreur .= " manque votre nom";
}
$prenom = $_POST["prenom"];
if (!$prenom) {
$erreur .= " il manque votre prenom ";
}

$email = $_POST["email"];
$req = get_row_count("clients", "WHERE email='$email'");

if($req != 0) {
    $erreur .= 'cette email existe deja';
}

    if (!$email) {
        $erreur .= " il manque votre email ";
    }

    $email2 = $_POST["email2"];
    if ($email != $email2) {
        $erreur .= "vos email ne sont pas identique";
    }

    $adresse = $_POST["adresse"];
    if (!$adresse) {
        $erreur .= " il manque votre adresse ";
    }
    $city = $_POST["city"];
    if (!$city) {
        $erreur .= " il manque votre code postal ";
    }
    $password = sha1($_POST['password']);
    if (!$password) {
        $erreur .= " il manque votre mot-de-passe ";
    }

    $password2 = sha1($_POST['password2']);
    if($password != $password2){
        $erreur .= " vos mot de passe ne sont pas identique";
    }
$added = $today;
$last_login = $today;
$last_access = $today;
$ip = $_SERVER['REMOTE_ADDR'];
$class = 1;

if ($erreur == "") {
    SQL_Query_exec("INSERT INTO clients(`avatar`,`username`,`nom`,`prenom`,`email`,`adresse`,`city`,`password`,`added`,`last_login`,`last_access`,`ip`,`class`) 
                    VALUES ('$avatarname','$username','$nom','$prenom','$email','$adresse','$city','$password','$added','$last_login','$last_access','$ip','$class')");
    echo "votre inscription a bien etait pris en compte";

} else {
echo $erreur;
}
}
?>
<section class="hero is-fullheight">
   <div class="has-text-centered">
   <div class="hero-body">
     <div class="container">
       <div class="columns is-vcentered">
         <div class="column is-4 is-offset-4">
             <form method="post" action="inscription.php">
                 <input type="hidden" name="action" value="ajouter">
             <div class="has-text-centered"> <!--  centrer texte" connexion"-->
           <h1 class="title">
             Créer un compte
           </h1>
         </div>
           <div class="box inscription">
             <div class="has-text-centered">

               <!-- image avatar -->

               <div class="box avatar">
                 <div class="has-text-centered">
             <figure class="image is-128x128 ">

               <img src="http://findicons.com/files/icons/1072/face_avatars/300/a02.png"  >
               </div>
             </figure>
            </div>

          <!-- fin image avatar -->

             <a class="button is-primary">Ajouter un Avatar</a>
                 </div>
                    <!-- partie inscritpion -->
             <label class="label">Pseudo</label>
             <p class="control">
               <input class="input" type="text"  name="username" placeholder="pseudo">
             </p>
             <label class="label">Nom</label>
             <p class="control">
               <input class="input" type="text" name="nom" placeholder="nom">
             </p>
             <label class="label">Prenom</label>
             <p class="control">
               <input class="input" type="text" name="prenom" placeholder="prenom">
             </p>
             <label class="label" >Email</label>
             <p class="control">
               <input class="input" type="email" name="email" placeholder="email@">
             </p>
             <label class="label" >Confirmation Email</label>
             <p class="control">
               <input class="input" type="email" name="email2" placeholder="email@">
             </p>

             <label class="label">Adresse</label>
             <p class="control">
               <input class="input" type="text" name="adresse" placeholder="adresse">
             </p>
             <label class="label">Code postal</label>
             <p class="control">
               <input class="input" type="text" name="city" placeholder="30100">
             </p>

             <label class="label">Mot de passe</label>
             <p class="control">
               <input class="input" type="password" name="password" placeholder="●●●●●●●">
             </p>
             <label class="label">Confirmation mot de passe</label>
             <p class="control">
               <input class="input" type="password" name="password2" placeholder="●●●●●●●">
             </p>

              <!-- bouton confirmer l'inscription -->
             <p class="control">
               <div class="has-text-centered">
                 <button type="submit" class="button is-info2">Confirmer l'inscription</button>

                </p>
              </div>
           </div>
       </form>
                  <!-- fin partie inscritpion -->

                  <!-- lien -->
           <p class="has-text-centered">
             <a href="connection.php">Connection</a>
             |
             <a href="#"> Besoin d'aide?</a>
           </p>

         </div>
       </div>
     </div>
   </div>
 </div>
</section>

       <?php
       require_once ('template/footer.php');
       ?>
