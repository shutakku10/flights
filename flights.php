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
    <?php
    echo print_r($_POST);
    echo "<br>";
        if (array_key_exists('location', $_POST)) {
                fetchFlight();
            }
        else{
            $myfile = fopen("flights.log", "r") or die("Noch keine Flüge eingegangen");
            $log_file_name = 'flights.log';
            $flightlist = fread($myfile, filesize("flights.log"));
            file_put_contents($log_file_name, $flightlist.'recieved stuff');
        }
        
        function fetchFlight(){
            $velocity = $_POST['velocity'];
            $origin = $_POST['origin'];
            $location = $_POST['location'];
            $myfile = fopen("flights.log", "r") or die("Noch keine Flüge eingegangen");
            $log_file_name = 'flights.log';
            $flightlist = fread($myfile, filesize("flights.log"));
            $postdata = $velocity." mph , Herkunft: ".$origin." Aktueller Ort: ".$location;
            file_put_contents($log_file_name, $flightlist.$postdata.print_r($_POST));

            echo $flightlist;
        }
    ?>
</body>

</html>
