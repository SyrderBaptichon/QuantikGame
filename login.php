<?php
require_once 'PDOQuantik.php';
require_once 'db.php';

session_start();

function getPageLogin(): string {
    return '<!DOCTYPE html>
<html class="no-js" lang="fr" dir="ltr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="style.css" />
    <title>Accès à la salle de jeux</title>
    <style>
        body, html {
            height: 100%;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
                background-color: #cac5c5;
        }

        .container {
            text-align: center;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="quantik"></div>
    <h1>Accès au salon Quantik</h1>
    <h2>Identification du joueur</h2>
    <form action="traiteFormQuantik.php" method="POST">
        <fieldset>
            <legend>Nom</legend>
            <input type="text" name="playerName" />
            <input type="submit" value="connecter" name="action"/>
        </fieldset>
    </form>
</div>
</body>
</html>
';

}

if (isset($_REQUEST['playerName'])) {
    // connexion à la base de données
   $player = PDOQuantik::selectPlayerByName($_REQUEST['playerName']);
    if (is_null($player))
        $player = PDOQuantik::createPlayer($_REQUEST['playerName']);
    $_SESSION['player'] = $_REQUEST['playerName'];

    header('HTTP/1.1 303 See Other');
    header('Location: index.php');
}
else {
  echo getPageLogin();
}
