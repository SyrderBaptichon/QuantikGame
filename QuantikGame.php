<?php
require_once 'AbstractGame.php';

class QuantikGame extends AbstractGame
{
    public PlateauQuantik $plateau;
    public ArrayPieceQuantik $piecesBlanches;
    public ArrayPieceQuantik $piecesNoires;
    public array $couleurPlayer;

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
}
