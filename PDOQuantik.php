<?php
require_once 'Player.php';
require_once 'db.php';

use PDO;
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
        self::$createPlayer->bindValue(':name', $name, PDO::PARAM_STR);
        self::$createPlayer->execute();
        return self::selectPlayerByName($name);
    }

    public static function selectPlayerByName(string $name): ?Player
    {
        if (!isset(self::$selectPlayerByName))
            self::$selectPlayerByName = self::$pdo->prepare('SELECT * FROM Player WHERE name=:name');
        self::$selectPlayerByName->bindValue(':name', $name, PDO::PARAM_STR);
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
        if (!isset(self::$createGameQuantik))
            self::$createGameQuantik = self::$pdo->prepare('INSERT INTO QuantikGame(playerOne, json) VALUES (?, ?)');
        $id = self::selectPlayerByName($playerName)->getId();
        self::$createGameQuantik->bindValue($id, $json, PDO::PARAM_STR);
        self::$createGameQuantik->execute();
    }

    /**
     * initialisation et execution de $saveGameQuantik la requête préparée pour changer
     * l'état de la partie et sa représentation json
     */
    public static function saveGameQuantik(string $gameStatus, string $json, int $gameId): void
    {
        if (!isset(self::$saveGameQuantik))
            self::$saveGameQuantik = self::$pdo->prepare('UPDATE QuantikGame SET gameStatus=?, json=? WHERE gameId=?');
        self::$saveGameQuantik->bindValue($gameStatus, $json, $gameId, PDO::PARAM_STR);
        self::$saveGameQuantik->execute();
    }

    /**
     * initialisation et execution de $addPlayerToGameQuantik la requête préparée pour intégrer le second joueur
     */
    public static function addPlayerToGameQuantik(string $playerName, string $json, int $gameId): void
    {
        if (!isset(self::$addPlayerToGameQuantik))
            self::$addPlayerToGameQuantik = self::$pdo->prepare('UPDATE QuantikGame SET playerTwo=?, json=? WHERE gameId=?');
        self::$addPlayerToGameQuantik->bindValue($playerName, $json, $gameId, PDO::PARAM_STR);
        self::$addPlayerToGameQuantik->execute();
    }

    /**
     * initialisation et execution de $selectAllGameQuantikById la requête préparée pour récupérer
     * une instance de quantikGame en fonction de son identifiant
     */
    public static function getGameQuantikById(int $gameId): ?QuantikGame
    {
        if (!isset(self::$selectGameQuantikById))
            self::$selectGameQuantikById = self::$pdo->prepare('SELECT * FROM quantikgame WHERE gameId=?');
        self::$selectGameQuantikById->bindValue($gameId, PDO::PARAM_STR);
        self::$selectGameQuantikById->execute();
        $game = self::$selectGameQuantikById->fetchObject('QuantikGame');
        return ($game) ? $game : null;
    }
    /**
     * initialisation et execution de $selectAllGameQuantik la requête préparée pour récupérer toutes
     * les instances de quantikGame
     */
    public static function getAllGameQuantik(): array
    {
        $resu = array();
        if (!isset(self::$selectAllGameQuantik))
            self::$selectAllGameQuantik = self::$pdo->prepare('SELECT * FROM quantikgame');
        self::$selectAllGameQuantik->execute();
        while($game = self::$selectAllGameQuantik->fetchObject('QuantikGame')){
            $resu[] = $game;
        }
        return $resu;
    }

    /**
     * initialisation et execution de $selectAllGameQuantikByPlayerName la requête préparée pour récupérer les instances
     * de quantikGame accessibles au joueur $playerName
     * ne pas oublier les parties "à un seul joueur"
     */
    public static function getAllGameQuantikByPlayerName(string $playerName): array
    {
        $resu = array();
        if (!isset(self::$selectAllGameQuantik))
            self::$selectAllGameQuantik = self::$pdo->prepare('SELECT * FROM quantikgame WHERE playerOne= :name  OR playerTwo= :name');
        self::$selectAllGameQuantik->bindValue(':name', $playerName, PDO::PARAM_STR);
        self::$selectAllGameQuantik->execute();
        while($game = self::$selectAllGameQuantik->fetchObject('QuantikGame')){
            $resu[] = $game;
        }
        return $resu;
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

}