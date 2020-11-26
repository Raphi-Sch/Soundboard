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

    if($_POST['action'] == "edit" && !empty($_POST['reference'])){
      $reference = addslashes(trim($_POST['reference']));
      $audio = addslashes(trim($_POST['audio']));
      db_query_no_result($db, "UPDATE `active` SET `audio` = '$audio' WHERE reference = '$reference'");
    }

    header('Location: /assign.php');
    exit();
}

// Options
$options = "";
$result = db_query_raw($db, "SELECT * FROM audio WHERE audio.reference NOT IN (SELECT active.audio FROM active) ORDER BY audio.name");
while($row = mysqli_fetch_assoc($result)) {
    $ref = $row['reference'];
    $name = $row['name'];
    $options .= "<option value='$ref'>$name</option>";
}

$HTML = "";
$result = db_query_raw($db, "SELECT active.reference, audio.name FROM active LEFT JOIN audio ON active.audio = audio.reference ORDER BY active.reference");
while($row = mysqli_fetch_assoc($result)) {
    $HTML .= "
    <tr>
        <td id='ref_".$row["reference"]."'>".$row["reference"]."</td>
        <td id='name_".$row["reference"]."'>".$row["name"]."</td>
        <td>
            <button onClick='edit_entry(\"".$row["reference"]."\")' class='btn btn-warning' type='button'><i class='glyphicon glyphicon-pencil'></i></button>
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
        <table class="table table-hover table-condensed">
            <thead>
                <tr>
                    <th class="col-xs-1">Player</th>
                    <th>Audio</th>
                    <th class="col-xs-2"></th>
                </tr>
                <tr>
                    <form method="post">
                        <input type='hidden' name='action' value='add'>
                        <td></td>
                        <td></td>
                        <td><button type="submit" class="btn btn-success" name="action" value="add"><i class="glyphicon glyphicon-plus"></i></button></td>
                    </form>
                </tr>
            </thead>
            <tbody>
                <?php echo $HTML; ?>
            </tbody>
        </table>
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

        function edit_entry(ref){
            value = document.getElementById("name_" + ref).innerText;
            Swal({
                title: 'Editing name of : "' + ref + '"',
                type: 'info',
                html:   "<form id='swal-form' method='post'><input type='hidden' name='action' value='edit'>"+
                        "<input type='hidden' name='reference' value='" + ref + "'>"+
                        "<select class='form-control' name='audio'><?php echo $options;?></select>"+
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