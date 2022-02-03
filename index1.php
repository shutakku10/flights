<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
       "http://www.w3.org/TR/html4/loose.dtd">
<html>

<head>
  <title>FS201 Brötchenservice</title>
  <style>
    div {
      text-align: center;
    }
  </style>

  <meta http-equiv="cache-control" content="no-cache" />
  <meta http-equiv="pragma" content="no-cache" />

  <title>Title</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/uikit.min.css" />
  <script src="js/uikit.min.js"></script>
  <script src="js/uikit-icons.min.js"></script>

</head>

<body>



      <!--______________Create Order______________-->
  <div uk-grid class="uk-child-width-1-2@s uk-grid-match">
    <div class="uk-width-1-2@m uk-align-center uk-card uk-card-default uk-card-body">
      <form method="POST">
        <fieldset class="uk-fieldset">
          <legend class="uk-legend">Bestellung</legend>

          <!--Besteller-->
          <div class="uk-margin">
            <div class="uk-inline">
              <span class="uk-form-icon uk-form-icon-flip" uk-icon="icon: user"></span>
              <input id="besteller" name="besteller" class="uk-input" type="text" placeholder="Name">
            </div>

            <!--Passwort-->
            <div class="uk-inline">
              <span class="uk-form-icon uk-form-icon-flip" uk-icon="icon: lock"></span>
              <input id="pw" name="pw" class="uk-input" type="password" placeholder="Passwort">
            </div>

            <!--Anzahl-->
            <div class="uk-inline">
              <span class="uk-form-icon uk-form-icon-flip" placeholder="Anzahl Brötchen" uk-icon="icon: file-edit"></span>
              <input id="anzahl" name="anzahl" class="uk-input" type="text" placeholder="Normale Brötchen">
            </div>


                      <div class="uk-inline uk-margin uk-grid-small uk-child-width-auto uk-grid">
                        <label>
                          <input class="uk-checkbox" type="checkbox" id="dauerauftrag" name="dauerauftrag" value="ja">Dauerauftrag</label>
                      </div>
          </div>


          <input type="submit" class="uk-button" name="best" id="best" value="Bestellung aufgeben" />
        </fieldset>
      </form>



      <?php
      require_once __DIR__ . '/loginUser.php';

      if (array_key_exists('best', $_POST)) {
          SaveOrder();
      }

      function SaveOrder()
      {
          $besteller = $_POST['besteller'];
          $anzahl = $_POST['anzahl'];
          $pw = $_POST['pw'];
          $dauerauftrag = $_POST['dauerauftrag'];

          if (IsNotEmpty($besteller, $anzahl)) {
              if (loginUser($besteller, $pw) == 'pass') {
                  PostToDB($besteller, $anzahl, $dauerauftrag);
                  GetBestellnummer();
              } elseif (loginUser($besteller, $pw) == 'wrongPassword') {
                  echo ' <p><span class="uk-text-warning">Falsches Passwort :(</span></p>';
              } else {
                  echo '  <p><span class="uk-text-warning">Du bist vermutlich nich registriert</span></p>';
              }
          } else {
              echo '  <p><span class="uk-text-warning">Biite keine Felder leer lassen</span></p>';
          }
      }

      function IsNotEmpty($i, $l)
      {
          if ($i != null && $l != null) {
              return true;
          } else {
              return false;
          }
      }

      function CheckPW($pw)
      {
          if ($pw == 'maracuja') {
              return true;
          } else {
              return false;
          }
      }

      function PostToDB($besteller, $anzahl, $dauerauftrag)
      {
          $host_name = 'db5005501906.hosting-data.io';
          $database = 'dbs4625575';
          $user_name = 'dbu1351667';
          $password = 'NR!QMr8kdYd@s5?d';

          if ($dauerauftrag == 'ja') {
              $dauerauftrag = 1;
          } else {
              $dauerauftrag = 0;
          }

          $link = new mysqli($host_name, $user_name, $password, $database);

          if ($link->connect_error) {
              die('<p>Verbindung zum MySQL Server fehlgeschlagen: ' . $link->connect_error . '</p>');
          } else {
              if ($dauerauftrag == 1) {
                  try {
                      $sql = "SELECT * FROM `Dauerauftrag` WHERE `Username` = '" . $besteller . "'";
                      $result = $link->query($sql);
                      if ($result->num_rows == 0) {
                          $sql = "INSERT INTO Dauerauftrag (DauerauftragID, Username, Menge, Datum) VALUES(null, '" . $besteller . "', '" . $anzahl . "', CURRENT_TIMESTAMP)";
                          $result = $link->query($sql);
                      } else {
                          print_r($result);
                          echo '<p><span class="uk-text-warning">Du hast noch einen Dauerauftrag offen</span></p>';
                          return;
                      }
                  } catch (\Exception $e) {
                      echo $e;
                  }
              }
          }
          try {
              $sql =
                  "INSERT INTO `Bestellungen` (`BestellNr`, `Username`, `Anzahl`, `Bestellstatus`, `BestellUhrzeit`, `Dauerauftrag`) VALUES (NULL, '" . $besteller . "', '" . $anzahl . "', '0', CURRENT_TIMESTAMP, '" . $dauerauftrag . "') ";
              $result = $link->query($sql);
              echo '<p><span class="uk-text-success">Deine Bestellung ist eingereicht</span></p>';
          } catch (\Exception $e) {
              echo $e;
          }

          $link->close();
          return;
      }

      function GetBestellnummer()
      {
          $host_name = 'db5005501906.hosting-data.io';
          $database = 'dbs4625575';
          $user_name = 'dbu1351667';
          $password = 'NR!QMr8kdYd@s5?d';
          $link = new mysqli($host_name, $user_name, $password, $database);

          if ($link->connect_error) {
              die('<p>Verbindung zum MySQL Server fehlgeschlagen: ' . $link->connect_error . '</p>');
          } else {
              try {
                  $sql = "SELECT * FROM Bestellungen ORDER BY BestellNr DESC LIMIT 1";
                  $result = $link->query($sql);

                  if (!$result) {
                      die('Could not get data: ');
                  } else {
                      $row = mysqli_fetch_array($result);
                      echo '<p><span class="uk-text">Deine Bestellnummer: ' . $row['BestellNr'] . '</span></p>';
                  }
              } catch (\Exception $e) {
                  echo $e;
              }
          }
          $link->close();
      }
      ?>
      <p><span class="uk-text">Wenn du einen Dauerauftrag bestellst, wird deine Bestellung an jedem Brötchentag ausgeführt. Du kannst Jederzeit extra Brötchen bestellen, kannst aber nur einen Dauertauftrag aktiv haben :)</span></p>
         </div>
    </div>












    <!--______________Delete Dauerauftrag______________-->
          <div class="uk-width-1-2@m uk-align-center uk-card uk-card-default uk-card-body">
            <form method="POST">
              <fieldset class="uk-fieldset">
                <legend class="uk-legend">Lösche Dauerauftrag</legend>

                <!--User-->
                <div class="uk-inline">
                  <span class="uk-form-icon uk-form-icon-flip" uk-icon="icon: lock"></span>
                  <input id="user" name="user" class="uk-input" type="text" placeholder="User">
                </div>

                  <!--Passwort-->
                  <div class="uk-inline">
                    <span class="uk-form-icon uk-form-icon-flip" uk-icon="icon: lock"></span>
                    <input id="pw" name="pw" class="uk-input" type="password" placeholder="Passwort">
                  </div>

                <input type="submit" class="uk-button" name="deleteDauerauftrag" id="deleteDauerauftrag" value="Dauerauftrag löschen" />
              </fieldset>
            </form>
            <?php if (array_key_exists('deleteDauerauftrag', $_POST)) {
                deleteDauerauftrag();
            } ?>
        </div>
<?php
require_once __DIR__ . '/loginUser.php';

function deleteDauerauftrag()
{
    $besteller = $_POST['user'];
    if (loginUser($besteller, $_POST['pw']) == 'pass') {
        $link = connectDB();
        $sql = "DELETE FROM Dauerauftrag WHERE USERNAME = '" . $besteller . "'";
        try {
            $result = $link->query($sql);
            echo '<p><span class="uk-text-success">Hat geklappt</span></p>';
        } catch (\Exception $e) {
            echo $e;
        }
    }
}
?>










    <!--______________See Orders______________-->
    <div class="uk-width-1-2@m uk-align-center uk-card uk-card-default uk-card-body">
      <form method="POST">
        <fieldset class="uk-fieldset">

          <legend class="uk-legend">Bestellung einsehen</legend>

          <!--Besteller-->
          <div class="uk-margin">
            <div class="uk-inline">
              <span class="uk-form-icon uk-form-icon-flip" uk-icon="icon: user"></span>
              <input id="besteller" name="besteller" class="uk-input" type="text" placeholder="Name">
            </div>

            <!--Passwort-->
            <div class="uk-inline">
              <span class="uk-form-icon uk-form-icon-flip" uk-icon="icon: lock"></span>
              <input id="pw" name="pw" class="uk-input" type="password" placeholder="Passwort">
            </div>
          </div>
          <input type="submit" class="uk-button" name="showbest" id="showbest" value="Bestellungen anzeigen" />
        </fieldset>
      </form>

      <?php
      if (array_key_exists('showbest', $_POST)) {
          ShowOrders();
      }

      function ShowOrders()
      {
          $besteller = $_POST['besteller'];
          $pw = $_POST['pw'];

          if (IsNotEmpty($besteller, $pw)) {
              if (loginUser($besteller, $pw) == 'pass') {
                  FetchOrdersFromDB($besteller);
              } else {
                  echo '  <p><span class="uk-text-warning">Falsches Passwort</span></p>';
              }
          } else {
              echo '  <p><span class="uk-text-warning">Bitte keine leeren Felder einreichen</span></p>';
          }
      }

      function FetchOrdersFromDB($besteller)
      {
          $host_name = 'db5005501906.hosting-data.io';
          $database = 'dbs4625575';
          $user_name = 'dbu1351667';
          $password = 'NR!QMr8kdYd@s5?d';
          $link = new mysqli($host_name, $user_name, $password, $database);

          if ($link->connect_error) {
              die('<p>Verbindung zum MySQL Server fehlgeschlagen: ' . $link->connect_error . '</p>');
          } else {
              try {
                  $sql = "SELECT * FROM `Bestellungen` WHERE `Username` = '" . $besteller . "'";
                  //$sql = "SELECT * FROM Bestellungen ORDER BY BestellNr DESC LIMIT 1";
                  $result = $link->query($sql);
                  if (!$result) {
                      die('Could not get data: ');
                  } else {
                      echo '
                      <p><span class="uk-text">Ignorier den Status, bin zu faul das zu ändern</span></p>
            <table class="uk-table uk-table-divider">
          <thead>
              <tr>
                  <th>Best.nr.</th>
                  <th>Menge</th>
                  <th>Datum</th>
                  <th>Status</th>
              </tr>
          </thead>
          <tbody>';

                      while ($row = $result->fetch_array()) {
                          echo '<tr>';
                          $date = explode(' ', $row['BestellUhrzeit']);
                          echo '<td>' . $row['BestellNr'] . '</td><td>' . $row['Anzahl'] . '</td><td>' . $date[0] . '</td><td>' . $row['Bestellstatus'] . '</td>';
                          echo '</tr>';
                      }
                      echo '</tbody></table>';
                  }
              } catch (\Exception $e) {
                  echo $e;
              }
          }
      }
      ?>











    <!--______________Register______________-->
    </div>
    <div class="uk-width-1-2@m uk-align-center uk-card uk-card-default uk-card-body">
      <form method="POST">
        <fieldset class="uk-fieldset">
          <legend class="uk-legend">Registrieren</legend>

          <!--Besteller-->
          <div class="uk-margin">
            <div class="uk-inline">
              <span class="uk-form-icon uk-form-icon-flip" uk-icon="icon: user"></span>
              <input id="besteller" name="besteller" class="uk-input" type="text" placeholder="Name">
            </div>
            <!--Passwort-->
            <div class="uk-inline">
              <span class="uk-form-icon uk-form-icon-flip" uk-icon="icon: lock"></span>
              <input id="pw" name="pw" class="uk-input" type="password" placeholder="Passwort">
            </div>
          </div>

          <input type="submit" class="uk-button" name="register" id="register" value="Registrieren" />
        </fieldset>
      </form>

   <?php
   require_once __DIR__ . '/registerUser.php';
   if (array_key_exists('register', $_POST)) {
       $besteller = $_POST['besteller'];
       $pw = $_POST['pw'];

       if (!isUserRegistered($besteller)) {
           if (registerNewUser($besteller, $pw)) {
               echo '<p><span class="uk-text-success">Du wurdest registriert :)</span></p>';
           } else {
               echo '<p><span class="uk-text-warning">Etwas ist schiefgelaufen :(</span></p>';
           }
       } else {
           echo '<p><span class="uk-text-warning">Du bist bereits registriert</span></p>';
       }
   }
   ?>
 </div>
</div>
</body>

</html>
