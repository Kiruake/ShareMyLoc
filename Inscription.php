<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données du formulaire
    $nickname = $_POST["username"];  // Utiliser "username" ici
    $email = $_POST["email"];
    $mot_de_passe = $_POST["password"];  // Utiliser "password" ici

    // Valider les données (vous pouvez ajouter des validations supplémentaires ici)

    // Enregistrer les données dans la base de données
    include('connexion.php');
    
    // Utiliser une requête préparée pour améliorer la sécurité
    $query = $connexion->prepare("INSERT INTO Utilisateurs (nickname, email, mot_de_passe) VALUES (?, ?, ?)");
    $query->bind_param("sss", $nickname, $email, $mot_de_passe);
    $result = $query->execute();

    if ($result) {
        // Rediriger vers une page de succès ou afficher un message de succès
        header("Location: index.php");
        exit;
    } else {
        echo "Erreur lors de l'enregistrement de l'utilisateur : " . $connexion->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Inscription</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
    <h2 class="titre">Inscription</h2>

    <div style="right : 0px; top: 0px; position: absolute">
        <img src="img/Forme.png" >
    </div>

   <div class="logo"> <?php include('img/Logo.svg');?> </div>


    <form method="POST" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
        
        <input type="text" id="nickname" name="username" placeholder="Username" required><br><br>

        <input type="email" id="email" name="email" placeholder="Email" required><br><br>

        <input type="password" id="password" name="password" placeholder="Mot de passe" required><br><br>

        <button type="submit" id="inscription"><p class="para">S'inscrire</p></button> <!-- Modifier le bouton -->

        <a href="index.php" style="text-decoration: none"><p class="creation">j'ai déja un compte</p></a> <!-- Lien de retour à la connexion -->
    </form>
</body>
</html>

<style>

.logo {
 text-align: center;
 z-index: 2;
position: relative;
margin-bottom: 50px;
}

.titre {
 color: var(--Vert-secondaire, #1F8684);
 margin-top: 55px;
text-align: center;
font-family: 'Poppins', sans-serif;
font-size: 30px;
font-style: normal;
font-weight: 700;
line-height: normal;
z-index: 2;
position: relative;
}

.error {
    position: absolute;
    text-align: center;
    bottom: 350px;
    left: 70px;
    margin: auto;
     }

.para {
    text-align: center;
    margin: auto;
    padding-top: 8px;
    padding-bottom: 8px;
    color: #FFF;
    font-family: 'Poppins', sans-serif;
    font-size: 20px;
    font-style: normal;
    font-weight: 700;
    line-height: normal;
}

.mdp {

color: #1F8684;
text-align: center;
font-family: 'Poppins', sans-serif;
font-size: 16px;
font-style: normal;
font-weight: 600;
line-height: normal;
margin-top: 5px;
margin-bottom: 35px;
}

.creation {

color: #494949;
text-align: center;
font-family: 'Poppins' , sans-serif;
font-size: 16px;
font-style: normal;
font-weight: 600;
line-height: normal;
margin-top: 30px;

}

#nickname, #email, #password {
    display: flex;
    width: 270px;
    padding: 20px 35px 20px 20px;
    align-items: center;
    gap: 10px;
    z-index: 2;
    border-radius: 10px;
    border: none;
    background-color : #F0F0F0;
    margin: auto;
    position: relative;
    font-family: 'Montserrat', sans-serif;
    font-size: 16px;
    font-style: normal;
    font-weight: 400;
    line-height: normal;
}



#inscription {
    display: flex;
    width: 290px;
    padding: 8px 35px 8px 20px;
    align-items: center;
    gap: 10px;
    z-index: 2;
    border-radius: 10px;
    border: none;
    background-color: #1F8684;
    box-shadow: 0px 10px 20px 0px rgba(44, 184, 143, 0.38);
    margin: auto;
    position: relative;
    text-align: center;
}

input#nickname:focus, input#password:focus {
    border: 2px solid #1F8684;
    background-color: #F0F0F0;
    /* Autres styles personnalisés... */
}



</style>
