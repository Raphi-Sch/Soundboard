<?php
require_once('src/php/db.php');
$db = db_connect();

$data = db_query_raw($db, "SELECT * FROM active, audio WHERE active.audio = audio.reference ORDER BY active.reference");
$HTML_players = "";
while($row = mysqli_fetch_assoc($data)) {
    $ref = $row['reference'];
    $name = $row['name'];
    $file = $row['file'];
    $volume = $row['volume'];
    $speed = $row['speed'];

    $HTML_players .= "
        <div class='card'>
            <div class='row'>
                <div class='col-md-12'>
                    <div class='sample-title'>$name</div>
                    <div class='progress'>
                        <div id='progress-bar-$ref' class='progress-bar progress-bar-success' role='progressbar' style='width:0%'></div>
                    </div>
                    <button id='btn-play-$ref' class='btn btn-success' onclick='play_pause($ref)'><i id='ico-play-$ref' class='glyphicon glyphicon-play'></i></button>
                    <button id='btn-load-$ref' class='btn btn-danger pull-right' onclick='load($ref)'><i id='ico-load-$ref' class='glyphicon glyphicon-stop'></i></button>
                </div>
            </div>

            <br/>
            <div class='row'>
                <div class='col-md-1'>
                    <i class='glyphicon glyphicon-volume-up'></i>
                </div>
                <div class='col-md-8'>
                    <input id='volume-range-$ref' type='range' min=0 max=1 step=0.05 value=$volume oninput='volume($ref)'>
                </div>
                <div class='col-md-2'>
                    <p id='volume-text-$ref'>".($volume * 100)."%</p>
                </div>
            </div>

            <br/>
            <div class='row'>
                <div class='col-md-1'>
                    <i class='glyphicon glyphicon-dashboard'></i>
                </div>
                <div class='col-md-8'>
                    <input id='speed-range-$ref' type='range' min=0.25 max=4 step=0.25 value='$speed' oninput='speed($ref)'>
                </div>
                <div class='col-md-2'>
                    <p id='speed-text-$ref'>$speed</p>
                </div>
            </div>

            <audio id='player-$ref' preload=none>
                <source src='src/audio/$file' type='audio/mpeg'>
            </audio>
        </div>
    ";
}

?>

<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <link href="src/bootstrap-3.3.7-dist/css/bootstrap.css" rel="stylesheet">
    <link href="src/css/dashboard.css" rel="stylesheet">
    <title>Soundboard</title>
  </head>

  <body>
    <!-- Main area -->
    <div class="main col-md-10 col-md-offset-1">
        <div class='wrapper'>
            <?php echo $HTML_players ?>
        </div>
    </div>

    <!-- Footer -->
    <script src='src/js/player.js'></script>
</body>
</html>