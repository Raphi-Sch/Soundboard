<?php

function db_connect(){
    $config_raw = file_get_contents("src/config.json");
    $config_JSON = json_decode($config_raw, true);

    $db = mysqli_connect($config_JSON["db_host"], $config_JSON["db_user"], $config_JSON["db_pass"], $config_JSON["db_name"]); 
    mysqli_set_charset ($db, "utf8");
    /* check connection */
    if (mysqli_connect_errno()) {
        echo "<h2>SQL Error : ".mysqli_connect_error()."</h2>";
        exit();
    }

    return $db;
}

function erreur_requete($db, $request){
    echo "SQL Error : ".mysqli_error($db);
    error_log("SQL : ".$request);
    exit(1);
}

function db_query($db, $request){
    $res = mysqli_query($db, $request);

    if(mysqli_error($db)){
        erreur_requete($db, $request);
    }

    return mysqli_fetch_assoc($res);
}

function db_query_no_result($db, $request){
    mysqli_query($db, $request);

    if(mysqli_error($db)){
        erreur_requete($db, $request);
    }
}

function db_query_raw($db, $request){
    $res = mysqli_query($db, $request);

    if(mysqli_error($db)){
        erreur_requete($db, $request);
    }

    return $res;
}