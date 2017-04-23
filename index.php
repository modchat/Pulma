<?php
function updateBoard() {
	global $dom;
	$dom = new DOMDocument();
	$dom->formatOutput = true;
	$dom->preserveWhiteSpace = false;
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

function getUnitStat($t, $s) {
	global $units;
	$xpath = new DomXpath($units);
	return $xpath->query('//unit[@name="'.$t.'"][1]')->item(0)->getElementsByTagName($s)->item(0)->nodeValue;
}

//misc/oneoff functions
function printBoard() {
	global $board, $height, $width;
	$return = "";
	for ($y = 0; $y < $height; $y++) {
		for ($x = 0; $x < $width; $x++) {
			$return .= "<span class=\"tile\" id=\"".$y."_".$x."\" onclick=\"tileClick(this, 1);\" oncontextmenu=\"tileClick(this, 0);return false;\" ondblclick=\"php('promote(".$x.",".$y.")')\"><span class=\"text\">";
			if ($board->getElementsByTagName("tile")->item(($y * $width) + $x)->getElementsByTagName("unit")->length != 0) {
				$unit = getTile($x, $y)->getElementsByTagName("unit")->item(0);
				$return .= getUnitStat($unit->getAttribute("type"), "short");
				$return .= "<br>";
				$return .= "<span class=\"stats\">";
				$return .= "HP: ".($unit->hasAttribute("hp") ? $unit->getAttribute("hp") : getUnitStat($unit->getAttribute("type"), "health")).($unit->hasAttribute("hpInc") ? " (+".$unit->getAttribute("hpInc").")" : "")." / ".getUnitStat($unit->getAttribute("type"), "health")."<br><br>";
				$return .= "Strength: ".getUnitStat($unit->getAttribute("type"), "strength").($unit->hasAttribute("att") ? " + ".$unit->getAttribute("att") : "")."<br>";
				$return .= "Defence: ".getUnitStat($unit->getAttribute("type"), "defence").($unit->hasAttribute("def") ? " + ".$unit->getAttribute("def") : "")."<br><br>";
				$return .= "Bombard: ".getUnitStat($unit->getAttribute("type"), "bombard").($unit->hasAttribute("bom") ? " + ".$unit->getAttribute("bom") : "")."<br>";
				$return .= "Resistance: ".getUnitStat($unit->getAttribute("type"), "resistance").($unit->hasAttribute("res") ? " + ".$unit->getAttribute("res") : "")."<br><br>";
				$return .= "Speed: ".getUnitStat($unit->getAttribute("type"), "speed").($unit->hasAttribute("spd") ? " + ".$unit->getAttribute("spd") : "")."<br>";
				$return .= "Range: ".getUnitStat($unit->getAttribute("type"), "range").($unit->hasAttribute("dst") ? " + ".$unit->getAttribute("dst") : "")."<br><br>";
				$return .= "XP: ".($unit->hasAttribute("xp") ? $unit->getAttribute("xp") : 0)."/".getUnitStat($unit->getAttribute("type"), "levelXP");
				$return .= "</span>";
				$return .= (($unit->hasAttribute("hp") ? $unit->getAttribute("hp") : getUnitStat($unit->getAttribute("type"), "health")) + ($unit->hasAttribute("hpInc") ? $unit->getAttribute("hpInc") : 0))." / ".getUnitStat($unit->getAttribute("type"), "health");
			}
			$return .= "</span></span>";
		}
		$return .= "<br>";
	}
	echo $return;
}
?>
<!DOCTYPE html>
<head>
	<head>
		<title>Pulma</title>
		<script type="text/javascript">window.height = <?php echo $height; ?>; window.width = <?php echo $width; ?>;</script>
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