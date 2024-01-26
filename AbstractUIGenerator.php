<?php

class AbstractUIGenerator{

    protected static function getDebutHTML(String $title="title content"): String{
        $chaine='<!DOCTYPE html>
    <html>
    <head>
        <meta charset=\"utf-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
    <title>'.$title.'</title>
    <link rel=\"stylesheet\" href=\"https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css\">
</head>
<body>';
    return $chaine;
    }

    protected static function getFinHTML(): String
    {
        $chaine='</body>
</html>';
        return $chaine;
    }

    public static function getPageErreur(String $message, String $urlLien) : String
    {
        return $message.'\n'.$urlLien;
    }

}
