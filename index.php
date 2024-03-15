<?php
require_once 'QuantikGame.php';
require_once 'QuantikUIGenerator.php';
require_once 'PlateauQuantik.php'; // Ajout de l'inclusion de PlateauQuantik.php
require_once 'PieceQuantik.php';
require_once 'AbstractUIGenerator.php';

session_start();

$chaine = AbstractUIGenerator::getDebutHTML();

/*if (!isset($_SESSION['UI'])) {
    $_SESSION['UI'] = new QuantikGame();
    $_SESSION['UI']->plateau = new PlateauQuantik();
    $_SESSION['UI']->piecesBlanches = ArrayPieceQuantik::initPiecesBlanches();
    $_SESSION['UI']->piecesNoires = ArrayPieceQuantik::initPiecesNoires();
    $_SESSION['UI']->couleurPlayer = [PieceQuantik::$BLACK, PieceQuantik::$WHITE];
    $_SESSION['UI']->gameStatus = "choixPiece";
    $_SESSION['ETAT'] = "choixPiece";
    $_SESSION['UI']->currentPlayer = PieceQuantik::$BLACK;
}*/

if(!isset($_SESSION['ETAT'])){
    $_SESSION['ETAT'] = "login";
}

switch ($_SESSION['ETAT']){
    case 'login':
        header('HTTP/1.1 303 See Other');
        header("Location: login.php");
        exit();

    case 'home':
        header('HTTP/1.1 303 See Other');
        header("Location: home.php");
        exit();

    case 'choixPiece':


        $chaine .= QuantikUIGenerator::getPageSelectionPiece($_SESSION['UI'], $_SESSION['UI']->currentPlayer);
        break;

    case 'posePiece':
        if (isset($_SESSION['selectedPiece'])) {
            $chaine .= QuantikUIGenerator::getPagePosePiece($_SESSION['UI'],$_SESSION['UI']->currentPlayer,$_SESSION['selectedPiece']);
        } else {
            echo $_POST['selectedPiece'];
        }
        break;

    case 'consultePartieVictoire':

        $chaine .= QuantikUIGenerator::getPageVictoire($_SESSION['UI'], $_SESSION['UI']->currentPlayer);
        break;

    case 'consultePartieEnCours':
        $chaine .= QuantikUIGenerator::getPageEnCours($_SESSION['UI'], $_SESSION['UI']->currentPlayer);
        break;

    default:
        $chaine .= AbstractUIGenerator::getPageErreur("Erreur au cours de la partie", QuantikUIGenerator::getLienRecommencer());
}

$chaine.= AbstractUIGenerator::getFinHTML();
echo $chaine;

