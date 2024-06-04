<?php
require_once("koneksi.php");
 ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title></title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">

    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.2/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    <div class="container">
      <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-6">
          <form class="form-horizontal" method="post">
              <fieldset>

              <!-- Form Name -->
              <legend>Form Name</legend>

              <!-- Text input-->
              <div class="form-group">
                <label class="col-md-4 control-label" for="title">Title Voting</label>
                <div class="col-md-6">
                <input id="title" name="title" placeholder="" class="form-control input-md" required="" type="text">

                </div>
              </div>

              <!-- Text input-->
              <div class="form-group" id="dinamisinput1">
                <label class="col-md-4 control-label" for="namavote">Nama Vote ke 1</label>
                <div class="col-md-5">
                <input id="namavote" name="namavote[]" placeholder="" class="form-control input-md" type="text">
                </div>
              </div>

              <div class="form-group">
                <label class="col-md-4 control-label" for="namavote"></label>
                <div class="col-md-5">
                <button class="btn btn-primary btn-xs" id="btntambah"><span class="fa fa-plus"></span></button>
                </div>
              </div>



              <!-- Button -->
              <div class="form-group">
                <label class="col-md-4 control-label" for="kirim"></label>
                <div class="col-md-4">
                  <button id="kirim" name="kirim" class="btn btn-primary">Buat Voting</button>
                </div>
              </div>

              </fieldset>
              </form>
              <?php
              if (isset($_POST['kirim'])) {

                $query = $conn->prepare("INSERT INTO titlevoting(judul) VALUES(:jdl)");
                $query->bindParam(":jdl",$_POST['title']);
                $query->execute();

                $id = $conn->lastInsertId();
                $query = "INSERT INTO datavoting(nama,title_id) VALUES";
                $dinamis = "";
                $x = 0;
                foreach ($_POST['namavote'] as $value) {
                  if ($x<count($_POST['namavote'])-1) {
                    $dinamis .= "(?,?),";
                  }else {
                    $dinamis .= "(?,?)";
                  }
                  $x++;
                }

                $SQL = $conn->prepare($query . $dinamis);
                $i=1;
                foreach ($_POST['namavote'] as $value) {
                  $SQL->bindValue($i++,$value);
                  $SQL->bindValue($i++,$id);
                }
                $SQL->execute();
              }
               ?>

        </div>

        <div class="col-md-12">
          <hr />
          <?php
          $query = $conn->prepare('SELECT * FROM datavoting');
          $query->execute();
          $datavote = $query->fetchAll();

          $query = $conn->prepare('SELECT * FROM titlevoting ORDER BY id DESC LIMIT 3');
          $query->execute();
          $titlevote = $query->fetchAll();
          $hitung = 0;

          foreach ($titlevote as $title) {
            echo '<legend>'.$title['judul'].'</legend>';
            echo '<form class="form-horizontal" method="post">';
            foreach ($datavote as $vote) {
              if ($title['id']==$vote['title_id']) {
                $hitung = $hitung + $vote['jumlah'];
                echo'<div class="form-group">
                      <div class="col-md-12">
                          <div class="radio">
                            <label><input type="radio" name="namavote" value="'.$vote['id'].'">'.$vote['nama'].'</label>

                          </div>
                      </div>
                    </div>';
              }
            }
            echo '<div class="form-group">
                    <div class="col-md-12">
                      <button id="votebtn" name="votebtn" class="btn btn-primary">Vote</button>
                    </div>
                  </div>';
            echo'</form>';
            foreach ($datavote as $vote) {
              if ($title['id']==$vote['title_id']) {
                $dataall = round($vote['jumlah']*100/$hitung)."%";
                echo'<div class="progress">
                        <div class="progress-bar progress-bar-info" style="width: '.$dataall.'">'.$vote['nama'].'('.$dataall.')'.'</div>
                      </div>';
              }
            }
            echo '<br />';
            $hitung = 0;
          }

          if (isset($_POST['votebtn'])) {
            $idvote = $_POST['namavote'];
            $query = $conn->prepare("UPDATE datavoting SET jumlah=jumlah+1 WHERE id=:idnya");
            $query->bindParam(":idnya",$idvote);
            $query->execute();
			header('Location: http://localhost/voteweb/index.php');
		    exit;
          }
           ?>
        </div>
        <div class="col-md-3"></div>
      </div>
    </div>


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script src="data.js"></script>
  </body>
</html>
