<?php
require_once('src/php/db.php');
$db = db_connect();


function build_sequence($db, $next, $order, &$data_player){
    // Exit condition
    if(empty($next)) return "";

    // Building
    $data = db_query($db, "SELECT sequencer.*, audio.file, audio.name FROM sequencer LEFT JOIN audio ON sequencer.audio = audio.reference WHERE sequencer.reference = '$next'");
    $ref = $data['reference'];
    $name = empty($data['audio']) ? "- - EMPTY - -" : $data['name'];
    $file = $data['file'];
    $HTML = "
        <i class='glyphicon glyphicon-arrow-right' style='vertical-align=middle'></i>
        <div class='card'>
            <div class='row'>
                <div class='col-md-12'>
                    <div class='sample-title'>$name</div>
                    <div class='progress'>
                        <div id='progress-bar-$ref' class='progress-bar progress-bar-success' role='progressbar' style='width:0%'></div>
                    </div>
                    <button class='btn btn-info btn-player'><i class='glyphicon glyphicon-pencil'></i></button>
                </div>
            </div>
            <br/>

            <audio id='player-$ref' preload=none>
                <source src='src/audio/$file' type='audio/mpeg'>
            </audio>
        </div>
    ";

    // Player
    array_push($data_player, ['reference' => $ref]);
    $order++;
    
    // Next element
    $HTML .= build_sequence($db, $data['next'], $order, $data_player);

    // Return
    return $HTML;
}

$HTML_players = "";
$data_all_sequences = array();
$data_header = db_query_raw($db, "SELECT sequencer.*, audio.file, audio.name FROM sequencer LEFT JOIN audio ON sequencer.audio = audio.reference WHERE sequencer.header = 1 ORDER BY sequencer.reference");
while($row = mysqli_fetch_assoc($data_header)) {
    $ref = $row['reference'];
    $name = empty($row['audio']) ? "- - EMPTY - -" : $row['name'];
    $file = $row['file'];

    $HTML_players .= "
        <div class='wrapper'>
            <div class='card'>
                <div class='row'>
                    <div class='col-md-12'>
                        <div class='sample-title'>$name</div>
                        <div class='progress'>
                            <div id='progress-bar-$ref' class='progress-bar progress-bar-success' role='progressbar' style='width:0%'></div>
                        </div>
                        <button id='btn-play-$ref' class='btn btn-success btn-player' onclick='play($ref)'><i class='glyphicon glyphicon-fast-backward'></i> <i class='glyphicon glyphicon-play'></i></button>
                        <button id='btn-stop-$ref' class='btn btn-danger btn-player' onclick='stop($ref)'><i class='glyphicon glyphicon-stop'></i></button>
                        <button class='btn btn-info btn-player'><i class='glyphicon glyphicon-pencil'></i></button>
                    </div>
                </div>
                <br/>

                <audio id='player-$ref' preload=none>
                    <source src='src/audio/$file' type='audio/mpeg'>
                </audio>
            </div>
    ";

    // Header player
    $sequence = array();
    array_push($sequence, ['reference' => $ref]);

    // Every element of that chain
    $HTML_players .= build_sequence($db, $row['next'], 1, $sequence);
    $HTML_players .= "</div><br/>";

    // Add to general array
    $data_all_sequences = $data_all_sequences + [$ref => ['shortkey' => 'test', 'sequence' => $sequence]];
}

$JSON_data_sequence = json_encode($data_all_sequences, JSON_FORCE_OBJECT);

?>

<!DOCTYPE html>
<html lang="fr">
  <head>
    <?php include('src/html/header.html');?>
    <title>Soundboard - Sequencer</title>
  </head>

  <body onkeyup="key_pressed(event)">
    <!-- Navbar -->
    <?php include('src/php/navbar.php');?>

    <!-- Main -->
    <div class="main col-md-12">

        <!-- Players -->
        <?php echo $HTML_players ?>
        
    </div>

    <!-- Footer -->
    <?php include('src/html/footer.html');?>
    <script src='src/js/sequencer.js'></script>
    <script>
        var data_sequence = JSON.parse('<?php echo $JSON_data_sequence; ?>');

        $(document).ready(function() {
            document.getElementById("sequencer").className="active";
        });
    </script>
</body>
</html>