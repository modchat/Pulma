<?php
//Create a new xml board
function newXML() {
	$width = 14;
	$height = 6;
	$xml = new DOMDocument();
	$xml->formatOutput = true;
	$board = $xml->appendChild($xml->createElement("board"));
	$board->setAttribute("width", $width);
	$board->setAttribute("height", $height);
	for ($h = 0; $h < $height; $h++) {
		for ($w = 0; $w < $width; $w++) {
			$tile = $board->appendChild($xml->createElement("tile"));
			if (!!!random_int(0, 8)) {
				$unit = $tile->appendChild($xml->createElement("unit"));
				$unit->setAttribute("type", "cat");
				if (!!!random_int(0, 8)) {
					$unit->setAttribute("type", "dog");
				}
			}
		}
	}
	$xml->save("txml.xml");
}

$dom = new DOMDocument();
$units = new DOMDocument();
$dom->load("txml.xml");
$units->load("units.xml");
$board = $dom->getElementsByTagName("board")->item(0);
$width = $board->getAttribute("width");
$height = $board->getAttribute("height");

function getTile($x, $y) {
	global $board, $width;
	return $board->getElementsByTagName("tile")->item(($y*$width)+$x)->getElementsByTagName("unit")->length==0?false:$board->getElementsByTagName("tile")->item(($y*$width)+$x);
}

function getUnit($t) {
	global $units;
	$xpath = new DomXpath($units);
	return $xpath->query('//unit[@name="'.$t.'"][1]')->item(0);
}

function getNextUnit($t) {
	global $units;
	preg_match_all("/\[([^\]]*)\]/", getUnit($t)->getNodePath(), $matches);
	return getUnit($t)->parentNode->getElementsByTagName("unit")->item(end($matches[1]));
}

//misc/oneoff functions
function getShortFromTile($x, $y) {
	return !!getTile($x, $y)?getUnit(getTile($x, $y)->getElementsByTagName("unit")->item(0)->getAttribute("type"))->getElementsByTagName("short")->item(0)->nodeValue:"";
}
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
			for ($y = 0; $y < $height; $y++) {
				for ($x = 0; $x < $width; $x++) {
					echo "<span class=\"tile\" id=\"".$y."_".$x."\"><span class=\"text\">".getShortFromTile($x, $y)"</span></span>";
				}
				echo "<br>";
			}
			?>
		</div>
		<div id="footer"></div>
	</body>
</head>