<?php
require_once 'ArrayPieceQuantik.php';
require_once 'PieceQuantik.php';
class PlateauQuantik
{
    public static int $NBROWS = 4;
    public static int $NBCOLS = 4;
    public static int $NW = 0;
    public static int $NE = 1;
    public static int $SW = 2;
    public static int $SE = 3;

    protected $cases;

    public function __construct()
    {
        $this->cases = new ArrayPieceQuantik();
        for($i = 0; $i < self::$NBCOLS*self::$NBROWS; $i++) {
            $this->cases->addPieceQuantik(PieceQuantik::initVoid());
        }
    }

    public function getPiece(int $rowNum, int $colNum): PieceQuantik
    {
        $index = ($rowNum - 1) * self::$NBCOLS + ($colNum-1);
        return $this->cases[$index];
    }

    public function setPiece(int $rowNum, int $colNum, PieceQuantik $p): void
    {
        $index = ($rowNum - 1) * self::$NBCOLS + ($colNum-1);
        $this->cases[$index] = $p;
    }

    public function getRow(int $numRow): ArrayPieceQuantik
    {
        $i = new ArrayPieceQuantik();
        for($y=1;$y<=self::$NBCOLS;$y++){
            $i[]=$this->getPiece($numRow,$y);
        }
        return $i;
    }

    public function getCol(int $numCol): ArrayPieceQuantik
    {
        $i = new ArrayPieceQuantik();
        for($y=1;$y<=self::$NBROWS;$y++){
            $i->addPieceQuantik($this->getPiece($y,$numCol));
        }
        return $i;
    }

    public function getCorner(int $dir): ArrayPieceQuantik
    {
        $i=new ArrayPieceQuantik();
        if($dir==self::$NW){
            $i->addPieceQuantik($this->getPiece(1,1));
            $i->addPieceQuantik($this->getPiece(1,2));
            $i->addPieceQuantik($this->getPiece(2,1));
            $i->addPieceQuantik($this->getPiece(2,2));
        } elseif($dir==self::$NE){
            $i->addPieceQuantik($this->getPiece(1,3));
            $i->addPieceQuantik($this->getPiece(1,4));
            $i->addPieceQuantik($this->getPiece(2,3));
            $i->addPieceQuantik($this->getPiece(2,4));
        } elseif($dir==self::$SW){
            $i->addPieceQuantik($this->getPiece(3,1));
            $i->addPieceQuantik($this->getPiece(3,2));
            $i->addPieceQuantik($this->getPiece(4,1));
            $i->addPieceQuantik($this->getPiece(4,2));
        } else {
            $i->addPieceQuantik($this->getPiece(3,3));
            $i->addPieceQuantik($this->getPiece(3,4));
            $i->addPieceQuantik($this->getPiece(4,3));
            $i->addPieceQuantik($this->getPiece(4,4));
        }
        return $i;
    }

    public static function getCornerFromCoord(int $rowNum, int $colNum): int
    {
        $d=0;
        if($rowNum<=2 && $colNum<=2){
           $d=self::$NW;
        } elseif($rowNum<=2 && $colNum>2){
            $d=self::$NE;
        }elseif ($rowNum>2 && $colNum<=2){
            $d=self::$SW;
        }else{
            $d=self::$SE;
        }
        return $d;
    }

    public function __toString()
    {
        return $this->cases->__toString();
    }
}

