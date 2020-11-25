<?php
require_once('src/php/db.php');
$db = db_connect();
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if($_POST['action'] == "save_parameters" && !empty($_POST['reference'])){
        $reference = addslashes(trim($_POST['reference']));
        $volume = addslashes(trim($_POST['volume']));
        $speed = addslashes(trim($_POST['speed']));

        db_query_no_result($db, "UPDATE `active` SET `volume` = '$volume', `speed` = '$speed' WHERE reference = '$reference'");
    }
    exit();
}


$data = db_query_raw($db, "SELECT active.*, audio.file, audio.name FROM active, audio WHERE active.audio = audio.reference ORDER BY active.reference ");
$HTML_players = "";
$params = [];
while($row = mysqli_fetch_assoc($data)) {
    $ref = $row['reference'];
    $name = $row['name'];
    $file = $row['file'];
    $volume = $row['volume'];
    $speed = $row['speed'];
    $params[$ref] = [$volume, $speed];

    $HTML_players .= "
        <div class='card'>
            <div class='row'>
                <div class='col-md-12'>
                    <div class='sample-title'>$name</div>
                    <div class='progress'>
                        <div id='progress-bar-$ref' class='progress-bar progress-bar-success' role='progressbar' style='width:0%'></div>
                    </div>
                    <button id='btn-play-$ref' class='btn btn-success btn-player' onclick='play_pause($ref)'><i id='ico-play-$ref' class='glyphicon glyphicon-play'></i></button>
                    <button class='btn btn-danger btn-player pull-right' onclick='stop($ref)'><i class='glyphicon glyphicon-stop'></i></button>
                </div>
            </div>

            <br/>
            <div class='row'>
                <div class='col-md-1'>
                    <i class='glyphicon glyphicon-volume-up'></i>
                </div>
                <div class='col-md-8'>
                    <input id='volume-range-$ref' type='range' min=0 max=1 step=0.01 value=$volume oninput='change_volume($ref)' onchange='save_parameters($ref)'>
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
                    <input id='speed-range-$ref' type='range' min=0.25 max=2 step=0.25 value='$speed' oninput='change_speed($ref)' onchange='save_parameters($ref)'>
                </div>
                <div class='col-md-2'>
                    <p id='speed-text-$ref'>x$speed</p>
                </div>
            </div>

            <audio id='player-$ref' preload=none>
                <source src='src/audio/$file' type='audio/mpeg'>
            </audio>
        </div>
    ";
}

$JSON_params = json_encode($params);

?>

<!DOCTYPE html>
<html lang="fr">
  <head>
    <?php include('src/html/header.html');?>
    <title>Soundboard - Player</title>
  </head>

  <body>
    <!-- Navbar -->
    <?php include('src/php/navbar.php');?>

    <!-- Main area -->
    <div class="main col-md-12">
        <div class='wrapper'>
            <?php echo $HTML_players ?>
        </div>
    </div>

    <!-- Footer -->
    <?php include('src/html/footer.html');?>
    <script src='src/js/player.js'></script>
    <script>
        var params = JSON.parse('<?php echo $JSON_params; ?>');
        $(document).ready(function() {
            document.getElementById("index").className="active"; 
            load_parameters(params);
        });
    </script>
</body>
</html>