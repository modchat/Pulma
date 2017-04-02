<?php
//Write data
$width = 14;
$height = 6;
$xml = new DOMDocument();
$xml->formatOutput = true;
$board = $xml->appendChild($xml->createElement("board"));
$board->setAttribute("width", $width);
$board->setAttribute("height", $height);
for ($h = 0; $h < $height; $h++)
	for ($w = 0; $w < $width; $w++)
		$tile = $board->appendChild($xml->createElement("tile", !!!random_int(0, 14)?"CAT":".."));
$xml->save("txml.xml");

//Read data
$dom = simplexml_load_file('txml.xml');
//xpath('/board/tile')
//xpath('//tile[contains(unit, "Cat")]/@hp')
$height = $dom->xpath('/board/@height')[0];
$width = $dom->xpath('/board/@width')[0];
?>
<!DOCTYPE html>
<head>
	<head>
		<title>Pulma</title>
		<script src="main.js"></script>
		<link rel="stylesheet" type="text/css" href="main.css">
	</head>
	<body>
		<div id="header"></div>
		<div id="content">
			<?php
			for ($h = 0; $h < $height; $h++) {
				for ($w = 0; $w < $width; $w++) {
					echo "<span class=\"tile\" id=\"".$h."_".$w."\"><span class=\"text\">".trim($dom->xpath('/board/tile')[($h * $width) + $w])."</span></span>";
				}
				echo "<br>";
			}
			?>
		</div>
		<div id="footer"></div>
	</body>
</head>