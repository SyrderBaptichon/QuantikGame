<?php
require_once 'QuantikGame.php';
require_once 'QuantikUIGenerator.php';
require_once 'PieceQuantik.php';


$UI = &$_SESSION['UI'];
// The application is already running, and a game is in progress
if (isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'choisirPiece':
            $UI->currentPlayer++;
            $UI->currentPlayer = $UI->currentPlayer % 2;
            $UI->gamestatus = "choixPiece";

            break;

        case 'finir':
            // Handle 'finir' action
            break;

        case 'raz':
            // Handle 'raz' action
            break;

        default:
            echo "An unexpected action was requested";
    }
}

header('HTTP/1.1 303 See Other');
header("Location: index.php");
// Commenting out session_unset and session_destroy to avoid ending the session.
// session_unset();
//session_destroy();
