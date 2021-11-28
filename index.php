<?php
require_once('src/php/db.php');
$db = db_connect();
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if($_POST['action'] == "save_parameters" && !empty($_POST['reference'])){
        $reference = addslashes(trim($_POST['reference']));
        $volume = addslashes(trim($_POST['volume']));
        $speed = addslashes(trim($_POST['speed']));
        $loop_enable = $_POST['loop_enable'] == 'true' ? 1 : 0;

        db_query_no_result($db, "UPDATE `active` SET `volume` = '$volume', `speed` = '$speed', `loop_enable` = '$loop_enable' WHERE reference = '$reference'");
    }
    exit();
}

// Page
if(isset($_GET["page"]))
    $page = addslashes(trim($_GET["page"]));
else
    $page = 0;

$data = db_query_raw($db, "SELECT active.*, audio.file, audio.name FROM active LEFT JOIN audio ON active.audio = audio.reference WHERE active.page = '$page' ORDER BY active.reference ");
$HTML_players = "";
$params_array = [];
while($row = mysqli_fetch_assoc($data)) {
    $ref = $row['reference'];
    $name = empty($row['audio']) ? "- - EMPTY - -" : $row['name'];
    $file = $row['file'];
    $volume = $row['volume'];
    $speed = $row['speed'];
    $loop_enable = $row['loop_enable'] ? true : false;
    $loop_enable_HTML = $row['loop_enable'] ? "checked" : "";
    $shortkey = $row['shortkey'] ? "&#".$row['shortkey'].";" : "";

    // Parameters JSON
    $params_array[$ref] = ['volume' => $volume, 'speed' => $speed, 'loop' => $loop_enable, 'shortkey' => $row['shortkey']];

    $HTML_players .= "
        <div class='card'>
            <div class='row'>
                <div class='col-md-12'>
                    <div class='sample-title'>$name<div class='pull-right'>$shortkey</div></div>
                    <div class='progress'>
                        <div id='progress-bar-$ref' class='progress-bar progress-bar-success' role='progressbar' style='width:0%'></div>
                    </div>
                    <button id='btn-play-$ref' class='btn btn-success btn-player' onclick='play($ref)'><i class='glyphicon glyphicon-fast-backward'></i> <i class='glyphicon glyphicon-play'></i></button>
                    <button id='btn-pause-$ref' class='btn btn-warning btn-player' onclick='pause($ref)'><i class='glyphicon glyphicon-play'></i> <i class='glyphicon glyphicon-pause'></i></button>
                    <button id='btn-stop-$ref' class='btn btn-danger btn-player' onclick='stop($ref)'><i class='glyphicon glyphicon-stop'></i></button>
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

            <div class='row'>
                <div class='col-md-1'>
                    <i class='glyphicon glyphicon-dashboard'></i>
                </div>
                <div class='col-md-8'>
                    <input id='speed-range-$ref' type='range' min=0.30 max=2 step=0.1 value='$speed' oninput='change_speed($ref)' onchange='save_parameters($ref)'>
                </div>
                <div class='col-md-2'>
                    <p id='speed-text-$ref'>x$speed</p>
                </div>
            </div>

            <div class='row'>
                <div class='col-md-1'>
                    <i class='glyphicon glyphicon-repeat'></i>
                </div>
                <div class='col-md-1'>
                    <input type='checkbox' id='loop-$ref' onclick='loop($ref)' onchange='save_parameters($ref)' $loop_enable_HTML>
                </div>
            </div>

            <audio id='player-$ref' preload=none>
                <source src='src/audio/$file' type='audio/mpeg'>
            </audio>
        </div>
    ";
}

$JSON_params = json_encode($params_array, JSON_FORCE_OBJECT);

$HTML_pages = "";
for($i = 0; $i <= 9; $i++){
    $HTML_pages .= "<li id='page-$i'><a id='page-a-$i' href='?page=$i'>$i</a></li>";
}

?>

<!DOCTYPE html>
<html lang="fr">
  <head>
    <?php include('src/html/header.html');?>
    <title>Soundboard - Player</title>
  </head>

  <body onkeyup="key_pressed(event)">
    <!-- Navbar -->
    <?php include('src/php/navbar.php');?>

    <!-- Main -->
    <div class="main col-md-12">
        <!-- Tabs -->
        <ul class="nav nav-tabs">
            <?php echo $HTML_pages; ?>
        </ul>

        <!-- Players -->
        <div class='wrapper'>
            <?php echo $HTML_players ?>
        </div>
    </div>

    <!-- Footer -->
    <?php include('src/html/footer.html');?>
    <script src='src/js/player.js'></script>
    <script>
        var params = JSON.parse('<?php echo $JSON_params; ?>');
        var shortkey = [];

        for (const [id, value] of Object.entries(params)) {
            shortkey[value.shortkey] = id;
        };

        $(document).ready(function() {
            document.getElementById("index").className="active";
            document.getElementById("page-<?php echo $page;?>").className="active";
            load_parameters(params);
        });
    </script>
</body>
</html>