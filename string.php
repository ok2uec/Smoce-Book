<?php

/**
 * @author Martin Nakládal
 * @copyright 2013
 * @version 1.0
 * @package smoce book
 */
class string{

    /**
     * Přepis čistého textu na formátovaný html text. Vhodné pro úpravu popisu.
     * @param String $text neformátovaný text
     * @return String type formated HTML.  
     */
    public function formatedPlainTextToHtml( $text ) {
        $ted = explode("\n", $text);
        /* ul datas */
        $data = null;
        $deliver = false;
        /* bold element */
        $linesActive = false;
        foreach($ted as $text) {
            if(strlen($text) > 1) {
                $text = preg_replace("#((http|https|ftp)://(\S*?\.\S*?))(\s|\;|\)|\]|\[|\{|\}|,|\"|'|:|\<|$|\.\s)#ie", "'<a href=\"$1\" target=\"_blank\">$1</a>$4'", $text);
                $items = null;
                $deConviture = null;
                if(isset($text[0])) {
                    $firstChar = mb_convert_encoding($text[0], 'ISO-8859-15', 'UTF-8');
                    switch($firstChar) {
                        case '<':
                            if($deliver === true) {
                                $items = '</ul>'."\r\n";
                                $deliver = false;
                            }
                            $data .= $items.'<p>'.$text."<br>\r\n";
                            $linesActive = true;
                            break;
                        case '?':
                            if($linesActive === true) {
                                $deConviture = "</p>";
                                $linesActive = false;
                            }
                            if($deliver === false) {
                                $items = '<ul>'."\r\n";
                                $deliver = true;
                            }
                            $data .= $deConviture.$items.'<li>'.trim(substr($text, 2)).'</li>'."\n";
                            break;
                        default:
                            if($deliver === true) {
                                $items = '</ul>'."\r\n";
                                $deliver = false;
                            }
                            if($linesActive !== true) {
                                $deConviture = "<p>";
                            }
                            $data .= $items.$deConviture.$text.'</p>'."\n";
                    }
                }
            }
        }
        return $data;
    }

    /**
     * Odstranění diaktritiky z řetězce
     * @param String $puvodni string
     * @return String un-diakritiky
     */
    function stringconvert( $puvodni ) {
        $prevedeno = StrTr($puvodni, "áäčďéěëíňóöřšťúůüýžÁÄČĎÉĚËÍŇÓÖŘŠŤÚŮÜÝŽ", "aacdeeeinoorstuuuyzAACDEEEINOORSTUUUYZ");
        return $prevedeno;
    }

    /**
     * Vytvoření modrewrite z vloženého textu ideální na title
     * @param String $str title řetězec
     * @return String vrací řetězec bez diakritiky
     */
    function to_modrew( $str ) {
        $str = stripslashes(chop($str));
        $rep_table = array(
           "ě" => "e",
           "š" => "s",
           "č" => "c",
           "ř" => "r",
           "ž" => "z",
           "ý" => "y",
           "á" => "a",
           "í" => "i",
           "é" => "e",
           "ú" => "u",
           "ů" => "u",
           "ť" => "t",
           "ď" => "d",
           "ó" => "o",
           "ň" => "n",
           "Ě" => "E",
           "Š" => "S",
           "Č" => "C",
           "Ř" => "R",
           "Ž" => "Z",
           "Ý" => "Y",
           "Á" => "a",
           "Í" => "I",
           "É" => "E",
           "Ú" => "U",
           "Ů" => "U",
           "Ó" => "O",
           "Ď" => "D",
           "Ť" => "T",
           "Ň" => "N",
           "ľ" => "l",
           "-" => "_",
           " " => "_"
        );
        $str = stripslashes($str);
        foreach($rep_table as $what => $to) {
            $str = str_replace($what, $to, $str);
        }
        return $str;
    }

    /**
     * Zjištění délky slova s ošetřením BUGU diakritickým
     * @param string $word
     * @return int length 
     */
    public static function length( $word ) { 
        $output = utf8_decode($word);
        return strlen($output);
    }

}
