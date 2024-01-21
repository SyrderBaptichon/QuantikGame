<?php

class ActionQuantik {
    protected $plateau;

    public function __construct(PlateauQuantik $lePlateau)
    {
        $this->plateau = $lePlateau;
    }

    /**
     * @return PlateauQuantik
     */
    public function getPlateau(): PlateauQuantik
    {
        return $this->plateau;
    }

    public function isRowWin(int $numRow) : bool
    {
        $row = $this->plateau->getRow($numRow);
        return ActionQuantik::isComboWin($row);
    }

    public function isColWin(int $numCol) : bool
    {
        $col = $this->plateau->getCol($numCol);
        return ActionQuantik::isComboWin($col);
    }

    public function isCornerWin(int $dir) :bool
    {
        $bor = $this->plateau->getCorner($dir);
        return ActionQuantik::isComboWin($bor);
    }

    public function isValidePose(int $rowNum, int $colNum, PieceQuantik $piece) : bool
    {
         return ActionQuantik::isPieceValide($this->plateau->getRow($rowNum),$piece)
            && ActionQuantik::isPieceValide($this->plateau->getCol($colNum),$piece)
             && ActionQuantik::isPieceValide($this->plateau->getBorder(PlateauQuantik::getCornerFromCoord($rowNum,$colNum)),$piece);
    }

    public function posePiece(int $rowNum, int $colNum, PieceQuantik $piece) : void
    {
        if($this->isValidePose($rowNum,$colNum,$piece)){
            $this->plateau->setPiece($rowNum,$colNum,$piece);
        }
    }

    public function __toString()
    {
        return $this->plateau->__toString();
    }

    private static function isComboWin(ArrayPieceQuantik $tab) :bool
    {
        for($i=0; $i<count($tab);$i++){
            $somme+=$tab[$i]->getForme();
        }
        if($somme==10)return true;
        else return false;
    }

    private static function isPieceValide(ArrayPieceQuantik $pieces, PieceQuantik $p)
    {
        for($i=0; $i<count($pieces);$i++){
            if($p->getForme() == $pieces[$i]->getForme())
                return false;
        }
        return true;
    }
}
