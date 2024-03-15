<?php
class AbstractUIGenerator{

    public static function getDebutHTML(String $title="Jeu Quantik"): String{
        $chaine="<!DOCTYPE html>
                    <html class='no-js' lang='fr' dir='ltr' style='background-color: #cac5c5;'>
                    <head>
                        <meta charset=\"utf-8\">
                        <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
                        <title>$title</title>
                        <link rel=\"stylesheet\" href=\"https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css\">
                        <link rel='stylesheet' href='style.css'>
                        <style>
        body, html {
            height: 100%;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
                background-color: #cac5c5;

        }

        .container {
            text-align: center;
        }
        
        .fieldset {
            text-align: justify ;
        }
    </style>
                    </head>
                    <body>";
    return $chaine;
    }

    public static function getFinHTML(): String
    {
        $chaine='</body>
</html>';
        return $chaine;
    }

    public static function getPageErreur(String $message, String $urlLien) : String
    {
        return self::getDebutHTML().$message.'\n'.$urlLien.self::getFinHTML();
    }

}
