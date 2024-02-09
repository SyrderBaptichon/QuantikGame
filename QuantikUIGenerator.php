<?php
require_once 'AbstractUIGenerator.php';
require_once 'PieceQuantik.php';
require_once 'PlateauQuantik.php';
require_once 'ActionQuantik.php';
require_once 'QuantikGame.php';
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
       for ($i = 0; $i < $apq->count(); $i++) {
           $piece = $apq->getPieceQuantik($i);
           if($i!=$pos) $chaine.=self::getButtonClass($apq->getPieceQuantik($i));
           else $chaine .="<button class='has-background-warning' type=\'submit\' name=\'active\' disabled >$piece</button>";
       }
       $chaine.='</div>';
       return $chaine;
    }

    protected static function getFormSelectionPiece(ArrayPieceQuantik $apq): string
    {
        $chaine = '<form action=\'traiteFormQuantik.php\' method=\'post\'>';

        for($i = 0; $i < $apq->count(); $i++) {
            $piece = $apq->getPieceQuantik($i);
            $chaine .= "<button type='submit' name='selectedPiece' value='$i'>$piece</button>";
        }
        $chaine.= "<input type='hidden' value='choisirPiece' name='action'/>\n";

        $chaine.='</form>';
        return $chaine;
    }

    protected static function getFormPlateauQuantik(PlateauQuantik $plateau, PieceQuantik $piece): string
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
                    $chaine.="<button class='has-background-success' type='submit' name='placePiece' value='$i,$j'>$p</button>";
                } else {
                    $chaine.="<button type='submit' name='selectedPiece' disabled>$p</button>";
                }
                $chaine.="</td>";
            }
            $chaine.="</tr>";
        }
        $chaine.="</table>";
        $chaine.= "<input type='submit' value='poserPiece' name='action' hidden='hidden'/>\n";

        $chaine.='</form>';
        return $chaine;
    }

    protected static function getFormBoutonAnnulerChoixPiece(): string
    {
        $html = "<form action='traiteFormQuantik.php' method='post'>";

        $html .= "<input type='hidden' name='cancelSelection' value='true'>";
        $html .= "<input type='submit' value='Changer de pièce'>";
        $html.= "<input type='submit' value='annulerChoix' name='action' hidden='hidden'/>\n";

        $html .= "</form>";

        return $html;
    }

    protected static function getDivMessageVictoire(int $couleur): string
    {
        $s = "";
        if($couleur == PieceQuantik::$BLACK) $s.= "Noirs";
        else $s.= "Blancs";
        return "<div> 
                    <p>Victoire des $s !</p> 
                    <p>".self::getLienRecommencer()."</p>
                 </div>";

    }

    protected static function getLienRecommencer(): string
    {
        return "<a href='traiteFormQuantik.php?action=recommencerPartie'>Recommencer</a>";
    }

    public static function getPageSelectionPiece(QuantikGame $quantik, int $couleurActive): string
    {
        $html = AbstractUIGenerator::getDebutHTML();

        $html.= "<div class='columns'>
                    <div class='column'>
                        <div>
                            <h5>Blancs</h5>
                            <div>";

        if($couleurActive == PieceQuantik::$WHITE){
            $html.= self::getFormSelectionPiece($quantik->piecesBlanches);
        } else {
            $html.= self::getDivPiecesDisponibles($quantik->piecesBlanches);
        }
        $html.= "</div>
                </div>
                <div>
                    <h5>Noirs</h5>
                    <div>";

        if($couleurActive == PieceQuantik::$BLACK){
            $html.= self::getFormSelectionPiece($quantik->piecesNoires);
        } else {
            $html.= self::getDivPiecesDisponibles($quantik->piecesNoires);
        }

        $html.= "</div>
                    </div>
                 </div>
                 <div class='column'>
                    <h4>Plateau</h4>".
                    self::getDivPlateauQuantik($quantik->plateau)
                ."</div>
            </div>";
        $html.= AbstractUIGenerator::getFinHTML();
        return $html;
    }

    public static function getPagePosePiece(QuantikGame  $quantik, int $couleurActive, int $posSelection): string
    {
        $html = AbstractUIGenerator::getDebutHTML();

        $html.= "<div class='columns'>
                    <div class='column'>
                        <div>
                            <h5>Blancs</h5>
                            <div>";
        $piece = null;
        if($couleurActive == PieceQuantik::$WHITE){
            $html.= self::getDivPiecesDisponibles($quantik->piecesBlanches,$posSelection);
            $piece = $quantik->piecesBlanches[$posSelection];
        } else {
            $html.= self::getDivPiecesDisponibles($quantik->piecesBlanches);
        }
        $html.= "</div>
                </div>
                <div>
                    <h5>Noirs</h5>
                    <div>";

        if($couleurActive == PieceQuantik::$BLACK){
            $html.= self::getDivPiecesDisponibles($quantik->piecesNoires,$posSelection);
            $piece = $quantik->piecesNoires[$posSelection];
        } else {
            $html.= self::getDivPiecesDisponibles($quantik->piecesNoires);
        }

        $html.= "</div>
                    </div>
                 </div>
                 <div class='column'>
                    <h4>Plateau</h4>".
            self::getFormPlateauQuantik($quantik->plateau, $piece)
            ."</div>
            </div>
            <div>".
            self::getFormBoutonAnnulerChoixPiece()
            ."</div>";
        $html.= AbstractUIGenerator::getFinHTML();
        return $html;
    }

    public static function getPageVictoire(QuantikGame $quantik, int $couleurActive): string
    {
        $html = self::getPageSelectionPiece($quantik, $couleurActive);
        $html.= self::getDivMessageVictoire($couleurActive);
        return $html;
    }
}

