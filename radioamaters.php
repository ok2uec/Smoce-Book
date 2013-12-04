<?php

/**
 * @author Martin Nakládal
 * @copyright 2013
 * @version 1.0
 * @package smoce book
 */
class radioamaters{

    /**
     * Upraví tvar frekvence u herců, pokud nejsou herce tak vyplní nule 146,2 -> 126,200
     * @param String $freg frequency
     * @return String type frequency 
     */
    public function frequencyFormat( $freg ) {
        $pieces = explode(".", $freg);
        $pocet = strlen($pieces[1]);
        if($pocet == 1) {
            $A = $pieces[1]."00";
        } if($pocet == 2) {
            $A = $pieces[1]."0";
        } if($pocet >= 3) {
            $A = $pieces[1];
        }
        return $pieces[0].".".$A;
    }

    /**
     * Ověření koncese na ČTU webu.
     * @param String $callname callname
     * @return Array data ČTU
     */
    public function overeni_znacky_ar( $callname ) { //<---slovo
        $fraze = strtoupper($search);

        $url = "http://www.ctu.cz/ctu-online/vyhledavaci-databaze/databaze-pridelenych-radiovych-kmitoctu-podle-vydanych-pridelu-a-individualnich-opravneni.html?trideni=TCS_CALL&smer=ASC&stranka=1&prohledat=FILTROVAT&f_call=".urlencode($fraze)."&f_rozhod=&f_valid=&pageid=&action=amateri";
        $input = @file_get_contents($url);
        $info = explode('<table class="common">', $input);
        $info = explode("</table>", $info[1]);

        $infos = explode("<tbody>", $info[0]);
        $podrobnosti = explode("</tbody>", $infos[1]);

        $data = explode("<td>", $podrobnosti[0]);
        $datb = explode("</td>", $data[2]);
        $datc = explode("</td>", $data[3]);

        if($data[1] != "") {
            $a = $data[1];
        } else {
            $a = "Nenalezeno u CTU";
        }
        if($datb[0] != "") {
            $b = $datb[0];
        } else {
            $b = "Nenalezeno u CTU";
        }
        if($datc[0] != "") {
            $c = $datc[0];
        } else {
            $c = "Nenalezeno u CTU";
        }

        return array($a, $b, $c);
    }

    /**
     * Vypsání posledních připojených spojení na IRCDDB
     * @param String $callname callname
     * @return String data HTML table
     */
    public function dstarTable( $callname ) {//echoes($filename) 
        $add = "http://status.ircddb.net/cgi-bin/ircddb-log?30%200%20".$callname;
        $input = @file_get_contents($add);
        $info = explode('<table BORDER=0 BGCOLOR="white">', $input);
        $infoa = explode('</table>', $info[1]);
        $ms = ' <LINK REL="stylesheet" type="text/css" href="http://prevadece.smoce.net/css/ircddb.css"><table border="0" bgcolor="white"><tbody>'.$infoa[0].'</table>';
        return $ms;
    }

    /**
     * Zjištění souřadnic z lokátorů
     * @param String $locator callname
     * @return Array longtitude, latitude, north, south, west, east
     */
    public function locatorData( $locator ) {
        $grid = strtolower($locator);

        if(!preg_match("/[a-r]{2}[0-9]{2}[a-x]{2}/msi", $grid))
            $grid = "JN89OD";

        $longtitude = (ord($grid[0]) - ord('a')) * 20;
        $longtitude += $grid[2] * 2;
        $longtitude += (ord($grid[4]) - ord('a')) * (2 / 24) + (1 / 24);
        $longtitude -= 180;

        $latitude = (ord($grid[1]) - ord('a')) * 10;
        $latitude += $grid[3] + 0;
        $latitude += (ord($grid[5]) - ord('a')) * (1 / 24) + (1 / 48);
        $latitude -= 90;

        $north = $latitude + (1 / 48);
        $south = $latitude - (1 / 48);
        $west = $longtitude - (1 / 24);
        $east = $longtitude + (1 / 24);
        return array($longtitude, $latitude, $north, $south, $west, $east);
    }

}
