<?php
require_once 'AbstractGame.php';
require_once 'PlateauQuantik.php';
require_once 'ArrayPieceQuantik.php';


class QuantikGame extends AbstractGame
{
    public PlateauQuantik $plateau;
    public ArrayPieceQuantik $piecesBlanches;
    public ArrayPieceQuantik $piecesNoires;
    public array $couleursPlayers;

    public function __construct(array $pl)
    {
        $this->couleursPlayers = $pl;
    }

    public function __toString(): string
    {
        return 'Partie n°' . $this->gameID . ' lancée par joueur ' . $this->getPlayers()[0];
    }
    public function getJson(): string
    {
        $json = '{';
        $json .= '"plateau":' . $this->plateau->getJson();
        $json .= ',"piecesBlanches":' . $this->piecesBlanches->getJson();
        $json .= ',"piecesNoires":' . $this->piecesNoires->getJson();
        $json .= ',"currentPlayer":' . $this->currentPlayer;
        $json .= ',"gameID":' . $this->gameID;
        $json .= ',"gameStatus":' . json_encode($this->gameStatus);
        if (is_null($this->couleursPlayers[1]))
            $json .= ',"couleursPlayers":[' . $this->couleursPlayers[0]->getJson() . ']';
        else
            $json .= ',"couleursPlayers":[' . $this->couleursPlayers[0]->getJson() . ',' . $this->couleursPlayers[1]->getJson() . ']';
        return $json . '}';
    }
    public static function initQuantikGame(string $json): QuantikGame
    {
        $object = json_decode($json);
        $players = [];
        foreach ($object->couleursPlayers as $stdObj) {
            $p = new Player();
            $p->setName($stdObj->name);
            $p->setId($stdObj->id);
            $players[] = $p;
        }
        $qg = new QuantikGame($players);
        $qg->plateau = PlateauQuantik::initPlateauQuantik($object->plateau);
        $qg->piecesBlanches = ArrayPieceQuantik::initArrayPieceQuantik($object->piecesBlanches);
        $qg->piecesNoires = ArrayPieceQuantik::initArrayPieceQuantik($object->piecesNoires);
        $qg->currentPlayer = $object->currentPlayer;
        $qg->gameID = $object->gameID;
        $qg->gameStatus = $object->gameStatus;
        return $qg;
    }

    public function nomCourant()
    {
        $tab = $this->couleursPlayers;
        if (!isset($tab[$this->currentPlayer])) return 'un joueur';
        return $tab[$this->currentPlayer]->getName();
    }

    public function lanceur()
    {
        return $this->couleursPlayers[0]->getName();
    }

    public function isMembre(string $nom):bool
    {
        return ($this->couleursPlayers[0])->getName() == $nom || ($this->couleursPlayers[1])->getName() == $nom;
    }

    public function isCourant(string $nom):bool
    {
        return $this->nomCourant() == $nom;
    }

    private function getPlayers()
    {
        return $this->couleursPlayers;
    }

}