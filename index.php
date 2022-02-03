<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
       "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>

  <meta http-equiv="cache-control" content="no-cache" />
  <meta http-equiv="pragma" content="no-cache" />

</head>
<body>
	<h1>Guten Morgen</h1>
	<h3>Willkommen beim FS201 Brötchenservice</h3>
	<p>Die Regeln sind wie folgt:</p>
	<ul>
		<li>Bestellungen müssen bis 7 Uhr morgens eingetragen werden</li>
		<li>Jedes Brötchen kostet 40 Cent</li>
		<li>Wer drei Bestellungen nicht bezahlt hat, bekommt nix mehr</li>
		<li>Bezahlt werden kann bar oder per PayPal <a href="https://paypal.me/maeffert?locale.x=de_DE">(Hier klicken)</a></li>
		<li>Bitte fickt nicht die Seite. Ich mache keine Inputkontrolle oder ähnliches. Läuft auf Vertrauensbasis</li>
	</ul>

	<!--Bestellformular-->
	<form method="POST" onsubmit="confirm()"> 
  <fieldset>
    <p>
      Name:
		<input name="besteller" type="text">
    </p>
	      <p>
     	Anzahl
		<input name="anzahl" type="text" id="anzahl">
    </p>
	<p>
	<input type="passwort" name="pw" id="pw" value="Passwort"/>
    <p>
	</p>
      <input type="submit" name="best" id="best" value="Bestellung aufgeben"/>
    </p>
	  <?php

function IsNotEmpty($besteller, $anzahl){
	if($besteller != '' && $anzahl != ''){
		return true;
	}
	else{
		return false;
	}
}

function NewCustomer($besteller, $orders){
	$neuerbesteller = true;

	$singleorder = explode(';', $orders);

	foreach($singleorder as $i){

		if(strpos($i, $besteller)===0){
			return false;
		}
	}
	return true;
}

function GetCustomersOrderAmount($besteller, $orders){
	$singleorder = explode(';', $orders);

	foreach($singleorder as $i){

		if(strpos($i, $besteller)===0){
			$orderamount = explode(' ', $i);
			return $orderamount[1];
		}
	}
}

function CheckPW($pw){
	if($pw == 'maracuja'){
		return true;
	}
	else{
		return false;
	}
}

function SaveOrder()
{
	$besteller = $_POST['besteller'];
	$anzahl = $_POST['anzahl'];
	$pw = $_POST['pw'];
	$bestellung = $besteller.' '.$anzahl;
	$myfile = fopen("bestellungen.log", "r") or die("Noch keine Bestellung eingegangen");
	$orders = fread($myfile, filesize("bestellungen.log"));
	$log_file_name = 'bestellungen.log';
if(CheckPW($pw)){
	if(IsNotEmpty($besteller,$anzahl)){
		if(NewCustomer($besteller, $orders)){
			$bestellung = $bestellung . ';';

			file_put_contents($log_file_name, $bestellung, FILE_APPEND);
			echo '<h3>Deine Bestellung ist eingegangen. <a href="https://paypal.me/maeffert/' . $anzahl * 0.4 . '">BEZAHLE HIER '.$anzahl * 0.4.' EURO</a></h3>';
		}
		else{
			$neworder = str_replace($besteller.' '.GetCustomersOrderAmount($besteller, $orders), $besteller.' '.$anzahl, $orders);
			file_put_contents($log_file_name, $neworder);
			echo '<p style="color:blue">Deine Bestellung wurde geändert</p>';
		}
	}
	else{
		echo '<p style="color:red">Bei der Bestellung ist etwas schiefgegangen</p>';
	}
}
else{
echo '<p style="color:red">Falsches Passwort</p>';
}
}

if (array_key_exists('best', $_POST)) {
	SaveOrder();
}
?>
  </fieldset>
</form>
	<p>Bestellungen:</p>
	<br>
<p>
<ul>
 <?php
 function ShowOrder(){
$myfile = fopen("bestellungen.log", "r") or die("Noch keine Bestellung eingegangen");
$orders = fread($myfile, filesize("bestellungen.log"));
$splitorders = explode(';', $orders);
$i = 1;
foreach($splitorders as $singleorder){
	if($i < count($splitorders)){
	$splitsingleorder = explode(' ', $singleorder);
	echo '<li>'.$splitsingleorder[0].': '.$splitsingleorder[1].'  ('.$splitsingleorder[1]*0.4.'  Euro)</li>';
	$i++;
}
}

fclose($myfile);
 }
 ShowOrder();
?>
</ul>
<br>
<br>
<br>
<br>
<br>
<p>Changelog</p>
<ul>
<li>Doppelte Bestellungen sind nicht mehr möglich</li>
<li>Leere Bestellungen sind nicht mehr möglich</li>
<li>Bestellungen zeigen jetzt den zu zahlenden Betrag</li>
<li>Bestellungen können jetzt geändert werden (Einfach mit neuer Anzahl bestellen</li>
<li><a href="/sourcecode.txt">Sourcecode</a></li>
</ul>
</body>
</html>
