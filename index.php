<?php
require_once 'QuantikGame.php';
require_once 'QuantikUIGenerator.php';
require_once 'PlateauQuantik.php'; // Ajout de l'inclusion de PlateauQuantik.php
require_once 'PieceQuantik.php';
require_once 'AbstractUIGenerator.php';
require_once 'PDOQuantik.php';

session_start();

$chaine ='';

function getPageHome() :string
{
    return '<!DOCTYPE html>
<html class="no-js" lang="fr" dir="ltr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="author" content="Syrder Baptichon" />
    <link rel="stylesheet" href="style.css"/>
    <title>Page d\'accueil</title>
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
        
        .fieldset {
            text-align: justify ;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="quantik"></div>
    <h1>Salon Quantik</h1>
    <h2>Joueur : '.$_SESSION['player'].'</h2>
    <form action="traiteFormQuantik.php" method="post">
        <button type="submit" value="creerPartie" name="action">Créer une partie</button>
    </form>
    '.getPartiesEnCours().'
    '.getParties1Joueur().'
    '.getPartiesTerminees().'
    <form action="traiteFormQuantik.php" method="post">
        <button type="submit" value="quitterSession" name="action">Quitter la session</button>
    </form>
</div>
</body>
</html>';

}

function getPartiesTerminees():string
{
    $contenu = '<p><form action="traiteFormQuantik.php" method="post">
        <fieldset><legend>Parties terminées</legend>';
    require_once 'db.php';
    PDOQuantik::initPDO($_ENV['sgbd'],$_ENV['host'],$_ENV['database'],$_ENV['user'],$_ENV['password']);
    $games = PDOQuantik::getAllGameQuantik();
    if(is_null($games)) $contenu = "Aucune partie";
    else {
        foreach ($games as $game) {
            $id = $game->gameID;
            if ($game->gameStatus == 'finished') {
                $contenu .= "<button type='submit' value=$id name='gameId'>Voir</button>&nbsp;&nbsp;&nbsp;&nbsp;";
                $contenu .= $game.' (gagné par '.$game->nomCourant().')';
                $contenu .= '<br/>';
            }
        }
    }
    $contenu .= '<input type="hidden" value="consulterPartie" name="action"/>';
    $contenu.='</fieldset></form></p>';
    return $contenu;
}

function getParties1Joueur():string
{
    $contenu = '<p><form action="traiteFormQuantik.php" method="post">
        <fieldset><legend>Parties à 1 joueur</legend>';
    require_once 'db.php';
    PDOQuantik::initPDO($_ENV['sgbd'],$_ENV['host'],$_ENV['database'],$_ENV['user'],$_ENV['password']);
    $games = PDOQuantik::getAllGameQuantik();
    if(is_null($games)) $contenu = "Aucune partie";
    else {
        foreach ($games as $game) {
            $id = $game->gameID;
            if ($game->gameStatus == 'waitingForPlayer' && ($game->couleursPlayers[0])->getName()!=$_SESSION['player'] && !isset($game->couleursPlayers[1])) {
                $contenu .= "<button type='submit' value=$id name='gameId'>Rejoindre</button>&nbsp;&nbsp;";
                $contenu .= $game;
                $contenu .= '<br/>';
            }
        }
    }
    $contenu .= '<input type="hidden" value="rejoindrePartie" name="action"/>';
    $contenu.='</fieldset></form></p>';
    return $contenu;
}

function getPartiesEnCours() :string
{
    $contenu = '<p><form action="traiteFormQuantik.php" method="post">
<fieldset><legend>Parties à jouer</legend>';
    require_once 'db.php';
    PDOQuantik::initPDO($_ENV['sgbd'],$_ENV['host'],$_ENV['database'],$_ENV['user'],$_ENV['password']);

    $games = PDOQuantik::getAllGameQuantikByPlayerName($_SESSION['player']);
    if(is_null($games)) $contenu = "rien";
    else {
        foreach($games as $game){
            $id = $game->gameID;
            if($game->gameStatus != 'finished' && $game->isCourant($_SESSION['player'])) {
                $contenu .= "<button type='submit' value=$id name='gameId'>Jouer</button>&nbsp;&nbsp;&nbsp;&nbsp;";
                $contenu .= $game;
                $contenu .= '<br/>';
            }

        }
        $contenu .= '<input type="hidden" value="jouerPartie" name="action"/>';
        $contenu .='</fieldset></form></p>';
        $contenu .='<p><form action="traiteFormQuantik.php" method="post">
<fieldset><legend>Parties en attente</legend>';
        foreach($games as $game){
            $id = $game->gameID;
            if($game->gameStatus != 'finished' && $game->isMembre($_SESSION['player']) && !$game->isCourant($_SESSION['player']) ) {
                $contenu .= "<button type='submit' value=$id name='gameId'>Voir</button>&nbsp;&nbsp;&nbsp;&nbsp;";
                $contenu .=  $game.'        (en attente de  '.$game->nomCourant().')';
                $contenu .= '<br/>';
            }
        }
        $contenu .= '<input type="hidden" value="consulterPartie" name="action"/>';
        $contenu .='</fieldset></form></p>';
    }


    return $contenu;
}

if(!isset($_SESSION['ETAT'])){
    $_SESSION['ETAT'] = "login";
}

switch ($_SESSION['ETAT']){
    case 'login':
        header('HTTP/1.1 303 See Other');
        header("Location: login.php");
        exit();

    case 'home':
        $chaine .= getPageHome();
        break;

    case 'choixPiece':
			$chaine = AbstractUIGenerator::getDebutHTML();

        $chaine .= QuantikUIGenerator::getPageSelectionPiece($_SESSION['UI'], $_SESSION['UI']->currentPlayer);
        $chaine.= AbstractUIGenerator::getFinHTML();
        break;

    case 'posePiece':
    	$chaine = AbstractUIGenerator::getDebutHTML();
        if (isset($_SESSION['selectedPiece'])) {
            $chaine .= QuantikUIGenerator::getPagePosePiece($_SESSION['UI'],$_SESSION['UI']->currentPlayer,$_SESSION['selectedPiece']);
        } else {
            echo $_POST['selectedPiece'];
        }
        $chaine.= AbstractUIGenerator::getFinHTML();
        break;

    case 'consultePartieVictoire':
			$chaine = AbstractUIGenerator::getDebutHTML();
        $chaine .= QuantikUIGenerator::getPageVictoire($_SESSION['UI'], $_SESSION['UI']->currentPlayer);
        $chaine.= AbstractUIGenerator::getFinHTML();
        break;

    case 'consultePartieEnCours':
    $chaine = AbstractUIGenerator::getDebutHTML();
        $chaine .= QuantikUIGenerator::getPageEnCours($_SESSION['UI'], $_SESSION['UI']->currentPlayer);
        $chaine.= AbstractUIGenerator::getFinHTML();
        break;

    default:
    $chaine = AbstractUIGenerator::getDebutHTML();
        $chaine .= AbstractUIGenerator::getPageErreur("Erreur au cours de la partie", QuantikUIGenerator::getLienRecommencer());
        $chaine.= AbstractUIGenerator::getFinHTML();
}


echo $chaine;

