<?php
// Définir le titre de la page
$pageTitle = "Abonnements";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Abonnements</title>
    <?php include('header.php'); ?>
</head>
<body>
    

<h1 class="AboTitle">Nos abonnements premiums</h1>

<img src="img/Abo1.png" alt="Abonnement1" class="Abo">

<img src="img/Abo2.png" alt="Abonnement1" class="Abo2">




</body>
</html>

<?php 

session_start();
include("footer.php"); 

?>

<style>

.AboTitle{
    text-align:center;
    font-size: 24px;
    font-family: 'Poppins', sans-serif;
    color: #174949;
    margin-top: 50px;
}

.Abo{
    display: block;
    margin: auto;
    margin-top: 50px;
    width: 80%;
}

.Abo2{
    display: block;
    margin: auto;
    margin-top: 50px;
    margin-bottom: 120px;
    width: 80%;
}