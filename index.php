<?php
require_once 'QuantikGame.php';
require_once 'QuantikUIGenerator.php';
require_once 'PieceQuantik.php';
require_once 'AbstractUIGenerator.php';

session_start();

$chaine = "";

if (!isset($_SESSION['UI'])) {
    $_SESSION['UI'] = new QuantikGame();
    $UI = &$_SESSION['UI'];
    $UI->plateau = new PlateauQuantik();
    $UI->piecesBlanches = ArrayPieceQuantik::initPiecesBlanches();
    $UI->piecesNoires = ArrayPieceQuantik::initPiecesNoires();
    $UI->couleurPlayer = [PieceQuantik::$BLACK, PieceQuantik::$WHITE];
    $UI->gameStatus = "choixPiece";
    $UI->currentPlayer = PieceQuantik::$BLACK;
}

switch ($_SESSION['UI']->gameStatus){
    case 'choixPiece':
        $chaine .= QuantikUIGenerator::getPageSelectionPiece($UI, $UI->currentPlayer);
        break;
    case 'posePiece':
        $chaine .= QuantikUIGenerator::getPagePosePiece($UI,$UI->currentPlayer,$_POST['selectedPiece']);
        break;
    case 'victoire':
        $chaine .= QuantikUIGenerator::getPageVictoire($UI, $UI->currentPlayer);
        break;
    default:
        $chaine .= AbstractUIGenerator::getPageErreur("Erreur au cours de la partie", "<a href='traiteFormQuantik.php?action=recommencerPartie'>Recommencer</a>");
}

echo $chaine;

