<?php
session_start();
require_once('src/php/db.php');
require_once('src/php/functions.php');
$db = db_connect();

// POST
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if($_POST['action'] == "add" && !empty($_POST['name'])){
        $name = addslashes(trim($_POST['name']));
        $file_name = file_upload("audio", dirname(__FILE__)."/src/audio", "", false, guidv4());
        if($file_name)
            db_query_no_result($db, "INSERT INTO audio VALUES (NULL, '$name', '$file_name')");

        header('Location: library.php');
        exit();
    }

    if($_POST['action'] == "del" && !empty($_POST['reference'])){
        $reference = addslashes(trim($_POST['reference']));
        $file = db_query($db, "SELECT `file` FROM audio WHERE reference = '$reference'")['file'];
        shell_exec("rm src/audio/$file");
        db_query_no_result($db, "DELETE FROM audio WHERE reference = '$reference'");
        db_query_no_result($db, "UPDATE active SET audio = NULL WHERE audio = '$reference'");
        exit();
    }

    if($_POST['action'] == "edit" && !empty($_POST['reference'])){
      $reference = addslashes(trim($_POST['reference']));
      $name = addslashes(trim($_POST['name']));
      db_query_no_result($db, "UPDATE `audio` SET `name` = '$name' WHERE reference = '$reference'");
      header('Location: library.php');
      exit();
    }

    exit();
}


$HTML = "";
$result = db_query_raw($db, "SELECT * FROM audio ORDER BY audio.name");
while($row = mysqli_fetch_assoc($result)) {
    $reference = $row["reference"];
    $name = $row["name"];
    $file = $row["file"];

    $HTML .= "
    <tr>
        <td id='name_$reference'>$name</td>
        <td><audio id='player-$ref' controls preload=none><source src='src/audio/$file' type='audio/mpeg'></audio></td>
        <td class='center'><a href='src/audio/$file'><i class='glyphicon glyphicon-cloud-download'></i> Download</a></td>
        <td>
          <button onClick='edit_entry(\"$reference\")' class='btn btn-warning' type='button'><i class='glyphicon glyphicon-pencil'></i></button>
          <button type='button' class='btn btn-danger' onclick='del_entry(\"$reference\")'><i class='glyphicon glyphicon-remove'></i></button>
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
                    <th class="col-xs-3">Track</th>
                    <th>Preview</th>
                    <th></th>
                    <th class="col-xs-2">
                        <button onClick='import_audio()' class='btn btn-success' type='button'><i class='glyphicon glyphicon-cloud-upload'></i></button>
                    </th>
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
            document.getElementById("library").className="active"; 
        });

        function del_entry(ref){
            value = document.getElementById("name_" + ref).innerText;
            Swal.fire({
            title: "Delete '" + value + "' ?",
            type: 'question',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes',
            cancelButtonText: 'No',
            focusCancel: true
            }).then((result) => {
            if (result.value) {
                $.post("library.php", { action : "del", reference: ref }, function(data){
                    document.location.reload();
                }); 
            }
            })
        }

        function edit_entry(ref){
            value = document.getElementById("name_" + ref).innerText;
            Swal.fire({
                title: 'Edit name',
                type: 'info',
                html: "<form id='Swal-form' method='post'><input type='hidden' name='action' value='edit'><input type='hidden' name='reference' value='" + ref + "'><input class='form-control' type='text' name='name' value=\"" + value + "\"></form>",
                showCancelButton: true,
                focusConfirm: false,
                allowOutsideClick: false,
                width: "30%",
                confirmButtonText: 'Edit',
                cancelButtonText: 'Cancel'
            }).then((result) =>{
                if(result.value)
                    document.getElementById('Swal-form').submit();
            })
        }

        function import_audio(){
            Swal.fire({
                title: "Import audio file",
                html:   "<form id='swal-form' method='post' enctype='multipart/form-data'>"+
                        "<input type='hidden' name='action' value='add'>"+
                        "<td><input type='text' class='form-control' name='name' placeholder='Track name' required></td><br/>"+
                        "<td colspan=2><input type='file' class='form-control' name='audio' accept='.mp3' required></td>"+
                        "</form>",
                showCancelButton: true,
                showConfirmButton: confirm,
                focusConfirm: false,
                allowOutsideClick: false,
                width: "25%",
                confirmButtonText: 'Import',
                cancelButtonText: 'Cancel'
            }).then((result) =>{
                if(result.value)
                    document.getElementById('swal-form').submit();
            });
        }

    </script>
</body>
</html>