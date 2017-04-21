<?php
function updateBoard() {
	global $dom;
	$dom = new DOMDocument();
	$dom->load("txml.xml");
}

updateBoard();
$units = new DOMDocument();
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

//misc/oneoff functions
function printBoard() {
	global $height, $width;
	for ($y = 0; $y < $height; $y++) {
		for ($x = 0; $x < $width; $x++) {
			echo "<span class=\"tile\" id=\"".$y."_".$x."\"><span class=\"text\">".(!!getTile($x, $y)?getUnit(getTile($x, $y)->getElementsByTagName("unit")->item(0)->getAttribute("type"))->getElementsByTagName("short")->item(0)->nodeValue:"")."</span></span>";
		}
		echo "<br>";
	}
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
			<?php printBoard(); ?>
		</div>
		<div id="footer"></div>
	</body>
</head>