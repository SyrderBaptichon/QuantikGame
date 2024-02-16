<?php

require_once 'PieceQuantik.php';
class ArrayPieceQuantik implements ArrayAccess,Countable
{
    protected array $piecesQuantik;

    public function __construct()
    {
        $this->piecesQuantik = array();
    }


    public function offsetExists($offset): bool
    {
        return isset($this->piecesQuantik[$offset]);
    }

    public function offsetGet($offset): mixed
    {
        return isset($this->piecesQuantik[$offset]) ?
            $this->piecesQuantik[$offset] : null;
    }

    public function offsetSet($offset, $value): void
    {
        if (is_null($offset)) {
            $this->piecesQuantik[] = $value;
        } else {
            $this->piecesQuantik[$offset] = $value;
        }
    }

    public function offsetUnset($offset): void
    {
        unset($this->piecesQuantik[$offset]);
    }

    public function count(): int
    {
        return count($this->piecesQuantik);
    }

    public function getPieceQuantik(int $pos) : PieceQuantik
    {
        return $this->offsetGet($pos);
    }

    public function setPieceQuantik(int $pos, PieceQuantik $piece) : void
    {
        $this->offsetSet($pos,$piece);
    }

    public function addPieceQuantik(PieceQuantik $piece) : void
    {
        $this->piecesQuantik[] = $piece;
    }

    public function removePieceQuantik(int $pos) :void
    {
        unset($this->piecesQuantik[$pos]);
        $this->piecesQuantik = array_values($this->piecesQuantik);
    }

    public function __toString()
    {
        $chaine="";
        for($i=0;$i<$this->count();$i++){
           $chaine.=$this->getPieceQuantik($i)->__toString();
           $chaine.=" ";
        }
        return $chaine;
    }

    public static function initPiecesNoires() : ArrayPieceQuantik
    {
        $pack = new ArrayPieceQuantik();
        $pack->addPieceQuantik(PieceQuantik::initBlacksphere());
        $pack->addPieceQuantik(PieceQuantik::initBlacksphere());
        $pack->addPieceQuantik(PieceQuantik::initBlackCone());
        $pack->addPieceQuantik(PieceQuantik::initBlackCone());
        $pack->addPieceQuantik(PieceQuantik::initBlackCube());
        $pack->addPieceQuantik(PieceQuantik::initBlackCube());
        $pack->addPieceQuantik(PieceQuantik::initBlackCylindre());
        $pack->addPieceQuantik(PieceQuantik::initBlackCylindre());

        return $pack;
    }

    public static function initPiecesBlanches() : ArrayPieceQuantik
    {
        $pack = new ArrayPieceQuantik();
        $pack->addPieceQuantik(PieceQuantik::initWhitesphere());
        $pack->addPieceQuantik(PieceQuantik::initWhitesphere());
        $pack->addPieceQuantik(PieceQuantik::initWhiteCone());
        $pack->addPieceQuantik(PieceQuantik::initWhiteCone());
        $pack->addPieceQuantik(PieceQuantik::initWhiteCube());
        $pack->addPieceQuantik(PieceQuantik::initWhiteCube());
        $pack->addPieceQuantik(PieceQuantik::initWhiteCylindre());
        $pack->addPieceQuantik(PieceQuantik::initWhiteCylindre());
        return $pack;
    }

    public function getJson(): string
    {
        $json = "[";
        $jTab = [];
        foreach ($this->piecesQuantiks as $p)
            $jTab[] = $p->getJson();
        $json .= implode(',', $jTab);
        return $json . ']';
    }

    public static function initArrayPieceQuantik(string|array $json): ArrayPieceQuantik
    {
        $apq = new ArrayPieceQuantik();
        if (is_string($json)) {
            $json = json_decode($json);
        }
        foreach ($json as $j)
            $apq[] = PieceQuantik::initPieceQuantik($j);
        return $apq;
    }
}
