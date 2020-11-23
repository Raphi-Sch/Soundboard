<?php

?>

<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <link href="src/bootstrap-3.3.7-dist/css/bootstrap.css" rel="stylesheet">
    <script src="src/sweetalert2-7.28.4/package/dist/sweetalert2.all.min.js"></script>
    <link href="src/css/dashboard.css" rel="stylesheet">
    <title>Soundboard</title>
  </head>

  <body>
    <!-- Main area -->
    <div class="main col-md-10 col-md-offset-1">
        <div class='wrapper'>

            <div id='card-1' class='card'>
                <div class='sample-title'>Titre du sample</div>
                <div class="progress">
                    <div id="progress-bar-1" class="progress-bar progress-bar-success" role="progressbar" style="width:0%"></div>
                </div>
                <button id='btn-play-1' class='btn btn-success' onclick='play_pause(1)'><i id='ico-play-1' class='glyphicon glyphicon-play'></i></button>
                <button id='btn-load-1' class='btn btn-danger pull-right' onclick='load(1)'><i id='ico-load-1' class='glyphicon glyphicon-stop'></i></button>
                <audio id='player-1'>
                    <source src="src/audio/sample-1.mp3" type="audio/mpeg">
                </audio>
            </div>

            <div id='card-2' class='card'>
                <div class='sample-title'>Titre du sample</div>
                <div class="progress">
                    <div id="progress-bar-2" class="progress-bar progress-bar-success" role="progressbar" style="width:0%"></div>
                </div>
                <button id='btn-play-2' class='btn btn-success' onclick='play_pause(2)'><i id='ico-play-2' class='glyphicon glyphicon-play'></i></button>
                <button id='btn-load-2' class='btn btn-danger pull-right' onclick='load(2)'><i id='ico-load-2' class='glyphicon glyphicon-stop'></i></button>
                <audio id='player-2'>
                    <source src="src/audio/sample-2.mp3" type="audio/mpeg">
                </audio>
            </div>

        </div>
    </div>

    <!-- Footer -->
    <script src='src/js/player.js'></script>
</body>
</html>