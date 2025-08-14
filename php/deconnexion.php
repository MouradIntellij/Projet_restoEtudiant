<?php
session_start();
session_unset();
session_destroy();
header("Location: /Projet_restoEtudiant/php/connexion.php");
exit();
