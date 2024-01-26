<?php
require_once 'AbstractUIGenerator.php';
require_once 'PieceQuantik.php';
require_once 'PlateauQuantik.php';
require_once 'ActionQuantik.php';
class QuantikUIGenerator extends AbstractUIGenerator {

    protected static function getButtonClass(PieceQuantik $piece): String
    {
        return '<button type=\'submit\' name=\'active\' disabled >'.$piece.'</button>';
    }

    protected static function getDivPlateauQuantik(PlateauQuantik $plateau): String
    {
        $chaine='<div>';
        for($i=1; $i<=$plateau::$NBROWS; $i++){
            for($j=1;$j<=$plateau::$NBCOLS;$j++){
                $chaine.=self::getButtonClass($plateau->getPiece($i,$j));
            }
            $chaine.='<br>';
        }
        $chaine.='</div>';
        return $chaine;
    }

    protected static function getDivPiecesDisponibles(ArrayPieceQuantik $apq, int $pos=-1): string
    {
       $chaine = '<div>';
       for ($i = $pos + 1; $i < $apq->count(); $i++) {
           $chaine.=self::getButtonClass($apq->getPieceQuantik($i));
       }
       $chaine.='</div>';
       return $chaine;
    }

    protected static function getFormSelectionPiece(ArrayPieceQuantik $apq): string
    {
        $chaine = '<form action=\'traiteFormQuantik.php\' method=\'post\'>';
        for($i = 0; $i < $apq->count(); $i++) {
            $piece = $apq->getPieceQuantik($i);
            $chaine.="<button type='submit' name='selectedPiece' value='$i'>$piece</button>";
        }
        $chaine.='</form>';
        return $chaine;
    }

    public static function getFormPlateauQuantik(PlateauQuantik $plateau, PieceQuantik $piece): string
    {
        $action = new ActionQuantik($plateau);
        $chaine = "<form action='traiteFormQuantik.php' method='post'>";
        $chaine.="<table class='is-table'>";
        for($i = 1; $i <= $plateau::$NBROWS; $i++) {
            $chaine.="<tr>";
            for($j = 1; $j <= $plateau::$NBCOLS; $j++) {
                $p = $plateau->getPiece($i, $j);
                $chaine.="<td>";
                if($action->isValidePose($i, $j, $piece)) {
                    $chaine.="<button class='has-background-success' type='submit' name='selectedPiece' value='$i,$j'>$p</button>";
                } else {
                    $chaine.="<button type='submit' name='selectedPiece' disabled>$p</button>";
                }
                $chaine.="</td>";
            }
            $chaine.="</tr>";
        }
        $chaine.="</table>";
        $chaine.='</form>';
        return $chaine;
    }
}

$pq = new PlateauQuantik();
$pq->setPiece(1 , 1, PieceQuantik::initWhiteCube());
echo QuantikUIGenerator::getFormPlateauQuantik($pq, PieceQuantik::initBlackCube());