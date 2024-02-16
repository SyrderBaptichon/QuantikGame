<?php
require_once 'QuantikGame.php';
require_once 'QuantikUIGenerator.php';
require_once 'PlateauQuantik.php'; // Ajout de l'inclusion de PlateauQuantik.php
require_once 'PieceQuantik.php';
require_once 'AbstractUIGenerator.php';

session_start();

$chaine = "";

if (!isset($_SESSION['UI'])) {
    $_SESSION['UI'] = new QuantikGame();
    $_SESSION['UI']->plateau = new PlateauQuantik();
    $_SESSION['UI']->piecesBlanches = ArrayPieceQuantik::initPiecesBlanches();
    $_SESSION['UI']->piecesNoires = ArrayPieceQuantik::initPiecesNoires();
    $_SESSION['UI']->couleurPlayer = [PieceQuantik::$BLACK, PieceQuantik::$WHITE];
    $_SESSION['UI']->gameStatus = "choixPiece";
    $_SESSION['ETAT'] = "choixPiece";
    $_SESSION['UI']->currentPlayer = PieceQuantik::$BLACK;
}

switch ($_SESSION['ETAT']){
    case 'choixPiece':
        $chaine .= QuantikUIGenerator::getPageSelectionPiece($_SESSION['UI'], $_SESSION['UI']->currentPlayer);
        break;
    case 'posePiece':
        if (isset($_SESSION['selectedPiece'])) {
            $chaine .= QuantikUIGenerator::getPagePosePiece($_SESSION['UI'],$_SESSION['UI']->currentPlayer,$_SESSION['selectedPiece']);
        } else {
            echo("BLAAAAAA");
            echo $_POST['selectedPiece'];
        }
        break;
    case 'victoire':
        $chaine .= QuantikUIGenerator::getPageVictoire($_SESSION['UI'], $_SESSION['UI']->currentPlayer);
        break;
    default:
        $chaine .= AbstractUIGenerator::getPageErreur("Erreur au cours de la partie", "<a href='traiteFormQuantik.php?action=recommencerPartie'>Recommencer</a>");
}

echo $chaine;

