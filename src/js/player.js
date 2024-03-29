var intervals = [];
var players_state = [];
var overlap_playing = false;

function play(id){
  // Playing
  players_state[id] = true;
  document.getElementById('player-' + id).currentTime = 0;
  document.getElementById('player-' + id).play();
  intervals[id] = setInterval(() => {
    play_progress(id)
  }, 100);
  document.getElementById('btn-play-' + id).blur();
}

function pause(id){
  if(players_state[id]){ 
    // Play -> pause
    players_state[id] = false;
    document.getElementById('player-' + id).pause();
    document.getElementById('btn-pause-' + id).blur();
    clearInterval(intervals[id]);
  }
  else{
    // Pause -> Play
    players_state[id] = true;
    document.getElementById('player-' + id).play();
    document.getElementById('btn-pause-' + id).blur();
    intervals[id] = setInterval(() => {
      play_progress(id)
    }, 100);
  }
}

function stop(id){
  players_state[id] = false;
  document.getElementById('player-' + id).pause();
  document.getElementById('player-' + id).currentTime = 0;
  document.getElementById('progress-bar-' + id).style.width = "0%";
  clearInterval(intervals[id]);
  document.getElementById('btn-stop-' + id).blur();
}

function play_progress(id){
  current = document.getElementById('player-' + id).currentTime;
  duration = document.getElementById('player-' + id).duration;
  document.getElementById('progress-bar-' + id).style.width = (current / duration) * 100 + "%";
  if(current == duration) stop(id);
}

function change_speed(id){
  current = document.getElementById('speed-range-' + id).value;
  document.getElementById('speed-text-' + id).innerText = "x" + current;
  document.getElementById('player-' + id).playbackRate = current;
}

function change_volume(id){
  current = document.getElementById('volume-range-' + id).value;
  document.getElementById('volume-text-' + id).innerText = parseInt(current * 100) + "%";
  document.getElementById('player-' + id).volume = current;
}

function loop(id){
  current = document.getElementById('loop-' + id).checked;
  document.getElementById("player-" + id).loop = current; 
}

function save_parameters(id){
  volume = document.getElementById('volume-range-' + id).value;
  speed = document.getElementById('speed-range-' + id).value;
  loop_enable = document.getElementById('loop-' + id).checked;

  $.post("index.php", { action : "save_parameters", reference: id, volume: volume, speed: speed, loop_enable: loop_enable }); 
}

function load_parameters(config){
  for (const [id, value] of Object.entries(config)) {
    document.getElementById('player-' + id).volume = parseFloat(value.volume);
    document.getElementById('player-' + id).playbackRate = parseFloat(value.speed);
    document.getElementById('player-' + id).loop = value.loop;
  };
}

function set_overlap_playing(state){
  overlap_playing = state;
}

function key_pressed(event){
  var shift = event.shiftKey;
  var key = event.which || event.keyCode;

  // Insert => Enable/disable overlap
  if(key == 45){
    overlap_playing = !overlap_playing;
    document.getElementById("global-overlap").checked = overlap_playing;
  }

  // Space pressed => All players stop
  if(key == 32){
    players_state.forEach(function (playing, index){stop(index);})
    return;
  }

  // Numpad => Change page
  if(key >= 96 && key <= 105){
    key = key - 96;
    document.getElementById("page-a-" + key).click();
    return;
  }
    
  // A -> Z => play sample
  // Shift is a modifier
  if(key >= 65 && key <= 90){
    player_id = shortkey[key];
    if(player_id){
      if(shift){
        pause(player_id)
      }
      else{
        if(!overlap_playing){
          players_state.forEach(function (playing, index){stop(index);})
        }
        play(player_id);
      }
    }
    return;
  }

  // Random sample ','
  if(key == 188){
    random_key = 65 + Math.floor(Math.random() * Math.floor(25));
    players_state.forEach(function (playing, index){stop(index);})
    play(shortkey[random_key]);
    return;
  }

}