<?php
if (isset($_POST['command'])) {
	
	
	//XML
	
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
	
	
	//Functions
	
	function funcName() {
		global $_POST;
		return explode("(", str_replace(array("(", ")"), "(", $_POST['command']))[0];
	}
	
	function funcParams() {
		global $_POST;
		return explode(",", explode("(", str_replace(array("(", ")"), "(", $_POST['command']))[1]);
	}
	
	function printBoard() {
		global $height, $width;
		$return = "";
		for ($y = 0; $y < $height; $y++) {
			for ($x = 0; $x < $width; $x++) {
				$return .= "<span class=\"tile\" id=\"".$y."_".$x."\"><span class=\"text\">".(!!getTile($x, $y)?getUnit(getTile($x, $y)->getElementsByTagName("unit")->item(0)->getAttribute("type"))->getElementsByTagName("short")->item(0)->nodeValue:"")."</span></span>";
			}
			$return .= "<br>";
		}
		return $return;
	}
	
	
	//XML functions
	
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
					if (!!!random_int(0, 6)) {
						$unit->setAttribute("type", "dog");
					}
				}
			}
		}
		$xml->save("txml.xml");
	}

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
	
	if (funcName() == "upgrade") {
		global $dom;
		getTile(funcParams()[0], funcParams()[1])->getElementsByTagName("unit")->item(0)->setAttribute("type", getNextUnit(getTile(funcParams()[0], funcParams()[1])->getElementsByTagName("unit")->item(0)->getAttribute("type"))->getAttribute("name"));
		$dom->save("txml.xml");
		echo printBoard();
	} else if (funcName() == "new") {
		newXML();
		updateBoard();
		echo printBoard();
	} else if (funcName() == "update") {
		echo printBoard();
	} else {
		//echo printBoard()."<img src=\"_.png\" onload=\"console.error('PHP: Invalid command passed in php(): ".$_POST['command']."');alert('PHP: Invalid command passed in php(): ".$_POST['command']."');this.parentNode.removeChild(this);\" />";
		echo "Invalid command passed: php(\"".$_POST['command']."\")";
	}
} else { echo "ERR"; }
?>