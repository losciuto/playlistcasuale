<?php
    include_once('db.php');
    $i = 0;    
    if($truncate_tab){
        $sql = "DELETE FROM " . $tabella . "";
        $conn->exec($sql);
        $sql = "UPDATE SQLITE_SEQUENCE SET seq = 0 WHERE name = '" . $tabella ."'";
        $conn->exec($sql);
        $sql="VACUUM";
        $conn->exec($sql);
        $truncate_tab = false;
    }
    // controlla l'ultimo id inserito nella tabella
    $sql = "SELECT MAX(id) as id FROM " . $tabella . ";";
    $result = $conn->query($sql);
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $i = $row['id'];
            echo "Ultimo id: " . $i . "\n";
        }
    $result = null;
    foreach ($ndir as $k => $v) {
        $i++;
        /*
        // gestisce l'id
        $sql = "INSERT OR IGNORE INTO " . $tabella . " (id,".
                implode(',',array_keys($v)).
                ") VALUES ($i,".
                implode(',',$v).
            ")";
        */
        // fa gestire l'id al dbms
        $sql = "INSERT OR FAIL INTO " . $tabella . " (" .
                implode(',',array_keys($v)).
                ") VALUES (" .
                implode(',',$v).
            ")";            
            echo ".";
            //echo $sql .  "\n";
            $conn->query($sql);
    }
    //$conn = null;
?>