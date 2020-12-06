<!-- Marielle Lepson 2020-->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="main.css">
    <title>Veebilahendus</title>
</head>
<body>
<h1>Veebilahendus</h1>
<h2>Marielle Lepson</h2>
<p> Ülesandeks on luua veebilahendus, kahe põhi funktsionaalsusega</p>
<!-- Jagame veebilehe kaheks tulbaks -->
<div class="row">
    <div class="column" id="left">
        <h2>Kasutajal on võimalik antud näidis andmetabelid(3tk) sisse lugeda.</h2>
        <!-- Nupu vajutamine ja andmete laadimine-->
        <form method="post" action="" enctype='multipart/form-data'>
            <input type="file" name="file[]" id="file" multiple> <br>
            <input type='submit' id="sisesta" name='lae_ules' value='Lisa andmed andmebaasi'>
        </form>
        <?php
        include 'config.php'; #ühendamise fail
        $conn = new config();
        $conn = $conn->connecttoDB(); #loome ühenduse andmebaasiga
        $created = new config();
        $created = $created->createDatabase($conn);
        $result = new config();
        $result = $result->addData($conn,'lae_ules','file','name'); #laeme andmed andmebaasi

        ?>
    </div>

    <div class="column" id="right">
        <h2>Kasutaja saab vähemalt isikukoodi järgi otsida, et missugused read isiku kohta andmebaasis on</h2>
        <form action="" method="post" name ="otsing">
            Otsing: <input type="text" name="sisestus" />
            <select name="tüübid" id="tüübid">
                <option value="code">kood</option>
                <option value="dep">dep</option>
                <option value="first">eesnimi</option>
                <option value="last">perekonnanimi</option>
                <option value="email">email</option>
                <option value="visit_time">külastusaeg</option>
                <option value="id_code">isikukood</option>
            </select>
            <input type="submit" id="otsi"  value="Esita päring"/>
        </form>

        <?php
        if (!empty($_REQUEST['sisestus']) && !empty($_REQUEST['tüübid'])) {
            $term = ucfirst(pg_escape_string($_REQUEST['sisestus']));
            $tüüp = pg_escape_string($_REQUEST['tüübid']);
            $jatkub = '%';
            $lause = "SELECT id, code, dep, first, last, email, id_code, visit_time FROM public.user WHERE \"%s\" LIKE '%s%s';";
            $sql = sprintf($lause,$tüüp, $term, $jatkub);
            $query2 = pg_query($conn,$sql);
            if (!$query2) {
                echo "Ei teinud päringut\n";
                exit;
            }
            elseif (pg_num_rows($query2) > 0){
                $tabelipealkirjad = ["ID"," Kood", "Dep", "Eesnimi", "Perekonnanimi", "email", "ID kood", "Külastusaeg"];
                echo '<table class="tulemused"><thead style="font-size: 15px;line-height: 1.9;letter-spacing: 0.12em;font-weight: 500;font-style: normal; background-color: rgba(186,87,87,0.91); color: #ffffff; color: #ffffff; border: 1px solid #ddd;padding: 8px;"><tr>';
                foreach($tabelipealkirjad as $column) { #sättime tabelite veergude pealkirjad
                    echo '<th>'.$column.'</th>';
                }
                echo '</tr></thead><tbody style="font-size: 12px;line-height: 0.8;letter-spacing: 0.116em;font-weight: 500;font-style: normal; background-color: #fff; color: #b96e6e; padding: 8px; padding-top: 12px; padding-bottom: 12px;"';
                while ($row = pg_fetch_row($query2)) {
                    echo '<tr>';
                    for ($x = 0; $x < count($tabelipealkirjad); $x++) {
                        echo '<td>'.$row[$x].'</td>';
                    }
                    echo '</tr>';
                }
                echo '</tbody></table>';
            } else echo "Selliseid andmeid ei ole.";


        } else echo "Pole sisendit";
        pg_close($conn);
        ?>
    </div>
</div>

</body>
</html>
