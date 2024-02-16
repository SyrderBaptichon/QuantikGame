<?php
require_once 'QuantikGame.php';
require_once 'QuantikUIGenerator.php';
require_once 'PieceQuantik.php';


$UI = &$_SESSION['UI'];
// The application is already running, and a game is in progress
if (isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'choisirPiece':

            $UI->gamestatus = "posePiece";

            break;

        case 'poserPiece':

            if($UI->currentPlayer == PieceQuantik::$BLACK){
                $piece = $UI->piecesNoires[$_POST['selectedPiece']];
                $UI->piecesNoires->removePieceQuantik($_POST['selectedPiece']);
            }else {
                $piece = $UI->piecesBlanches[$_POST['selectedPiece']];
                $UI->piecesBlanches->removePieceQuantik($_POST['selectedPiece']);
            }

            $positions = explode(',', $_POST['placePiece']);
            $i = $positions[0]; // Première valeur
            $j = $positions[1]; // Deuxième valeur
            $action = new ActionQuantik($UI->plateau);
            $action->posePiece($i,$j,$piece);

            if($action->isRowWin($i) || $action->isColWin($j) || $action->isCornerWin(PlateauQuantik::getCornerFromCoord($i,$j))){
                $UI->gameStatus = "victoire";
            } else {
                $UI->currentPlayer++;
                $UI->currentPlayer = $UI->currentPlayer % 2;
                $UI->gameStatus = "choixPiece";
            }

            break;

        case 'annulerChoix':

            $UI->gameStatus = "choixPiece";

            break;

        case 'recommencerPartie':

            session_unset();
            session_destroy();

            break;

        default:
            $UI->gameStatus = "erreur";
    }
}

header("Location: index.php");
// Commenting out session_unset and session_destroy to avoid ending the session.
// session_unset();
//session_destroy();
