<?php
require_once 'QuantikGame.php';
require_once 'QuantikUIGenerator.php';
require_once 'PieceQuantik.php';

session_start();

if (isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'choisirPiece':
            $_SESSION['ETAT'] = "posePiece";
            $_SESSION['selectedPiece'] = $_POST['selectedPiece'];
            break;

        case 'poserPiece':
            if ($_SESSION['UI']->currentPlayer == PieceQuantik::$BLACK) {
                $piece = $_SESSION['UI']->piecesNoires[$_SESSION['selectedPiece']];
                $_SESSION['UI']->piecesNoires->removePieceQuantik($_SESSION['selectedPiece']);
            } else {
                $piece = $_SESSION['UI']->piecesBlanches[$_SESSION['selectedPiece']];
                $_SESSION['UI']->piecesBlanches->removePieceQuantik($_SESSION['selectedPiece']);
            }

            $positions = explode(',', $_POST['placePiece']);
            $i = $positions[0]; // Première valeur
            $j = $positions[1]; // Deuxième valeur

            $action = new ActionQuantik($_SESSION['UI']->plateau);
            $action->posePiece(intval($i), intval($j), $piece);

            if ($action->isRowWin($i) || $action->isColWin($j) || $action->isCornerWin(PlateauQuantik::getCornerFromCoord($i, $j))) {
                $_SESSION['ETAT'] = "victoire";
            } else {
                $_SESSION['UI']->currentPlayer++;
                $_SESSION['UI']->currentPlayer = $_SESSION['UI']->currentPlayer % 2;
                $_SESSION['ETAT'] = "choixPiece";
            }
            break;

        case 'annulerChoix':
            $_SESSION['ETAT'] = "choixPiece";
            break;

        case 'recommencerPartie':
            session_unset();
            session_destroy();
            break;

        default:
            $_SESSION['ETAT'] = "erreur";
    }
}

header("Location: index.php");
exit();
