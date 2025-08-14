<?php
session_start();

if (!isset($_SESSION['test'])) {
    $_SESSION['test'] = "Session active à " . date("H:i:s");
}

echo "Valeur de session : " . $_SESSION['test'];
echo "<br>";
echo "ID de session : " . session_id();
