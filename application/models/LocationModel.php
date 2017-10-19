<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class LocationModel extends CI_Model{

    function __construct() {
        parent::__construct();
    }
    
    private $loc = [
        "bidan"=>array(
            "Janapria"=>array('user1'=>'Lekor','user2'=>'Saba','user3'=>'Pendem','user4'=>'Setuta','user5'=>'Jango','user6'=>'Janapria'),
            "Sengkol"=>array('user8'=>'Ketara','user9'=>'Sengkol','user10'=>'Sengkol','user11'=>'Kawo','user12'=>'Tanak Awu','user13'=>'Pengembur','user14'=>'Segala Anyar')),
        "vaksinator"=>array(
            "Janapria"=>array('nouser1'=>'Lekor','vaksin1'=>'Saba','nouser2'=>'Pendem','nouser3'=>'Setuta','nouser4'=>'Jango','nouser5'=>'Janapria'),
            "Sengkol"=>array('nouser1'=>'Ketara','nouser1'=>'Sengkol','nouser1'=>'Sengkol','nouser1'=>'Kawo','vaksin2'=>'Tanak Awu','nouser1'=>'Pengembur','nouser1'=>'Segala Anyar')),
        "gizi"=>array(
            "Janapria"=>array('nouser1'=>'Lekor','gizi1'=>'Saba','nouser2'=>'Pendem','nouser3'=>'Setuta','nouser4'=>'Jango','nouser5'=>'Janapria'),
            "Sengkol"=>array('nouser1'=>'Ketara','nouser1'=>'Sengkol','nouser1'=>'Sengkol','nouser1'=>'Kawo','gizi2'=>'Tanak Awu','nouser1'=>'Pengembur','nouser1'=>'Segala Anyar'))
        ];
    
    private $loc_id = [
            "Janapria"=>array('user1'=>'Lekor','user2'=>'Saba','user3'=>'Pendem','user4'=>'Setuta','user5'=>'Jango','user6'=>'Janapria'),
            "Sengkol"=>array('user8'=>'Ketara','user9'=>'Sengkol','user10'=>'Sengkol','user11'=>'Kawo','user12'=>'Tanak Awu','user13'=>'Pengembur','user14'=>'Segala Anyar')
        ];
    
    private $int_loc_id = [
            "bidan"=>array('user1'=>'Lekor','user2'=>'Saba','user3'=>'Pendem','user4'=>'Setuta','user5'=>'Jango','user6'=>'Janapria','user8'=>'Ketara','user9'=>'Sengkol','user11'=>'Kawo','user12'=>'Tanak Awu','user13'=>'Pengembur','user14'=>'Segala Anyar'),
            "gizi"=>array('gizi2'=>'Saba','gizi12'=>'Tanak Awu'),
            "vaksinator"=>array('vaksinator2'=>'Saba','vaksinator12'=>'Tanak Awu')
        ];
    
    private $dusun = [
        'Lekor'=>array(1=>"Lekor Barat","Lekor Timur","Lengkok Bunut","Sondo","Renge","Presak","Gulung","Taken Aken","Pepao Timur","Pepao Barat","Pepao Tengah","Pelapak","Walun"),
        'Saba'=>array(1=>"Jembe Barat","Jembe Timur","Jembe Utara","Pengempok","Suangke","Janggawana Selatan 1","Sengkerek","Lingkok Buak Barat","Lingkok Buak Tengah","Lingkok Buak Timur","Melati","Selek","Gundu","Masjaya","Presak Sanggeng","Tentram","Terentem","Keruak","Keruak Utara","Janggawana Selatan","Janggawana Utara","Janggawana Barat","Selek Direk","Sengkerek Timur","Tenges Enges"),
        'Pendem'=>array(1=>'Pendem','Karang Majelo','Gelondong','Maliklo','Montong Bile','Jelitong','Lekong Bangkon','Penuntut','Kuang','Piling','Jangka','Petorok','Gelung','Nyangget'),
        'Setuta'=>array(1=>'Siwi','Setuta Barat','Setuta Timur','Batu Belek','Liwung Satu','Liwung Dua','Juna','Biletawah','Nunang'),
        'Jango'=>array(1=>'Rungkang Timur','Rungkang Barat','Puntik Baru','Jango Selatan','Jango Utara','Kenyalu II','Kenyalu I','Grepek'),
        'Janapria'=>array(1=>'Perok Timur','Menyer','Perok Barat','Kedapang','Tempek Empek','Geong Manis','Nunang Utara','Pengebat','Sadah','Lokon','Janapria','Batu Bungus Utara','Montong Kesene','Batu Kembar Barat','Peresak Jenggang','Gempang','Batu Kembar Timur','Bukit Awas','Penambong','Bolor','Lemokek','Lambah Olot','Tonjong'),
        'Ketara'=>array(1=>'Sempalan','Sarah','Bagek Dewe','Dese','Dayen Rurung','Lebak','Sampet','Abe','Embung Rungkas'),
        'Sengkol'=>array(1=>'Kale','Piyang','Soweng','Semundal','Jomang','Penambong','Peresak','Pesarih','Sedo','Lotir','Belong','Sereneng','Sengkol I','Sengkol II','Junge','Gentang','Tajuk'),
        'Kawo'=>array(1=>'Karang Jangkong','Batu Bangke','Gonjong','Bale Montong I','Bumi Gora','Dayen Kubur','Gilik','Pance','Pengadang','Wareng','Bale Montong II','Gampung','Balen Along','Sarang Angin','Karang Daye','Gubuk Direk','Buntereng'),
        'Tanak Awu'=>array(1=>'Tanak Awu I','Tanak Awu II','Tanak Awu Bat','Singa','Perendek','Tatak','Reak I','Reak II','Selawang Timuq','Selawang Bat','Gantang Lauk','Gantang Bat','Gantang Daye','Jambek I','Jambek II','Rebile'),
        'Pengembur'=>array(1=>'Pengembur I','Pengembur II','Pengembur III','Penyampi','Batu Belek','Tawah','Perigi','Sinah','Siwang','Tamping','Sepit','Keramat'),
        'Segala Anyar'=>array(1=>'Anak Anjan','Kadik','Penupi','Karang Baru','Lamben','Tenang','Bolok')
    ];
    
    private $dusun_typo = [
        'Lekor'=>array('Gulung'=>'Gulung','Lekor+Timur'=>'Lekor Timur','Lekor Timur'=>'Lekor Timur','Lekor+Barat'=>'Lekor Barat','Lekor Barat'=>'Lekor Barat','Lendang+Jawe'=>'Pepao Barat','Lengkok Bunut'=>'Lengkok Bunut','Lengkok+Bunut'=>'Lengkok Bunut','Montong+Bile'=>'Pepao Tengah','Pelapak'=>'Pelapak','Pepao+Barat+I'=>'Pepao Barat','Pepao Barat I'=>'Pepao Barat','Pepao+Barat+II'=>'Pepao Barat','Pepao Barat II'=>'Pepao Barat','Pepao+Timur'=>'Pepao Timur','Pepao Timur'=>'Pepao Timur','Presak'=>'Presak','Renge'=>'Renge','Sondo'=>'Sondo','Taken-Aken'=>"Taken Aken",'Walun'=>'Walun','Lendang Jawe'=>'Pepao Barat','Menteger'=>'Pelapak','Berenge'=>'Pelapak','Embung Wile'=>'Gulung','Sandat'=>'Lekor Timur','Ambat'=>'Pelapak','Montong Bile'=>'Pepao Tengah','Wiyung'=>'Gulung','Lekor Tengah'=>'Lekor Timur','Belo'=>'Walun','Selaping'=>'Gulung','Bare Putih'=>'Bare Putih','Dongger'=>'Dongger','Lempenge'=>'Lempenge'),
        'Saba'=>array("Tenges Enges"=>"Tenges Enges","Sengkerek Timur"=>"Sengkerek Timur","Selek Direk"=>"Selek Direk","Jembe+Barat"=>"Jembe Barat","Jembe+Timur"=>"Jembe Timur","Pengempok"=>"Pengempok","Suangke"=>"Suangke","Janggawana"=>"Janggawana","Sengkerek"=>"Sengkerek","Lingkok+Buak+Barat"=>"Lingkok Buak Barat","Lingkok+Buak+Tengah"=>"Lingkok Buak Tengah","Lingkok+Buak+Timur"=>"Lingkok Buak Timur","Melati"=>"Melati","Selek"=>"Selek","Gundu"=>"Gundu","Masjawa"=>"Masjaya","Presak+Sanggeng"=>"Presak Sanggeng","Tentram"=>"Tentram","Terentem"=>"Terentem","Keruak"=>"Keruak","Keruak Utara"=>"Keruak Utara","Masjaya"=>"Masjaya","Presak Sanggeng"=>"Presak Sanggeng","Janggawana+Selatan"=>"Janggawana Selatan","Janggawana+Utara"=>"Janggawana Utara","Janggawana+Tengah"=>"Janggawana Tengah","Janggawana Selatan"=>"Janggawana Selatan","Janggawana Utara"=>"Janggawana Utara","Lingkok Buak Barat"=>"Lingkok Buak Barat","Jembe Utara"=>"Jembe Utara","Jembe Barat"=>"Jembe Barat","Jembe Timur"=>"Jembe Timur","Lingkok Buak Tengah"=>"Lingkok Buak Tengah","Lingkok Buak Timur"=>"Lingkok Buak Timur"),
        'Pendem'=>array("Pendem"=>"Pendem","Piling"=>"Piling","Maliklo"=>"Maliklo","Jelitong"=>"Jelitong","Karang+Majelo"=>"Karang Majelo","Karang Majelo"=>"Karang Majelo","Penuntut"=>"Penuntut","Kuang"=>"Kuang","Jangka"=>"Jangka","Petorok"=>"Petorok","Gelung"=>"Gelung","Gelondong"=>"Gelondong","Nyangget"=>"Nyangget","Montong+Bile"=>"Montong Bile","Montong Bile"=>"Montong Bile","Lekong+Bangkon"=>"Lekong Bangkon","Lekong Bangkon"=>"Lekong Bangkon"),
        'Setuta'=>array("Juna"=>"Juna","Nunang"=>"Nunang","Batu+Belek"=>"Batu Belek","Batu Belek"=>"Batu Belek","Siwi"=>"Siwi","Setuta+Barat"=>"Setuta Barat","Setuta Barat"=>"Setuta Barat","Setuta+Timur"=>"Setuta Timur","Setuta Timur"=>"Setuta Timur","Liwung"=>"Liwung","Liwung_Selatan"=>"Liwung Selatan","Biletawah"=>"Biletawah","Liwung+Satu"=>"Liwung Satu","Liwung Satu"=>"Liwung Satu","Liwung+Dua"=>"Liwung Dua","Liwung Dua"=>"Liwung Dua","Nunang+Selatan"=>"Nunang Selatan"),
        'Jango'=>array("Rungkang+Timur"=>"Rungkang Timur","Rungkang Timur"=>"Rungkang Timur","Rungkang+Barat"=>"Rungkang Barat","Rungkang Barat"=>"Rungkang Barat","Puntik+Baru"=>"Puntik Baru","Puntik Baru"=>"Puntik Baru","Jango+Selatan"=>"Jango Selatan","Jango Selatan"=>"Jango Selatan","Jango Utara"=>"Jango Utara","Kenyalu+Utara"=>"Kenyalu II","Kenyalu Utara"=>"Kenyalu II","Kenyalu+Barat"=>"Kenyalu I","Kenyalu Barat"=>"Kenyalu I","Kenyalu+Selatan"=>"Kenyalu I","Kenyalu Selatan"=>"Kenyalu I","Kenyalu+Timur"=>"Kenyalu II","Kenyalu Timur"=>"Kenyalu II","Kampung+Baru"=>"Grepek","Kampung Baru"=>"Grepek","Arba"=>"Jango Selatan","Batu Ngereng"=>"Jango Selatan","Gerepek"=>"Grepek","Jango+Utara"=>"Jango Utara"),
        'Janapria'=>array("Bolor"=>"Bolor","Bukit Awas"=>"Bukit Awas","Gempang"=>"Gempang","Peresak Jenggang"=>"Peresak Jenggang","Montong Kesene"=>"Montong Kesene","Batu Bungus Utara"=>"Batu Bungus Utara","Lokon"=>"Lokon","Geong Manis"=>"Geong Manis","Kedapang"=>"Kedapang","Menyer"=>"Menyer","Janapria"=>"Janapria","Lemokek"=>"Lemokek","Tempek-Empek"=>"Tempek Empek","Tempek Empek"=>"Tempek Empek","Batu+Bangus"=>"Batu Bangus","Nunang+I"=>"Nunang Utara","Nunang I"=>"Nunang Utara","Nunang+Utara"=>"Nunang Utara","Nunang Utara"=>"Nunang Utara","Perok+Timur"=>"Perok Timur","Perok Timur"=>"Perok Timur","Batu+Kembar+II"=>"Batu Kembar Timur","Batu Kembar II"=>"Batu Kembar Timur","Batu+Kembar+I"=>"Batu Kembar Barat","Batu Kembar I"=>"Batu Kembar Barat","Pengebat"=>"Pengebat","Sadah"=>"Sadah","Penambong"=>"Penambong","Tonjong"=>"Tonjong","Pendem"=>"Pendem","Perok+Barat"=>"Perok Barat","Perok Barat"=>"Perok Barat","Lambah+Olot"=>"Lambah Olot","Lambah Olot"=>"Lambah Olot"),
        'Ketara'=>array("Dese"=>"Dese","Abe"=>"Abe","Sampet"=>"Sampet","Sempalan"=>"Sempalan","Selak"=>"Lebak","Dayen+Rurung"=>"Dayen Rurung","Dayen Rurung"=>"Dayen Rurung","Embung+Rungkas"=>"Embung Rungkas","Embung Rungkas"=>"Embung Rungkas","Reban"=>"Sarah","Plangsang"=>"Bagek Dewe","Lebak"=>"Lebak","Bagek+Payung"=>"Lebak","bagek payung"=>"Lebak","Sarah"=>"Sarah","Bagek+Dewe"=>"Bagek Dewe","Perigi"=>"Abe","Bagek Dewe"=>"Bagek Dewe","Enggaek"=>"Sempalan","Sarah Botok"=>"Sarah","Karang Bayan"=>"Bagek Dewe","Ular Naga"=>"Sampet","Napur"=>"Sampet","Gendang"=>"Sampet","Penyeleng"=>"Abe","Godok"=>"Abe","Mange"=>"Abe","Bikan"=>"Abe","Pait"=>"Abe"),
        'Sengkol'=>array("Piyang"=>"Piyang","Kale"=>"Kale","Belong"=>"Belong","Semundal"=>"Semundal","Jomang"=>"Jomang","Lotir"=>"Lotir","Sengkol+I"=>"Sengkol I","Sengkol I"=>"Sengkol I","Gentang"=>"Gentang","Sekong"=>"Sekong","Sedo"=>"Sedo","Kekale"=>"Kekale","Tajuk"=>"Tajuk","Puji+Rahayu"=>"Puji Rahayu","Puji Rahayu"=>"Puji Rahayu","Junge"=>"Junge","Sereneng"=>"Sereneng","Kale"=>"Kale","Sengkol+II"=>"Sengkol II","Sengkol II"=>"Sengkol II","Pesarih"=>"Pesarih","Penambong"=>"Penambong","Peresak"=>"Peresak","Senundal"=>"Senundal","Soweng"=>"Soweng"),
        'Kawo'=>array("Karang+Jangkong"=>"Karang Jangkong","Karang Jangkong"=>"Karang Jangkong","Gilik"=>"Gilik","Karang+Daye"=>"Karang Daye","Karang Daye"=>"Karang Daye","Balen+Along"=>"Balen Along","Bale+Montong+I"=>"Bale Montong I","Gubuk+Direk"=>"Gubuk Direk","Gubuk Direk"=>"Gubuk Direk","Pengadang"=>"Pengadang","Sarang+Angin"=>"Sarang Angin","Sarang Angin"=>"Sarang Angin","Dayen+Kubur"=>"Dayen Kubur","Dayen Kubur"=>"Dayen Kubur","Bale+Montong+II"=>"Bale Montong II","Gonjong"=>"Gonjong","Gampung"=>"Gampung","Taman+Bumi+Gora"=>"Bumi Gora","Buntereng"=>"Buntereng","Wareng"=>"Wareng","Pance"=>"Pance","Bumi+Gora"=>"Bumi Gora","Batu+Bangke"=>"Batu Bangke","Batu Bangke"=>"Batu Bangke","Bumi Gora"=>"Bumi Gora","Bale Montong I"=>"Bale Montong I","Balen Along"=>"Balen Along","Bale Montong II"=>"Bale Montong II"),
        'Tanak Awu'=>array("Singa"=>"Singa","Perendek"=>"Perendek","Tanak+Awu+Bat"=>"Tanak Awu Bat","Tanak+Awu+I"=>"Tanak Awu I","Perendik"=>"Perendek","Gantang+Daye"=>"Gantang Daye","Tanak+Awu+II"=>"Tanak Awu II","Rebile"=>"Rebile","Tatak"=>"Tatak","Reak+II"=>"Reak II","Reak+I"=>"Reak I","Gantang+Lauk"=>"Gantang Lauk","Gantang+Bat"=>"Gantang Bat","Gantang+Timuk"=>"Gantang Timuk","Selawang+Timuq"=>"Selawang Timuq","Selawang+Bat"=>"Selawang Bat","Selawang Bat"=>"Selawang Bat","Jambek+II"=>"Jambek II","Jambek+I"=>"Jambek I","Gantang Daye"=>"Gantang Daye","Tanak Awu Bat"=>"Tanak Awu Bat","Reak I"=>"Reak I","Reak II"=>"Reak II","Selawang Timuq"=>"Selawang Timuq","Gantang Bat"=>"Gantang Bat","Jambek II"=>"Jambek II","Jambek I"=>"Jambek I","Tanak Awu II"=>"Tanak Awu II","Gantang Lauk"=>"Gantang Lauk","Tanak Awu I"=>"Tanak Awu I"),
        'Pengembur'=>array("Pengembur+III"=>"Pengembur III","Rajan"=>"Pengembur I","Tamping"=>"Tamping","Sepit"=>"Sepit","Penyampi"=>"Penyampi","Siwang"=>"Siwang","Perigi"=>"Perigi","Keramat"=>"Keramat","Tawah"=>"Tawah","Pengembur+II"=>"Pengembur II","Sinah"=>"Sinah","Pengembur+I"=>"Pengembur I","Batu+Belek"=>"Batu Belek","Pengembur I"=>"Pengembur I","Batu Belek"=>"Batu Belek","Pengembur II"=>"Pengembur II","Pengembur III"=>"Pengembur III"),
        'Segala Anyar'=>array("Bolok"=>"Bolok","Anak+Anjan"=>"Anak Anjan","Penupi"=>"Penupi","Kadik+I"=>"Penupi","Kadik I"=>"Penupi","Karang+baru"=>"Karang Baru","Karang baru"=>"Karang Baru","Tenang"=>"Tenang","Lamben"=>"Lamben","Tuban"=>"Anak Anjan","Segale"=>"Anak Anjan","Tenang+Baru"=>"Tenang","Tenang Baru"=>"Tenang","Kadik+II"=>"Kadik","Anak Anjan"=>"Anak Anjan","Kadik II"=>"Kadik","Dasan Duah"=>"Kadik")
    ];
    
    public function getAllLoc($fhw){
        return $this->loc[$fhw];
    }
    
    public function getAllLocSpv($fhw,$kec){
        return [$kec=>$this->loc[$fhw][$kec]];
    }
    
    public function getLocId($kec){
        return $this->loc_id[$kec];
    }
    
    public function getIntLocId($fhw){
        return $this->int_loc_id[$fhw];
    }
    
    public function getLocIdQuery($locId){
        $location = '';
        foreach ($locId as $loc=>$id){
            $location .= "userID LIKE '%$loc%'";
            if($id!=  end($locId)) $location .= " OR ";
        }
        return $location;
    }
    
    public function getLocIdAndDesabyDesa($desa){
        foreach ($this->loc_id as $kec=>$desas){
            if($ret = array_search($desa, $desas)) return [$ret=>$desa];
        }
    }
    
    public function getLocIdbyDesa($desa){
        foreach ($this->loc_id as $kec=>$desas){
            if($ret = array_search($desa, $desas)) return $ret;
        }
    }
    
    public function getDesa($fhw,$kec){
        return $this->loc[$fhw][$kec];
    }
    
    public function getKecUsers($fhw,$kec){
        $users = [];
        foreach ($this->loc[$fhw][$kec] as $user=>$loc){
            array_push($users, $user);
        }
        return $users;
    }
    
    public function getKecFromDesa($namadesa){
        $namadesa = str_replace('%20',' ',$namadesa);
        foreach ($this->loc['bidan'] as $kec=>$loc){
            if(array_search($namadesa,$loc)){
                return $kec;
            }
        }
    }
    
    public function getKecFromUser($fhw,$userid){
        foreach ($this->loc[$fhw] as $kec=>$loc){
            if(array_key_exists($userid, $loc)){
                return $kec;
            }
        }
    }

    public function getDesaFromDusun($dusun){
        foreach ($this->dusun_typo as $desa=>$dusun_list){
            if(array_search($dusun,$dusun_list)){
                return $desa;
            }
        }
    }
    
    public function getUserFromDusun($fhw,$dusun){
        $desa = $this->getDesaFromDusun($dusun);
        foreach ($this->loc[$fhw] as $kec=>$desa_list){
            if($user = array_search($desa,$desa_list)){
                return $user;
            }
        }
    }
    
    public function getKecFromDusun($fhw,$dusun){
        return $this->getKecFromDesa($this->getDesaFromDusun($dusun));
    }

    public function getDesaUser($fhw,$kec,$desa){
        foreach ($this->loc[$fhw][$kec] as $user=>$loc){
            if($loc==$desa) return $user;
        }
    }
    
    
    
    public function getDesaFromUser($fhw,$userid){
        foreach ($this->loc[$fhw][$this->getKecFromUser($fhw,$userid)] as $user=>$loc){
            if($user==$userid) return $loc;
        }
    }
    
    public function getDusun($desa){
        return $this->dusun[$desa];
    }
    
    public function getDusunTypo($desa){
        return $this->dusun_typo[$desa];
    }

    public function getOneDusunTypo($desa,$dusun){
        $dusuns =  $this->dusun_typo[$desa];
        $res = [];
        foreach ($dusuns as $typo => $ori) {
            if($ori==$dusun){
                $res[$typo] = $ori;
            }
        }
        return $res;
    }
}