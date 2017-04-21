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
			if (!!!random_int(0, 14)) {
				$unit = $tile->appendChild($xml->createElement("unit"));
				$unit->setAttribute("type", "cat");
			}
		}
	}
	$xml->save("txml.xml");
}

//Read xml board
function readXML() {
	$dom = new DOMDocument();
	if ($dom->load("txml.xml")) {
		return $dom;
	}
	return false;
}

$dom = readXML();
$board = $dom->getElementsByTagName("board")->item(0);
$width = $board->getAttribute("width");
$height = $board->getAttribute("height");

class unit {
	public $name = 'Tom';
	
	private $type = 'blank';
	public $short = '..';
	private $health = 5;
	private $cHealth = 5;
	private $strength = 5;
	private $resistance = 2;
	
	private $x = 0;
	private $y = 0;
	
	function __construct($x, $y) {
		$this->x = $x;
		$this->y = $y;
		
		$this->name = 'Tom';//RANDOMIZE
	}
	
	function __destruct() {}
	function destroy() { $this->__destruct(); }
	
	function getType() { return $this->type; }
	function getHP() { return $this->cHealth; }
	function getMHP() { return $this->health; }
	function getAtt() { return $this->strength; }
	function getDef() { return $this->resistance; }
}

class cat extends unit {
	private $type = 'cat';
	public $short = 'CAT';
	private $health = 2;
	private $cHealth = 2;
	private $strength = 8;
	private $resistance = 1;
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
			for ($h = 0; $h < $height; $h++) {
				for ($w = 0; $w < $width; $w++) {
					echo "<span class=\"tile\" id=\"".$h."_".$w."\"><span class=\"text\">".($board->getElementsByTagName("tile")->item(($h*$width)+$w)->getElementsByTagName("unit")->length==0?"":$board->getElementsByTagName("tile")->item(($h*$width)+$w)->getElementsByTagName("unit")->item(0)->getAttribute("type"))."</span></span>";
				}
				echo "<br>";
			}
			?>
		</div>
		<div id="footer"></div>
	</body>
</head>