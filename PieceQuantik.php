<?php
class PieceQuantik
{
    public static int $WHITE = 0;
    public static int $BLACK = 1;
    public static int $VOID = 0;
    public static int $CUBE = 1;
    public static int $CONE = 2;
    public static int $CYLINDRE = 3;
    public static int $SPHERE = 4;

    protected int $forme;
    protected int $couleur;

    private function __construct(int $forme, int $couleur)
    {
        $this->forme = $forme;
        $this->couleur = $couleur;
    }

    /**
     * @return int
     */
    public function getForme(): int
    {
        return $this->forme;
    }

    /**
     * @return int
     */
    public function getCouleur(): int
    {
        return $this->couleur;
    }

    public function __toString(): String
    {
        // TODO: Implement __toString() method.
        $chaine = "(";
        switch ($this->forme) {
            case "0":
                $chaine.="&nbsp;&nbsp;&nbsp;&nbsp;";
                break;
            case "1":
                $chaine.="Cu:";
                break;
            case "2":
                $chaine.="Co:";
                break;
            case "3":
                $chaine.="Cy:";
                break;
            case "4":
                $chaine.="Sp:";
                break;
            default:
                $chaine.="";
        }
        if($this->forme != self::$VOID) {
            switch ($this->couleur) {
                case "0":
                    $chaine.="W";
                    break;
                case "1":
                    $chaine.="B";
                    break;
                default:
                    $chaine.="";
            }
        }
        return $chaine.")";
    }

    public static function initVoid(): PieceQuantik
    {
        return new PieceQuantik(self::$VOID, self::$WHITE);
    }

    public static function initWhiteCube(): PieceQuantik
    {
        return new PieceQuantik(self::$CUBE, self::$WHITE);
    }
    public static function initBlackCube(): PieceQuantik
    {
        return new PieceQuantik(self::$CUBE, self::$BLACK);
    }

    public static function initWhiteCone(): PieceQuantik
    {
        return new PieceQuantik(self::$CONE, self::$WHITE);
    }
    public static function initBlackCone(): PieceQuantik
    {
        return new PieceQuantik(self::$CONE, self::$BLACK);
    }

    public static function initWhiteCylindre(): PieceQuantik
    {
        return new PieceQuantik(self::$CYLINDRE, self::$WHITE);
    }
    public static function initBlackCylindre(): PieceQuantik
    {
        return new PieceQuantik(self::$CYLINDRE, self::$BLACK);
    }

    public static function initWhiteSphere(): PieceQuantik
    {
        return new PieceQuantik(self::$SPHERE, self::$WHITE);

    }
    public static function initBlacksphere(): PieceQuantik
    {
        return new PieceQuantik(self::$SPHERE, self::$BLACK);

    }
}
