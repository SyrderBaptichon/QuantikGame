<?php
require_once 'AbstractUIGenerator.php';
require_once 'PieceQuantik.php';
require_once 'PlateauQuantik.php';
require_once 'ActionQuantik.php';
require_once 'QuantikGame.php';

class QuantikUIGenerator extends AbstractUIGenerator {

    protected static function getButtonClass(PieceQuantik $piece): String
    {
        if ($piece->getCouleur() == PieceQuantik::$BLACK){
            switch ($piece->getForme()) {
                case PieceQuantik::$CUBE :
                    $buttonClass = 'cube';
                    break;
                case PieceQuantik::$CONE:
                    $buttonClass = 'cone';
                    break;
                case PieceQuantik::$CYLINDRE:
                    $buttonClass = 'cylindre';
                    break;
                case PieceQuantik::$SPHERE:
                    $buttonClass = 'sphere';
                    break;
                default:
                    $buttonClass = 'default';
                    break;
            }
        } else {
            switch ($piece->getForme()) {
                case PieceQuantik::$CUBE:
                    $buttonClass = 'cube_blanc';
                    break;
                case PieceQuantik::$CONE:
                    $buttonClass = 'cone_blanc';
                    break;
                case PieceQuantik::$CYLINDRE:
                    $buttonClass = 'cylindre_blanc';
                    break;
                case PieceQuantik::$SPHERE:
                    $buttonClass = 'sphere_blanc';
                    break;
                default:
                    $buttonClass = 'default';
                    break;
            }
        }

        return $buttonClass;
    }

    protected static function getDivPlateauQuantik(PlateauQuantik $plateau): String
    {
        $chaine='<div>';
        for($i=1; $i<=$plateau::$NBROWS; $i++){
            for($j=1;$j<=$plateau::$NBCOLS;$j++){
                $buttonclass = self::getButtonClass($plateau->getPiece($i,$j));
                $chaine.= '<button class="buttonPiece '.$buttonclass.'" type="submit" disabled >
                  '. $plateau->getPiece($i,$j)->__toString() .' 
                </button>';;
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
           $buttonclass = self::getButtonClass($piece);
           if($i!=$pos) $chaine.= '<button class="buttonPiece '.$buttonclass.'" type="submit" disabled >
                  '. $piece->__toString() .' </button>';
           else $chaine .='<button class="buttonPiece has-background-warning '.$buttonclass.'" type="submit" name="active" disabled >'.$piece.'</button>';
       }
       $chaine.='</div>';
       return $chaine;
    }

    protected static function getFormSelectionPiece(ArrayPieceQuantik $apq): string
    {
        $chaine = "<form action='traiteFormQuantik.php' method='POST'>";

        for($i = 0; $i < $apq->count(); $i++) {
            $piece = $apq->getPieceQuantik($i);
            $buttonclass = self::getButtonClass($piece);
            $chaine .= "<button class=\"buttonPiece\" class=" .$buttonclass."  type='submit' name='selectedPiece' value='$i'>$piece</button>";
        }
        $chaine.= "<input type='hidden' value='choisirPiece' name='action'/>\n";

        $chaine.='</form>';
        return $chaine;
    }

    protected static function getFormPlateauQuantik(PlateauQuantik $plateau, PieceQuantik $piece): string
    {
        $action = new ActionQuantik($plateau);
        $chaine = "<form action='traiteFormQuantik.php' method='POST'>";
        $chaine.="<table class='is-table'>";
        for($i = 1; $i <= $plateau::$NBROWS; $i++) {
            $chaine.="<tr>";
            for($j = 1; $j <= $plateau::$NBCOLS; $j++) {
                $p = $plateau->getPiece($i, $j);
                $chaine.="<td>";
                if($action->isValidePose($i, $j, $piece)) {
                    $chaine.="<button class='buttonPiece has-background-success' type='submit' name='placePiece' value='$i,$j'>$p</button>";
                } else {
                    $chaine.="<button class='buttonPiece' type='submit' disabled>$p</button>";
                }
                $chaine.="</td>";
            }
            $chaine.="</tr>";
        }
        $chaine.="</table>";
        $chaine.= "<input type='hidden' value='poserPiece' name='action'/>\n";

        $chaine.='</form>';
        return $chaine;
    }

    protected static function getFormBoutonAnnulerChoixPiece(): string
    {
        $html = "<form action='traiteFormQuantik.php' method='post'>";

        $html .= "<button type='submit' value='Changer de pièce'>Annuler le choix</button>";
        $html.= "<input type='hidden' value='annulerChoix' name='action'/>\n";

        $html .= "</form>";

        return $html;
    }

    protected static function getDivMessageVictoire(QuantikGame $quantik, int $couleur): string
    {
        $s = "";
        if($couleur == PieceQuantik::$BLACK) $s.= "Noirs  :";
        else $s.= "Blancs  :";
        $s .= $quantik->nomCourant();
        echo $quantik->nomCourant();
        return "<div> 
                    <p>Victoire des $s  !</p> 
                    <p>".self::getLienRecommencer()."</p>
                 </div>";

    }

    public static function getLienRecommencer(): string
    {
        $html = "<form action='traiteFormQuantik.php' method='post'>";
        $html .= "<button type='submit' value='retournerHome' name='action'>Revenir au salon</button>";
        $html .= "</form>";
        return $html;
    }

    public static function getPageSelectionPiece(QuantikGame $quantik, int $couleurActive): string
    {
        $html = '';
        $html .= "<div class='columns is-centered'>
            <div class='column'>
                <div>
                    <h5>Blancs : </h5>
                    <div>";

        if ($couleurActive == PieceQuantik::$WHITE) {
            $html .= self::getFormSelectionPiece($quantik->piecesBlanches);
        } else {
            $html .= self::getDivPiecesDisponibles($quantik->piecesBlanches);
        }

        $html .= "</div>
        </div>
        <div>
            <h5>Noirs : </h5>
            <div>";

        if ($couleurActive == PieceQuantik::$BLACK) {
            $html .= self::getFormSelectionPiece($quantik->piecesNoires);
        } else {
            $html .= self::getDivPiecesDisponibles($quantik->piecesNoires);
        }

        $html .= "</div>
        </div>
    </div>
    <div class='column'>
        <h4>Plateau</h4>" .
            self::getDivPlateauQuantik($quantik->plateau)
            . "</div>
</div>";

        return $html;

    }

    public static function getPagePosePiece(QuantikGame  $quantik, int $couleurActive, int $posSelection): string
    {
        $html = '';
        /*$c1 = $quantik->couleursPlayers[0]->getName();
        if(is_null($quantik->couleursPlayers[1])) $c2 =' ';
        else $c2 = $quantik->couleursPlayers[0]->getName();*/

        $html.= "<div class='columns'>
                    <div class='column'>
                        <div>
                            <h5>Blancs : </h5>
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
                    <h5>Noirs : </h5>
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
        return $html;
    }

    public static function getPageVictoire(QuantikGame $quantik, int $couleurActive): string
    {
        $html = "<h3 class='texteV'>Victoire de ". $_SESSION['winner']. " </h3>";
        $html .= self::getPageSelectionPiece($quantik, 3);
        $html.= self::getLienRecommencer();
        return $html;
    }

    public static function getPageEnCours(QuantikGame $quantik, int $couleurActive): string
    {
        $html = self::getPageSelectionPiece($quantik, 3);
        $html .= self::getLienRecommencer();
        return $html;
    }
}

