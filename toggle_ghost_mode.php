<?php
// toggle_ghost_mode.php

session_start();

// Inversez la valeur de la variable de session "ghost_mode"
$_SESSION['ghost_mode'] = !isset($_SESSION['ghost_mode']) || $_SESSION['ghost_mode'] == false;

// Répondez avec un message (vous pouvez personnaliser cela selon vos besoins)
echo "Ghost mode is now " . ($_SESSION['ghost_mode'] ? 'enabled' : 'disabled');
?>
