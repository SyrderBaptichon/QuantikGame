<?php
require_once 'AbstractGame.php';

class QuantikGame extends AbstractGame
{
    public PlateauQuantik $plateau;
    public ArrayPieceQuantik $piecesBlanches;
    public ArrayPieceQuantik $piecesNoires;
    public array $couleurPlayer;

}
