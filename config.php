<?php
#see klass on andmebaasiga suhtlemiseks

class config
{
    public function connecttoDB() {
        $conn = pg_connect("host='localhost' port=5432 dbname=postgres user=postgres password=postgres")
            or die("Ei saa ühendada andmebaasiga");
        #kontrollin ühendust
        if ($conn){
            echo('Andmebaasiga Postgres edukalt ühendatud!: <br>');
            #var_dump($conn);
        } else {
            echo('Ei suutnud andmebaasiga ühendada! <br>');
        }
        return  $conn;
    }

    public function createDatabase($conn) {
        $sqlList = "CREATE TABLE IF NOT EXISTS public.user (
        id integer PRIMARY KEY NOT NULL GENERATED ALWAYS AS IDENTITY ,
        code character varying(250) ,
        dep character varying(25) ,
        first character varying(49) ,
        last character varying(49) ,
        email character varying(250) ,
        visit_time character varying(250) ,
        id_code character varying(20) 
        )";

        $result = pg_query($conn,$sqlList);
        $query= "INSERT INTO public.user(code, dep, first,  last, email, visit_time, id_code) VALUES ('X+zrZv/IbzjZUnhsbWlsecLbwjndTpG0ZynXOif7V+k', 'LAB', 'Marielle', 'Lepson', 'lepson.marielle@gmail.com', '2020-09-18 05:44:01', '49708272732');";
        $query .= "INSERT INTO public.user(code, dep, first,  last, email, visit_time, id_code) VALUES ('n+zrZv/IbzjZUskdjssdsddsdsk', 'SYNLAB', 'Kaisa', 'Hansen', 'kaisa@gmail.com', '2016-09-18 16:44:01', '42108272732');";
        $query .= "INSERT INTO public.user(code, dep, first,  last, email, visit_time, id_code) VALUES ('s+zrZv/IbzjZUnhsbWlsda0ZynXOif7V+k', 'LTKH', 'Siim', 'Saar', 's.saar@gmail.com', '2015-01-20 21:44:01', '36593282393');";
        $query .= "INSERT INTO public.user(code, dep, first,  last, email, visit_time, id_code) VALUES ('jfhksdfadhfukwhefuw3344if7V+k', 'PERH', 'Jaan', 'Paat', 'paat@djsf.com', '2009-09-18 05:44:01', '34889340438');";

        $result2 = pg_query($conn, $query);

        return $result;
    }

    public function addData($conn, $lae_ules, $file, $name) {
        #peale nupu vajutust luuakse kaust upload
        $dir = "upload";
        $result = null;
        define('MB', 1048576);
        if( is_dir($dir) === false ) {
            mkdir($dir);
        }

        try {
            if (isset($_POST[$lae_ules])) {
                $failidelugemine = count($_FILES[$file][$name]);
                $sihtmärk_dir = "upload/";
                #kui faile on ühe 4 korraga esitatud]
                if ($failidelugemine >= 4) { #aktsepteerib maksimaalselt 3 faili korraga
                    throw new RuntimeException('Sisestatud liiga palju faile. Proovi uuesti!');
                }

                for ($j = 0; $j < $failidelugemine; $j++) { #vaatame failid läbi järjest
                    $failinimi = $_FILES[$file][$name][$j];
                    $sihtmärk_fail = $sihtmärk_dir . basename($failinimi);
                    #sisestatud faili arvude kontrollimine
                    // Faili suuruse kontroll
                    $faili_suurus = $_FILES[$file]['size'][$j];
                    if ($faili_suurus > 5 * MB) {
                        throw new RuntimeException('Error: sisestatud fail on liiga suur.');
                        break;
                    }
                    //errori kontroll
                    switch ($_FILES[$file]['error'][$j]) {
                        case UPLOAD_ERR_OK:
                            break;
                        case UPLOAD_ERR_NO_FILE:
                            throw new RuntimeException('Faili ei esitatud.');
                        case UPLOAD_ERR_INI_SIZE:
                        case UPLOAD_ERR_FORM_SIZE:
                            throw new RuntimeException('Fail ületas suuruse.');
                        default:
                            throw new RuntimeException('Määramata errorid');
                    }

                    #faili salvestamise kontroll
                    move_uploaded_file($_FILES[$file]['tmp_name'][$j], $sihtmärk_dir . $failinimi);
                    #move_uploaded_file($_FILES[$file]['tmp_name'][$j], $sihtmärk_dir . $failinimi);
                    $failiTüüp = strtolower(pathinfo($sihtmärk_fail, PATHINFO_EXTENSION)); // Tagastab failitüübi

                    $i = 0;
                    $semikoolon = True;
                    $pealkirjad = array();
                    $uuspealkirjad = ["","", "", "", "", "",""];
                    [$eikuulukuskile, $eesnimeIndeks, $perekonnanimeIndeks, $koodIndeks, $idIndeks, $depIndeks, $visittimeIndeks, $emailIndeks]  = 0;
                    foreach (file($sihtmärk_fail) as $line) { # rida rea haaval
                        $line = trim($line);
                        if ($i == 0) {
                            $semikoolonite_arv =  substr_count($line,';');
                            $tabulaatorite_arv =  substr_count($line, "\t");
                            if ($semikoolonite_arv == 6) {
                                $pealkirjad = explode(';', $line);
                                $semikoolon =True;
                            }else if ($tabulaatorite_arv == 6) {
                                $pealkirjad = explode("\t", $line);
                                $semikoolon=False;
                            }else {
                                throw new RuntimeException('Eraldatud teiste sümbolitega.');
                                break;
                            }
                            if (count($pealkirjad) != 7) { #kontroll, kas sisestatud tabeli veerge on kokku 7
                                throw new RuntimeException('Sisestatud on teistsuguste andmetega fail. Ei saa vastu võtta.');
                                break;
                            }
                            $lugeja= 0;
                            for ($i = 0; $i < count($pealkirjad); $i++) { #märgmine ära, mis igal veerul on
                                #$pealkirjad[$i] = preg_replace('/\s+/', '', $pealkirjad[$i]);
                                if ($pealkirjad[$i] == "first" || $pealkirjad[$i] == "eesnimi" || $pealkirjad[$i] == "first_name" ) {
                                    $eesnimeIndeks = $i;
                                    $uuspealkirjad[$i]= "first";
                                } elseif ($pealkirjad[$i] == "last" || $pealkirjad[$i] == "perekonnanimi" || $pealkirjad[$i] == "last_name") {
                                    $perekonnanimeIndeks = $i;
                                    $uuspealkirjad[$i]= "last";
                                } elseif ($pealkirjad[$i] == "id_code" || $pealkirjad[$i] == "isikukood" || strpos($pealkirjad[$i], "id") == True ) {
                                    $idIndeks = $i;
                                    $uuspealkirjad[$i]= "id_code";
                                } elseif ($pealkirjad[$i] == "code" || strpos($pealkirjad[$i], "code") == True ) {
                                    $koodIndeks = $i;
                                    $uuspealkirjad[$i]= "code";
                                } elseif ($pealkirjad[$i] == "dep") {
                                    $depIndeks = $i;
                                    $uuspealkirjad[$i]= "dep";
                                } elseif ($pealkirjad[$i] == "email" || $pealkirjad[$i] == "email ") {
                                    $emailIndeks = $i;
                                    $uuspealkirjad[$i]= "email";
                                } elseif ($pealkirjad[$i] == "visit_time") {
                                    $visittimeIndeks = $i;
                                    $uuspealkirjad[$i]= "visit_time";
                                }else {
                                    $eikuulukuskile+=1;
                                }
                                $lugeja+=1;
                            }
                        } else {
                            if ($semikoolon == True) { #kontroll, millega ülejäänud read eraldatud on
                                $tulemused = explode(';', $line);
                            }else $tulemused = explode("\t", $line);

                            $itul = str_replace(',', '.', $tulemused[$idIndeks] ); #sest muidu ei saa tabelisse sisestada
                            $isikukoodtulemus = strval((float) $itul);

                            $query = "INSERT INTO public.user($uuspealkirjad[$koodIndeks], $uuspealkirjad[$depIndeks], $uuspealkirjad[$eesnimeIndeks],  $uuspealkirjad[$perekonnanimeIndeks], $uuspealkirjad[$emailIndeks], $uuspealkirjad[$visittimeIndeks], $uuspealkirjad[$idIndeks]) VALUES ('$tulemused[$koodIndeks]', '$tulemused[$depIndeks]', '$tulemused[$eesnimeIndeks]', '$tulemused[$perekonnanimeIndeks]', '$tulemused[$emailIndeks]', '$tulemused[$visittimeIndeks]', '$isikukoodtulemus');";
                            $result = pg_query($conn, $query);
                        }
                        $i += 1;

                    }
                }

            }if(!$result){
                echo pg_last_error($conn);
            } else {
                echo "Andmed sisestatud!";
            }
        } catch (RuntimeException $e) {
            echo $e->getMessage();

        }

    }
}
?>