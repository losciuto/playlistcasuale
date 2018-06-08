<?PHP
  global $db, $conn, $truncate_tab;
  // effettua un truncate sulla tabella o meno
  $truncate_tab = false;
   // aggiorna la base dati o meno uso debug
  $db = true;
  // Original PHP code by Chirp Internet: www.chirp.com.au
  // Please acknowledge use of this code by including this header.
  function getFileList($dir, $recurse = FALSE)
  { 
  global $conn;
    $retval = [];
    // mime type incluse nella scansione
    $video = array('video/x-matroska',
                   'video/x-msvideo',
                   'video/mpeg',
                   'video/mp4',
                   'video/x-flv'
             );
    // add trailing slash if missing
    if(substr($dir, -1) != "/") {
      $dir .= "/";
    }
    // open pointer to directory and read list of files
    $d = @dir($dir) or die("\ngetFileList: Failed opening directory {$dir} for reading\n");
    while(FALSE !== ($entry = $d->read())) {
      // skip hidden files
      if($entry{0} == ".") continue;
      if(is_dir("{$dir}{$entry}")) {
	//continue;
	/*
        $retval[] = [
          'name' => "{$entry}/",
         'file' => "{$dir}{$entry}/",
          'type' => filetype("{$dir}{$entry}"),
          'size' => 0,
          'lastmod' => filemtime("{$dir}{$entry}")
        ];
	*/
        if($recurse && is_readable("{$dir}{$entry}/")) {
          $retval = array_merge($retval, getFileList("{$dir}{$entry}/", TRUE));
        }
      } elseif(is_readable("{$dir}{$entry}")) {
	if(in_array(mime_content_type("{$dir}{$entry}"),$video)){
        // esclude l'estensione .sub e l'estensione .vob
        if((strtolower(substr("{$entry}",-4)) != ".sub") && (strtolower(substr("{$entry}",-4)) != ".vob")){
            /*
                $retval[] = [
                    'name' => "'" . $conn->real_escape_string(substr("{$entry}",0,-4)) . "'",
                    'file' => "'" . $conn->real_escape_string("{$dir}{$entry}" ). "'",
                    'type' => "'" . $conn->real_escape_string(mime_content_type("{$dir}{$entry}")) . "'",
                    'size' => "'" . filesize("{$dir}{$entry}") . "'",
                    'lastmod' => "'" . date ("Y-m-d H:i:s", filemtime("{$dir}{$entry}"))."'"
                ];
                */
                $retval[] = [
                    'name' => "'" . substr("{$entry}",0,-4) . "'",
                    'file' => "'" . "{$dir}{$entry}" . "'",
                    'type' => "'" . "{$dir}{$entry}". "'",
                    'size' => "'" . filesize("{$dir}{$entry}") . "'",
                    'lastmod' => "'" . date ("Y-m-d H:i:s", filemtime("{$dir}{$entry}"))."'"
                ];
                echo ".";
        }
	} else {
	     //echo "File saltato: " . mime_content_type("{$dir}{$entry}") . "\n<br>";
        }
      }
    }
    $d->close();
    return $retval;
}
if($db) include_once("db.php");
// gestione dei parametri da riga di comando
$numdir = count($argv) -1;
$inizio = 1;
if($argv[1] == 0) {
    //$numdir--; 
    $inizio++; 
    $truncate_tab = false;
}  
if($argv[1] == 1) {
    //$numdir--; 
    $inizio++; 
    $truncate_tab = true;
    echo "La tabella " . $tabella . " verrà troncata (truncate)!!!\n";
}  else {
    echo "I dati verranno aggiunti alla tabella " . $tabella . "\n";
    }
//echo $numdir . " " . $inizio;
//echo $argv[1] == 1; exit ;
//$dir = getFileList('/media/root/TrekStor1/Films',TRUE);
//$dir = getFileList('/media/root/backup/Films',TRUE);
// ciclo scansione directory
for($j = $inizio; $j <= $numdir; $j++){
    echo "Fase raccolta dati dalla periferica directory: " . $argv[$j] . "...";    
    $dir = getFileList($argv[$j],TRUE);
    //echo $argv[$i] . "\n"; // debug
    echo "<br>\nFase ordinamento vettore...<br>\n";
    $ndir = array_sort($dir, 'file', SORT_ASC);
    if($db){
        echo "Fase aggiornamento base dati...";
        include('input.php');
    } else {
        // fase debug
        echo '<pre>';
        print_r($ndir);
        echo '</pre>';
}
//echo $j . "\n"; // debug
}
$conn = null;
// fine scandir

// funzione ordinamento vettori
// prelevato da php.net
function array_sort($array, $on, $order=SORT_DESC)
{
    $new_array = array();
    $sortable_array = array();
    if (count($array) > 0) {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $k2 => $v2) {
                    if ($k2 == $on) {
                        $sortable_array[$k] = $v2;
                    }
                }
            } else {
                $sortable_array[$k] = $v;
            }
        }
        switch ($order) {
            case SORT_ASC:
                asort($sortable_array);
            break;
            case SORT_DESC:
                arsort($sortable_array);
            break;
        }
        foreach ($sortable_array as $k => $v) {
            $new_array[$k] = $array[$k];
        }
    }
    return $new_array;
}
?>