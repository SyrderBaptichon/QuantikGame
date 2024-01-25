<?php

require_once 'PieceQuantik.php';
class ArrayPieceQuantik implements ArrayAccess,Countable
{
    protected $piecesQuantik;

    public function __construct()
    {
        $this->piecesQuantik = array();
    }


    public function offsetExists($offset)
    {
        // TODO: Implement offsetExists() method.
        return isset($this->piecesQuantik[$offset]);
    }

    public function offsetGet($offset)
    {
        // TODO: Implement offsetGet() method.
        return isset($this->piecesQuantik[$offset])?
            $this->piecesQuantik[$offset] : null;
    }

    public function offsetSet($offset, $value)
    {
        // TODO: Implement offsetSet() method.
        if (is_null($offset)) {
            $this->piecesQuantik[] = $value;
        } else {
            $this->piecesQuantik[$offset] = $value;
        }
    }

    public function offsetUnset($offset)
    {
        // TODO: Implement offsetUnset() method.
        unset($this->piecesQuantik[$offset]);
    }

    public function count()
    {
        // TODO: Implement count() method.
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

}

$apq = ArrayPieceQuantik::initPiecesNoires();
for ($i=0; $i<count($apq); $i++)
    echo $apq[$i];
echo "\n";
$apq = ArrayPieceQuantik::initPiecesBlanches();
for ($i=0; $i<count($apq); $i++)
    echo $apq[$i];
echo "\n";