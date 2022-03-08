var intervals = [];
var players_state = [];

function play(header_id){
    //current_sequence = data_sequence[header_id];
    current_sequence = [];
    for (const [id, data] of Object.entries(data_sequence[header_id])){
        current_sequence.push(data.reference);
    }

    current_sequence.reverse();

    play_sequence(current_sequence);
}

function play_sequence(sequence){
    console.log(sequence);

    id = sequence[sequence.length - 1];
    sequence.pop();

    document.getElementById('player-' + id).currentTime = 0;
    document.getElementById('player-' + id).play();
    intervals[id] = setInterval(() => {
      play_progress(id, sequence);
    }, 10);
}

function play_progress(id, sequence){
    current = document.getElementById('player-' + id).currentTime;
    duration = document.getElementById('player-' + id).duration;
    document.getElementById('progress-bar-' + id).style.width = (current / duration) * 100 + "%";

    if(current == duration){
        clear(id);
        if(sequence.length > 0){
            play_sequence(sequence);
        }
    }
}

function clear(id){
    players_state[id] = false;
    document.getElementById('player-' + id).pause();
    document.getElementById('player-' + id).currentTime = 0;
    document.getElementById('progress-bar-' + id).style.width = "0%";
    clearInterval(intervals[id]);
}