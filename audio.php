<link href="src/bootstrap-3.3.7-dist/css/bootstrap.css" rel="stylesheet">
<script src="src/sweetalert2-7.28.4/package/dist/sweetalert2.all.min.js"></script>

<audio id='player-1' controls>
  <source src="src/audio/sample.mp3" type="audio/mpeg">
</audio>
<br/>
<br/>
<br/>
<br/>

<div class="col-sm-2">
  <div class="progress">
    <div id="progress-bar-1" class="progress-bar progress-bar-success" role="progressbar" style="width:0%"></div>
  </div> 

  <br/>
  <button id='btn-play-1' class='btn btn-success' onclick='play_pause(1)'><i id='ico-play-1' class='glyphicon glyphicon-play'></i></button>
  <button id='btn-load-1' class='btn btn-danger' onclick='load(1)'><i id='ico-load-1' class='glyphicon glyphicon-stop'></i></button>
</div>


<script src='src/js/player.js'></script>