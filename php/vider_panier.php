<?php
session_start();
unset($_SESSION['panier']);
header('Location: panier.php');
exit();
