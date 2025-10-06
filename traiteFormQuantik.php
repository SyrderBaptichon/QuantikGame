<?php
require_once 'QuantikGame.php';
require_once 'QuantikUIGenerator.php';
require_once 'PieceQuantik.php';
require_once 'PDOQuantik.php';
require_once 'PlateauQuantik.php'; // Ajout de l'inclusion de PlateauQuantik.php
require_once 'AbstractUIGenerator.php';

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
                $_SESSION['ETAT'] = "consultePartieVictoire";
                $_SESSION['UI']->gameStatus = 'finished';
                $_SESSION['winner'] = $_SESSION['UI']->nomCourant();
            } else {
                $_SESSION['UI']->currentPlayer++;
                $_SESSION['UI']->currentPlayer = $_SESSION['UI']->currentPlayer % 2;
                $_SESSION['ETAT'] = "consultePartieEnCours";
                $_SESSION['UI']->gameStatus = 'waitingForPlayer';
            }
            PDOQuantik::saveGameQuantik($_SESSION['UI']->gameStatus, $_SESSION['UI']->getJson(), $_SESSION['UI']->gameID);
            break;

        case 'annulerChoix':
            $_SESSION['ETAT'] = "choixPiece";
            break;

        case 'recommencerPartie':
            session_unset();
            session_destroy();
            break;

        case 'connecter':
            require_once 'db.php';
            PDOQuantik::initPDO($_ENV['sgbd'],$_ENV['host'],$_ENV['database'],$_ENV['user'],$_ENV['password']);
            $_SESSION['ETAT'] = "home";
            $_SESSION['player'] = $_POST['playerName'];
            // echo $_SESSION['player'];
            $player = PDOQuantik::selectPlayerByName($_SESSION['player']);
            if (is_null($player))
                $player = PDOQuantik::createPlayer($_SESSION['player']);
            break;

        case 'creerPartie':
            require_once 'db.php';
            PDOQuantik::initPDO($_ENV['sgbd'],$_ENV['host'],$_ENV['database'],$_ENV['user'],$_ENV['password']);
            $qg= new QuantikGame(array(PDOQuantik::selectPlayerByName($_SESSION['player'])));
            $qg->plateau = new PlateauQuantik();
            $qg->piecesBlanches = ArrayPieceQuantik::initPiecesBlanches();
            $qg->piecesNoires = ArrayPieceQuantik::initPiecesNoires();
            $qg->currentPlayer = PieceQuantik::$WHITE;
            $qg->gameStatus = "constructed";
            if(isset($_SESSION['inc'])) $_SESSION['inc']++;
            else $_SESSION['inc']=PDOQuantik::getMaxIdGameQuantik()+1;
            $qg->gameID = $_SESSION['inc'];

            try {
                PDOQuantik::createGameQuantik($_SESSION['player'], $qg->getJson());
            } catch (Exception $e) {

            }

            $_SESSION['ETAT'] = 'home';
            break;

        case 'quitterSession':
            $_SESSION['ETAT'] = 'login';
            break;

        case 'rejoindrePartie':
            require_once 'db.php';
            PDOQuantik::initPDO($_ENV['sgbd'],$_ENV['host'],$_ENV['database'],$_ENV['user'],$_ENV['password']);
            $q = PDOQuantik::getGameQuantikById($_POST['gameId']);
            $qg = QuantikGame::initQuantikGame($q->getJson());
            $qg->couleursPlayers[1] = PDOQuantik::selectPlayerByName($_SESSION['player']);
            $qg->gameStatus ='initialized';
            try {
                PDOQuantik::addPlayerToGameQuantik($_SESSION['player'], $qg->getJson(), $_POST['gameId']);
            } catch (Exception $e) {
            }

            PDOQuantik::saveGameQuantik($qg->gameStatus,$qg->getJson(),$_POST['gameId']);
            $_SESSION['ETAT'] = 'home';
            break;

        case 'jouerPartie':
            require_once 'db.php';
            PDOQuantik::initPDO($_ENV['sgbd'],$_ENV['host'],$_ENV['database'],$_ENV['user'],$_ENV['password']);
            $ui = PDOQuantik::getGameQuantikById($_POST['gameId']);
            unset($_SESSION['UI']);
            $_SESSION['UI'] = QuantikGame::initQuantikGame($ui->getJson());
            $_SESSION['ETAT'] = "choixPiece";
            //echo $_SESSION['UI']->gameID;
            break;

        case 'retournerHome':
            $_SESSION['ETAT'] = 'home';
            break;

        case 'consulterPartie':
            require_once 'db.php';
            PDOQuantik::initPDO($_ENV['sgbd'],$_ENV['host'],$_ENV['database'],$_ENV['user'],$_ENV['password']);
            unset($_SESSION['UI']);
            $ui = PDOQuantik::getGameQuantikById($_POST['gameId']);
            $_SESSION['UI'] = QuantikGame::initQuantikGame($ui->getJson());
            $_SESSION['ETAT'] = "consultePartieEnCours";
            break;

        default:
            $_SESSION['ETAT'] = "erreur";
    }
}

header("Location: index.php");
exit();
