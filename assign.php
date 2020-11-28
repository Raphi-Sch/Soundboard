<?php
require_once('src/php/db.php');
$db = db_connect();

// POST
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if($_POST['action'] == "add"){
        db_query_no_result($db, "INSERT INTO `active` (`reference`, `volume`, `speed`, `audio`) VALUES (NULL, '1', '1', NULL);");
    }

    if($_POST['action'] == "del" && !empty($_POST['reference'])){
        $reference = addslashes(trim($_POST['reference']));
        db_query_no_result($db, "DELETE FROM active WHERE reference = '$reference'");
    }

    if($_POST['action'] == "edit-audio" && !empty($_POST['reference'])){
      $reference = addslashes(trim($_POST['reference']));
      $audio = addslashes(trim($_POST['audio']));
      db_query_no_result($db, "UPDATE `active` SET `audio` = '$audio' WHERE reference = '$reference'");
    }

    if($_POST['action'] == "edit" && !empty($_POST['reference'])){
        $reference = addslashes(trim($_POST['reference']));
        $shortkey = mb_ord(strtoupper(addslashes(trim($_POST['shortkey']))), "utf8");
        if($shortkey)
            $shortkey = "'" . $shortkey . "'";
        else
            $shortkey = "NULL";

        $page = addslashes(trim($_POST['page']));
        db_query_no_result($db, "UPDATE `active` SET `shortkey` = $shortkey, `page` = '$page' WHERE reference = '$reference'");
      }

    header('Location: /assign.php');
    exit();
}

// Options
$options = "";
$result = db_query_raw($db, "SELECT * FROM audio WHERE audio.reference NOT IN (SELECT active.audio FROM active WHERE audio IS NOT NULL) ORDER BY audio.name");
while($row = mysqli_fetch_assoc($result)) {
    $ref = $row['reference'];
    $name = $row['name'];
    $options .= "<option value='$ref'>$name</option>";
}

$HTML = "";
$result = db_query_raw($db, "SELECT active.*, audio.name FROM active LEFT JOIN audio ON active.audio = audio.reference ORDER BY active.page, active.reference");
while($row = mysqli_fetch_assoc($result)) {
    $shortkey = $row['shortkey'] ? "&#".$row['shortkey'].";" : "";
    $HTML .= "
    <tr>
        <td id='ref_".$row["reference"]."'>".$row["reference"]."</td>
        <td id='shortkey_".$row["reference"]."'>".$shortkey."</td>
        <td id='page_".$row["reference"]."'>".$row["page"]."</td>
        <td id='name_".$row["reference"]."'>".$row["name"]."</td>
        <td>
            <button onClick='edit_audio(\"".$row["reference"]."\")' class='btn btn-warning' type='button'><i class='glyphicon glyphicon-volume-up'></i></button>
            <button onClick='edit(\"".$row["reference"]."\")' class='btn btn-warning' type='button'><i class='glyphicon glyphicon-pencil'></i></button>
            <button type='button' class='btn btn-danger' onclick='del_entry(\"".$row['reference']."\")'><i class='glyphicon glyphicon-remove'></i></button>
        </td>
    </tr>";
}
?>

<!DOCTYPE html>
<html lang="fr">
  <head>
    <?php include('src/html/header.html');?>
    <title>Soundboard - Library</title>
  </head>

  <body>
    <!-- Navbar -->
    <?php include('src/php/navbar.php');?>

    <!-- Main area -->
    <div class="main col-md-10 col-md-offset-1">
        <form method="post">
            <input type='hidden' name='action' value='add'>
            <table class="table table-hover table-condensed">
                <thead>
                    <tr>
                        <th class="col-xs-1">Player</th>
                        <th class="col-xs-1">Shortkey</th>
                        <th class="col-xs-1">Page</th>
                        <th>Audio</th>
                        <th class="col-xs-2"><button type="submit" class="btn btn-success" name="action" value="add"><i class="glyphicon glyphicon-plus"></i></button></th>
                    </tr>
                </thead>
                <tbody>
                    <?php echo $HTML; ?>
                </tbody>
            </table>
        </form>
    </div>

    <!-- Footer -->
    <?php include('src/html/footer.html');?>
    <?php include('src/php/alert.php');?>
    <script>
        $(document).ready(function() {
            document.getElementById("assign").className="active"; 
        });

        function del_entry(ref){
            value = document.getElementById("ref_" + ref).innerText;
            Swal({
                title: "Delete '" + value + "' ?",
                type: 'question',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Yes',
                cancelButtonText: 'No',
                focusCancel: true
            }).then((result) => {
                if (result.value) {
                    $.post("assign.php", { action : "del", reference: ref }, function(data){
                        document.location.reload();
                    }); 
                }
            })
        }

        function edit_audio(ref){
            value = document.getElementById("name_" + ref).innerText;
            Swal({
                title: 'Editing audio of : "' + ref + '"',
                type: 'info',
                html:   "<form id='swal-form' method='post'><input type='hidden' name='action' value='edit-audio'>"+
                        "<input type='hidden' name='reference' value='" + ref + "'>"+
                        "<select id='swal-select' class='form-control' name='audio'><?php echo $options;?></select>"+
                        "</form>",
                showCancelButton: true,
                focusConfirm: false,
                allowOutsideClick: false,
                width: "30%",
                confirmButtonText: 'Edit',
                cancelButtonText: 'Cancel'
            }).then((result) =>{
                if(result.value)
                    document.getElementById('swal-form').submit();
            })
        }

        function edit(ref){
            shortkey = document.getElementById("shortkey_" + ref).innerText;
            page = document.getElementById("page_" + ref).innerText;
            Swal({
                title: 'Editing shortkey of : "' + ref + '"',
                type: 'info',
                html:   "<form id='swal-form' method='post'><input type='hidden' name='action' value='edit'>"+
                        "<input type='hidden' name='reference' value='" + ref + "'>"+
                        "<input type='text' class='form-control' name='shortkey' maxlength='1' value='" + shortkey + "'>"+
                        "<input type='number' class='form-control' name='page' min=1 max=10 step=1 value='" + page + "'>"+
                        "</form>",
                showCancelButton: true,
                focusConfirm: false,
                allowOutsideClick: false,
                width: "30%",
                confirmButtonText: 'Edit',
                cancelButtonText: 'Cancel'
            }).then((result) =>{
                if(result.value)
                    document.getElementById('swal-form').submit();
            })
        }
    </script>
</body>
</html>