<?php

/**
 * @author Martin Nakládal
 * @copyright 2013
 * @version 1.0
 * @package smoce book
 */
class vypocet{

    /**
     * Převod textu na číslo
     * @param String $string text
     * @return Integer number 
     */
    public function parseInt( $string ) {
        //	return intval($string);
        if(preg_match('/(\d+)/', $string, $array)) {
            return (integer) $array[1];
        } else {
            return (integer) 0;
        }
    }

    /**
     * Testovaci prototip vzdalenosti lokatoru, a slysitelnosti podle kilometru
     * @param String $nmv nadmořská výška playera
     * @param String $nmvrep nadmořská výška převaděče
     * @param String $km vzdálenost od sebe
     * @return Array Data
     */
    public function dostupnost( $nmv, $nmvrep, $km ) {
        $status = 0;

        if($km > 200) {
            $mat = abs($nmv - $nmvrep);
            if($mat >= 1200) {
                $status = 5;
            }
        } elseif($km > 110) {
            $mat = abs($nmv - $nmvrep);
            if($mat >= 650) {
                $status = 4;
            }
        } elseif($km > 80) {
            $mat = abs($nmv - $nmvrep);
            if($mat >= 450) {
                $status = 3;
            }
        } elseif($km > 45) {
            $mat = abs($nmv - $nmvrep);
            if($mat >= 150) {
                $status = 2;
            }
        } elseif($km < 20) {
            $status = 1;
        }

        switch($status) {
            case 0:
                $saw = "Asi ne";
                $cor = "FFFFFF";
                break;
            case 1:
                $saw = "s 90% pojede";
                $cor = "64FF00";
                break;
            case 2:
                $saw = "70%  ze pojede";
                $cor = "CBFF00";
                break;
            case 3:
                $saw = "40% ze pojede";
                $cor = "D03200";
                break;
            case 4:
                $saw = "10% ze pojede";
                $cor = "646C74";
                break;
            case 5:
                $saw = "Asi ne";
                $cor = "";
                break;
            default:
                $saw = "Asi ne";
                $cor = "";
        }

        return array($saw, $cor);
    }
            
    private function TestLokatoru( $lokator ) {
        if(strlen($lokator) != 6) {
            echo"Lokator musi mit sest znaku .. opravte prosim lokator";
            return false;
        }
        //if(!is_int($lokator[2]) || !is_int($lokator[3])){echo"2 a 3 znak mus� b�t ��slice !";return false;}
        return true;
    }

    private function PrevedNaZnaky( $t ) {
        $H = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
        $G = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');

        $count = count($G);
        $cs = count($H);

        $i = 0;
        $d = 0;

        for($i = 0; $i < $count; $i++) {
            if($t == $G[$i]) {
                $qso = $i + 65;
                return $qso;
            }
        }
        for($d = 0; $d < $cs; $d++) {
            if($t == $H[$d]) {
                $qse = $d + 48;
                return $qse;
            }
        }
    }

    private function zdelka( $lokator ) {
        $n0 = $this->PrevedNaZnaky($lokator[0]);
        $n2 = $this->PrevedNaZnaky($lokator[2]);
        $n4 = $this->PrevedNaZnaky($lokator[4]);
        $op = (($n0 - 74) * 20 + ($n2 - 48) * 2 + ($n4 - 65) / 12.0) / 180 * pi();
        return $op;
    }

    private function zsirka( $lokator ) {
        $n1 = $this->PrevedNaZnaky($lokator[1]);
        $n3 = $this->PrevedNaZnaky($lokator[3]);
        $n5 = $this->PrevedNaZnaky($lokator[5]);
        $fq = (($n1 - 74) * 10 + ($n3 - 48) + ($n5 - 65) / 24.0) / 180 * pi();
        return $fq;
    }

    /**
     * Zjištění vzdálenosti a azimutu 
     * @param String $loc1 lokátor 
     * @param String $loc2 lokátor 
     * @return array vzdálenost, azimut
     */
    public function vzdalenost_smer( $loc1, $loc2 ) {
        $loc1 = strtolower($loc1); //znaky->male
        $loc2 = strtolower($loc2); //znaky->male

        $TLQ = 0;
        $TFQ = 0;
        $SLQ = 0;
        $SGQ = 0;
        $smer = 0;
        $ss = 0;
        $x1 = 0;
        $x2 = 0;

        if($this->TestLokatoru($loc1)) {
            //echo "$loc1";
        }

        if($this->TestLokatoru($loc2)) {
            //echo "$loc2";
        }

        $SLQ = $this->zdelka($loc1);
        $SFQ = $this->zsirka($loc1);
        $TLQ = $this->zdelka($loc2);
        $TFQ = $this->zsirka($loc2);

        //echo $SLQ."(-)".$SFQ."(-)".$TFQ."(-)".$TFQ."<br>";
        $ss = ($SLQ - $TLQ);

        $x1 = cos($SFQ) * tan($TFQ);
        $x2 = sin($SFQ) * cos($ss);

        if($x1 > $x2) {
            $smer = 180 - (atan(sin($ss) / ($x1 - $x2)) + pi()) * 180 / pi();
        }
        if($x1 < $x2) {
            $smer = 180 - (atan(sin($ss) / ($x1 - $x2))) * 180 / pi();
        }
        $smers = $smer;

        while($smer >= 360) {
            $smer -= 360;
        }
        while($smer < 0) {
            $smer += 360;
        }
        $smer = round($smer);
        $cosL = cos($ss) * cos($SFQ) * cos($TFQ) + sin($SFQ) * sin($TFQ);
        $LX = round(63700 * acos($cosL));
        $LH = $this->parseInt($LX / 10);
        $LZ = $LX - $LH * 10;
        $L = $LH.','.$LZ;
        return array($L, $smer);
    }

}

?>