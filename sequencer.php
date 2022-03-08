<!DOCTYPE html>
<html lang="fr">
  <head>
    <?php include('src/html/header.html');?>
    <title>Soundboard - Sequencer</title>
  </head>

  <body onkeyup="key_pressed(event)">
    <!-- Navbar -->
    <?php include('src/php/navbar.php');?>

    <!-- Main -->
    <div class="main col-md-12">

    </div>

    <!-- Footer -->
    <?php include('src/html/footer.html');?>
    <script src='src/js/sequencer.js'></script>
    <script>
        $(document).ready(function() {
            document.getElementById("sequencer").className="active";
        });
    </script>
</body>
</html>