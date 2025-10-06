<?php
require_once 'db.php';
require_once 'QuantikGame.php';
require_once 'Player.php';
require_once 'EntiteGameQuantik.php';
require_once 'QuantikUIGenerator.php';

class PDOQuantik
{
    private static PDO $pdo;

    public static function initPDO(string $sgbd, string $host, string $db, string $user, string $password, string $nomTable = ''): void
    {
        switch ($sgbd) {
            case 'pgsql':
                self::$pdo = new PDO('pgsql:host=' . $host . ' dbname=' . $db . ' user=' . $user . ' password=' . $password);
                break;
            default:
                exit ("Type de sgbd non correct : $sgbd fourni, 'mysql' ou 'pgsql' attendu");
        }

        // pour récupérer aussi les exceptions provenant de PDOStatement
        self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /* requêtes Préparées pour l'entitePlayer */
    private static $createPlayer;
    private static $selectPlayerByName;

    /******** Gestion des requêtes relatives à Player *************/
    public static function createPlayer(string $name): Player
    {
        if (!isset(self::$createPlayer))
            self::$createPlayer = self::$pdo->prepare('INSERT INTO Player(name) VALUES (:name)');
        self::$createPlayer->bindValue(':name', $name);
        self::$createPlayer->execute();
        return self::selectPlayerByName($name);
    }

    public static function selectPlayerByName(string $name): ?Player
    {
        if (!isset(self::$selectPlayerByName))
            self::$selectPlayerByName = self::$pdo->prepare('SELECT * FROM Player WHERE name=:name');
        self::$selectPlayerByName->bindValue(':name', $name);
        self::$selectPlayerByName->execute();
        $player = self::$selectPlayerByName->fetchObject('Player');
        return ($player) ? $player : null;
    }

    /* requêtes préparées pour l'entiteGameQuantik */
    private static $createGameQuantik;
    private static $saveGameQuantik;
    private static $addPlayerToGameQuantik;
    private static $selectGameQuantikById;
    private static $selectAllGameQuantik;
    private static $selectAllGameQuantikByPlayerName;

    /******** Gestion des requêtes relatives à QuantikGame *************/

    /**
     * initialisation et execution de $createGameQuantik la requête préparée pour enregistrer une nouvelle partie
     */
    public static function createGameQuantik(string $playerName, string $json): void
    {
        // Obtenez l'ID du joueur en utilisant son nom
        $player = self::selectPlayerByName($playerName);
        if (!$player) {
            throw new Exception("Le joueur $playerName n'existe pas.");
        }
        $playerId = $player->getId();

        // Préparer la requête SQL pour insérer une nouvelle partie
        $sql = "INSERT INTO QuantikGame (playerOne, json) VALUES (:playerId, :json)";

        // Préparer la requête avec PDO
        self::$createGameQuantik = self::$pdo->prepare($sql);

        // Liaison des valeurs et exécution de la requête
        self::$createGameQuantik->bindValue(':playerId', $playerId, PDO::PARAM_INT);
        self::$createGameQuantik->bindValue(':json', $json, PDO::PARAM_STR);
        self::$createGameQuantik->execute();
    }

    /**
     * initialisation et execution de $saveGameQuantik la requête préparée pour changer
     * l'état de la partie et sa représentation json
     */
    public static function saveGameQuantik(string $gameStatus, string $json, int $gameId): void
    {
        $sql = "UPDATE QuantikGame SET gameStatus = :gameStatus, json = :json WHERE gameId = :gameId";

        // Préparer la requête avec PDO
        self::$saveGameQuantik = self::$pdo->prepare($sql);

        // Liaison des valeurs et exécution de la requête
        self::$saveGameQuantik->bindValue(':gameStatus', $gameStatus, PDO::PARAM_STR);
        self::$saveGameQuantik->bindValue(':json', $json, PDO::PARAM_STR);
        self::$saveGameQuantik->bindValue(':gameId', $gameId, PDO::PARAM_INT);
        self::$saveGameQuantik->execute();
    }

    /**
     * initialisation et execution de $addPlayerToGameQuantik la requête préparée pour intégrer le second joueur
     */
    public static function addPlayerToGameQuantik(string $playerName, string $json, int $gameId): void
    {
        // Obtenez l'ID du joueur en utilisant son nom
        $player = self::selectPlayerByName($playerName);
        if (!$player) {
            throw new Exception("Le joueur $playerName n'existe pas.");
        }
        $playerId = $player->getId();

        // Préparer la requête SQL pour ajouter un deuxième joueur à une partie Quantik
        $sql = "UPDATE QuantikGame SET playerTwo = :playerId, json = :json WHERE gameId = :gameId";

        // Préparer la requête avec PDO
        self::$addPlayerToGameQuantik = self::$pdo->prepare($sql);

        // Liaison des valeurs et exécution de la requête
        self::$addPlayerToGameQuantik->bindValue(':playerId', $playerId, PDO::PARAM_INT);
        self::$addPlayerToGameQuantik->bindValue(':json', $json, PDO::PARAM_STR);
        self::$addPlayerToGameQuantik->bindValue(':gameId', $gameId, PDO::PARAM_INT);
        self::$addPlayerToGameQuantik->execute();
    }

    /**
     * initialisation et execution de $selectAllGameQuantikById la requête préparée pour récupérer
     * une instance de quantikGame en fonction de son identifiant
     */
    public static function getGameQuantikById(int $gameId): ?QuantikGame
    {
        // Préparer la requête SQL pour récupérer une partie Quantik en fonction de son identifiant
        $sql = "SELECT * FROM QuantikGame WHERE gameId = :gameId";

        // Préparer la requête avec PDO
        self::$selectGameQuantikById = self::$pdo->prepare($sql);

        // Liaison de l'identifiant de la partie et exécution de la requête
        self::$selectGameQuantikById->bindValue(':gameId', $gameId, PDO::PARAM_INT);
        self::$selectGameQuantikById->execute();

        // Récupérer la ligne de résultat de la requête
        $result = self::$selectGameQuantikById->fetch(PDO::FETCH_ASSOC);

        // Vérifier si la partie existe
        if (!$result) {
            return null; // La partie n'existe pas
        }

        // Créer une instance de QuantikGame à partir des données récupérées
        $quantikGame = QuantikGame::initQuantikGame($result['json']);

        return $quantikGame;
    }
    /**
     * initialisation et execution de $selectAllGameQuantik la requête préparée pour récupérer toutes
     * les instances de quantikGame
     */
    public static function getAllGameQuantik(): array
    {
        if (!isset(self::$selectAllGameQuantik))
            self::$selectAllGameQuantik = self::$pdo->prepare('SELECT * FROM quantikgame');
        self::$selectAllGameQuantik->execute();
        $games = [];
        while ($row = self::$selectAllGameQuantik->fetch(PDO::FETCH_ASSOC)) {
            $game = QuantikGame::initQuantikGame($row['json']);
            $games[] = $game;
        }

        return $games;
    }

    /**
     * initialisation et execution de $selectAllGameQuantikByPlayerName la requête préparée pour récupérer les instances
     * de quantikGame accessibles au joueur $playerName
     * ne pas oublier les parties "à un seul joueur"
     */
    public static function getAllGameQuantikByPlayerName(string $playerName): array
    {
        // Préparer la requête SQL
        $sql = "SELECT * FROM quantikgame WHERE playerOne = (SELECT id FROM player WHERE name = :playerName)
            OR playerTwo = (SELECT id FROM player WHERE name = :playerName)";

        // Préparer la requête avec PDO
        self::$selectAllGameQuantikByPlayerName = self::$pdo->prepare($sql);

        // Liaison des valeurs et exécution de la requête
        self::$selectAllGameQuantikByPlayerName->bindValue(':playerName', $playerName, PDO::PARAM_STR);
        self::$selectAllGameQuantikByPlayerName->execute();

        // Récupération des résultats sous forme d'objets QuantikGame
        $games = [];
        while ($row = self::$selectAllGameQuantikByPlayerName->fetch(PDO::FETCH_ASSOC)) {
            $game = QuantikGame::initQuantikGame($row['json']);
            $games[] = $game;
        }

        return $games;
    }
    /**
     * initialisation et execution de la requête préparée pour récupérer
     * l'identifiant de la dernière partie ouverte par $playername
     */
    public static function getLastGameIdForPlayer(string $playerName): int
    {
        $res =self::getAllGameQuantikByPlayerName($playerName);
        return end($res)->gameId;
    }

    public static function getMaxIdGameQuantik(): int
    {
        global $pdo; // Utiliser la connexion PDO définie dans votre fichier db.php

        try {
            $query = "SELECT MAX(gameId) AS max_id FROM QuantikGame";
            $statement = self::$pdo->query($query);

            $result = $statement->fetch(PDO::FETCH_ASSOC);

            $maxId = $result['max_id'];

            if ($maxId === null) {
                return 0; // Aucun jeu dans la table, retourner 0 ou une valeur par défaut
            } else {
                return intval($maxId); // Retourner l'identifiant de jeu maximum sous forme d'entier
            }
        } catch (PDOException $e) {
            exit("Erreur PDO: " . $e->getMessage());
        }
    }

}
PDOQuantik::initPDO($_ENV['sgbd'],$_ENV['host'],$_ENV['database'],$_ENV['user'],$_ENV['password']);
/*$game = PDOQuantik::getGameQuantikById(1);
$q = QuantikGame::initQuantikGame($game->getJson());
//echo QuantikUIGenerator::getPageSelectionPiece($game,$game->currentPlayer);
echo $q->getJson();*/
// Exemple d'utilisation de la fonction