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

}
