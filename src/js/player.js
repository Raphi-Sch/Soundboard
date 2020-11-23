var intervals = [];
var play = [];

function play_pause(id){
  if(play[id]){
    // Pause
    play[id] = false;
    document.getElementById('player-' + id).pause();
    clearInterval(intervals[id]);
    document.getElementById('btn-play-' + id).className = "btn btn-success";
    document.getElementById('ico-play-' + id).className = "glyphicon glyphicon-play";
    
  }
  else{
    // Playing
    play[id] = true;
    document.getElementById('player-' + id).play();
    document.getElementById('btn-play-' + id).className = "btn btn-warning";
    document.getElementById('ico-play-' + id).className = "glyphicon glyphicon-pause";
    intervals[id] = setInterval(() => {
      play_progress(id)
    }, 100);
  }
}

function load(id){
  play[id] = false;
  document.getElementById('player-' + id).load();
  clearInterval(intervals[id]);
  document.getElementById('btn-play-' + id).className = "btn btn-success";
  document.getElementById('ico-play-' + id).className = "glyphicon glyphicon-play";
  document.getElementById('progress-bar-' + id).style.width = "0%";
}

function play_progress(id){
  current = document.getElementById('player-' + id).currentTime;
  duration = document.getElementById('player-' + id).duration;
  document.getElementById('progress-bar-' + id).style.width = (current / duration) * 100 + "%";
  if(current == duration) load(id);
}

function speed(id){
  current = document.getElementById('speed-range-' + id).value;
  document.getElementById('speed-text-' + id).innerText = current;
  document.getElementById('player-' + id).playbackRate = current;
}

function volume(id){
  current = document.getElementById('volume-range-' + id).value;
  document.getElementById('volume-text-' + id).innerText = parseInt(current * 100) + "%";
  document.getElementById('player-' + id).volume = current;
}