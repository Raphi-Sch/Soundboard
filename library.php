<?php
session_start();
require_once('src/php/db.php');
require_once('src/php/file-upload.php');
$db = db_connect();

// POST
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if($_POST['action'] == "add" && !empty($_POST['name'])){
        $name = addslashes(trim($_POST['name']));
        $file_name = file_upload("audio", dirname(__FILE__)."/src/audio", "", false, md5(time()));
        if($file_name)
            db_query_no_result($db, "INSERT INTO audio VALUES (NULL, '$name', '$file_name')");
    }

    if($_POST['action'] == "del" && !empty($_POST['reference'])){
        $reference = addslashes(trim($_POST['reference']));
        $file = db_query($db, "SELECT `file` FROM audio WHERE reference = '$reference'")['file'];
        shell_exec("sudo rm src/audio/$file");
        db_query_no_result($db, "DELETE FROM audio WHERE reference = '$reference'");
        db_query_no_result($db, "UPDATE active SET audio = NULL WHERE audio = '$reference'");
    }

    if($_POST['action'] == "edit" && !empty($_POST['reference'])){
      $reference = addslashes(trim($_POST['reference']));
      $name = addslashes(trim($_POST['name']));
      db_query_no_result($db, "UPDATE `audio` SET `name` = '$name' WHERE reference = '$reference'");
    }

    header('Location: /library.php');
    exit();
}


$HTML = "";
$result = db_query_raw($db, "SELECT * FROM audio ORDER BY audio.name");
while($row = mysqli_fetch_assoc($result)) {
    $HTML .= "
    <tr>
        <td id='name_".$row["reference"]."'>".$row["name"]."</td>
        <td id='file_".$row["reference"]."'><a href='src/audio/".$row["file"]."'>".$row["file"]."</a></td>
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
                    <th class="col-xs-3">Name</th>
                    <th>File</th>
                    <th class="col-xs-2"></th>
                </tr>
                <tr>
                    <form method="post" enctype='multipart/form-data'>
                        <input type='hidden' name='action' value='add'>
                        <td><input type="text" class="form-control" name="name" required></td>
                        <td><input type="file" class="form-control" name="audio" accept=".mp3" required></td>
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
                title: 'Editing name of : "' + ref + '"',
                type: 'info',
                html: "<form id='Swal.fire-form' method='post'><input type='hidden' name='action' value='edit'><input type='hidden' name='reference' value='" + ref + "'><input class='form-control' type='text' name='name' value=\"" + value + "\"></form>",
                showCancelButton: true,
                focusConfirm: false,
                allowOutsideClick: false,
                width: "30%",
                confirmButtonText: 'Edit',
                cancelButtonText: 'Cancel'
            }).then((result) =>{
                if(result.value)
                    document.getElementById('Swal.fire-form').submit();
            })
        }
    </script>
</body>
</html>