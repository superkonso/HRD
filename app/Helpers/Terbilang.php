<?php

namespace SIMRS\Helpers;

class Terbilang{

       public static function kekata($x){
            $x=abs($x);
            $angka=array("","Satu","Dua","Tiga","Empat","Lima",
            "Enam","Tujuh","Delapan","Sembilan","Sepuluh","Sebelas");
            $temp="";
            if($x<12){
                $temp=" ".$angka[$x];
            }elseif($x<20){
                $temp=Terbilang::kekata($x-10)." Belas";
            }elseif($x<100){
                $temp=Terbilang::kekata($x/10)." Puluh".Terbilang::kekata($x%10);
            }elseif($x<200){
                $temp=" Seratus".Terbilang::kekata($x-100);
            }elseif($x<1000){
                $temp=Terbilang::kekata($x/100)." Ratus".Terbilang::kekata($x%100);
            }elseif($x<2000){
                $temp=" Seribu".Terbilang::kekata($x-1000);
            }elseif($x<1000000){
                $temp=Terbilang::kekata($x/1000)." Ribu".Terbilang::kekata($x%1000);
            }elseif($x<1000000000){
                $temp=Terbilang::kekata($x/1000000)." Juta".Terbilang::kekata($x%1000000);
            }elseif($x<1000000000000){
                $temp=Terbilang::kekata($x/1000000000)." Milyar".Terbilang::kekata(fmod($x,1000000000));
            }elseif($x<1000000000000000){
                $temp=Terbilang::kekata($x/1000000000000)." Trilyun".Terbilang::kekata(fmod($x,1000000000000));
            }    
                return $temp;
        }
         
         
       public static function terbilang($x,$style=4){
            if($x<0){
                $hasil="Minus ".trim(Terbilang::kekata($x));
            }else{
                $hasil=trim(Terbilang::kekata($x));
            }    
            switch($style){
                case 1:
                    $hasil=strtoupper($hasil);
                    break;
                case 2:
                    $hasil=strtolower($hasil);
                    break;
                case 3:
                    $hasil=ucwords($hasil);
                    break;
                default:
                    $hasil=ucfirst($hasil);
                    break;
            }    
            return $hasil.' Rupiah';
        }
}