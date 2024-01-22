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
        $index = ($rowNum - 1) * self::$NBCOLS + ($colNum);
        return $this->cases[$index];
    }

    public function setPiece(int $rowNum, int $colNum, PieceQuantik $p): void
    {
        $index = ($rowNum - 1) * self::$NBCOLS + ($colNum);
        $this->cases[$index] = $p;
    }

    public function getRow(int $numRow): ArrayPieceQuantik
    {

    }
}

$p = new PlateauQuantik();
$piece =  PieceQuantik::initBlackCone();
$p->setPiece(1, 1, $piece);
echo $p->getPiece(1,1);
?>
