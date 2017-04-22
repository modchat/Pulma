<?php
if (isset($_POST['command'])) {
	
	
	//XML
	
	$XML_FILE = "txml.xml";
	
	function updateBoard() {
		global $dom, $XML_FILE;
		$dom = new DOMDocument();
		$dom->formatOutput = true;
		$dom->preserveWhiteSpace = false;
		$dom->load($XML_FILE);
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
		return explode(",", explode("(", str_replace(array("(", ")"), "(", str_replace(" ", "", $_POST['command'])))[1]);
	}
	
	function printBoard() {
		global $board, $height, $width;
		$return = "";
		for ($y = 0; $y < $height; $y++) {
			for ($x = 0; $x < $width; $x++) {
				$return .= "<span class=\"tile\" id=\"".$y."_".$x."\"><span class=\"text\">";
				$condition = $board->getElementsByTagName("tile")->item(($y * $width) + $x)->getElementsByTagName("unit")->length != 0;
				if ($condition) {
					$unit = getTile($x, $y)->getElementsByTagName("unit")->item(0);
					$return .= getUnitStat($unit->getAttribute("type"), "short");
				}
				$return .= "<br>";
				if ($condition)
					$return .= $unit->hasAttribute("hp") ? $unit->getAttribute("hp") : getUnitStat($unit->getAttribute("type"), "health");
				$return .= "</span></span>";
			}
			$return .= "<br>";
		}
		return $return;
	}
	
	
	//XML functions
	
	function newXML() {
		global $XML_FILE;
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
		$xml->save($XML_FILE);
	}

	function getTile($x, $y) {
		global $board, $width;
		return $board->getElementsByTagName("tile")->item(($y*$width)+$x);
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

	function getNextUnit($t) {
		global $units;
		preg_match_all("/\[([^\]]*)\]/", getUnit($t)->getNodePath(), $matches);
		return getUnit($t)->parentNode->getElementsByTagName("unit")->item(end($matches[1]));
	}
	
	//MISC
	function limit ($v) { return $v < 0 ? 0 : $v; }
	
	if (funcName() == "upgrade") {
		$unit = getTile(funcParams()[0], funcParams()[1])->getElementsByTagName("unit")->item(0);
		$type = $unit->getAttribute("type");
		$unit->setAttribute("type", getNextUnit($type)->getAttribute("name"));
		if ($unit->hasAttribute("hp"))
			$unit->setAttribute("hp", $unit->getAttribute("hp") + limit(getUnitStat($unit->getAttribute("type"), "health") - getUnitStat($type, "health")));
		$dom->save($XML_FILE);
		echo printBoard();
	} else if (funcName() == "new") {
		newXML();
		updateBoard();
		echo printBoard();
	} else if (funcName() == "update") {
		echo printBoard();
	} else if (funcName() == "move") {
		$tile1 = getTile(funcParams()[0], funcParams()[1]);
		$tile2 = getTile(funcParams()[2], funcParams()[3]);
		
		if ($tile1->getElementsByTagName("unit")->length == 0) {
			echo printBoard()."php(\"".$_POST["command"]."\"): No unit at [".funcParams()[0].", ".funcParams()[1]."]";
			exit();
		} else if ($tile2->getElementsByTagName("unit")->length != 0) {
			echo printBoard()."php(\"".$_POST["command"]."\"): Cannot move unit at [".funcParams()[0].", ".funcParams()[1]."] to [".funcParams()[2].", ".funcParams()[3]."] as there is a unit located there";
			exit();
		} else if (abs(funcParams()[0] - funcParams()[2]) > getUnitStat($tile1->getElementsByTagName("unit")->item(0)->getAttribute("type"), "speed") || abs(funcParams()[1] - funcParams()[3]) > getUnitStat($tile1->getElementsByTagName("unit")->item(0)->getAttribute("type"), "speed")) {
			echo printBoard()."php(\"".$_POST["command"]."\"): The tile at [".funcParams()[1].", ".funcParams()[2]."] is too far from the unit at [".funcParams()[0].", ".funcParams()[1]."] to move to";
			exit();
		} else if (funcParams()[0] == funcParams()[2] && funcParams()[1] == funcParams()[3]) {
			echo printBoard()."php(\"".$_POST["command"]."\"): [".funcParams()[0].", ".funcParams()[1]."] and [".funcParams()[2].", ".funcParams()[3]."] are the same!";
			exit();
		} 
		
		$unitNode = $tile1->getElementsByTagName("unit")->item(0);
		$tile2->appendChild($unitNode);
		$dom->save($XML_FILE);
		updateBoard();
		echo printBoard();
	} else if (funcName() == "attack") {
		$tile1 = getTile(funcParams()[0], funcParams()[1]);
		$tile2 = getTile(funcParams()[2], funcParams()[3]);
		
		if ($tile1->getElementsByTagName("unit")->length == 0) {
			echo printBoard()."php(\"".$_POST["command"]."\"): No unit at [".funcParams()[0].", ".funcParams()[1]."]";
			exit();
		} else if ($tile2->getElementsByTagName("unit")->length == 0) {
			echo printBoard()."php(\"".$_POST["command"]."\"): No unit at [".funcParams()[2].", ".funcParams()[3]."]";
			exit();
		} else if (abs(funcParams()[0] - funcParams()[2]) > 1 || abs(funcParams()[1] - funcParams()[3]) > 1) {
			echo printBoard()."php(\"".$_POST["command"]."\"): Unit at [".funcParams()[0].", ".funcParams()[1]."] is too far from the unit at unit at [".funcParams()[2].", ".funcParams()[3]."]";
			exit();
		} else if (funcParams()[0] == funcParams()[2] && funcParams()[1] == funcParams()[3]) {
			echo printBoard()."php(\"".$_POST["command"]."\"): [".funcParams()[0].", ".funcParams()[1]."] and [".funcParams()[2].", ".funcParams()[3]."] are the same!";
			exit();
		}
		
		$unit1 = $tile1->getElementsByTagName("unit")->item(0);
		$unit2 = $tile2->getElementsByTagName("unit")->item(0);
		
		$unit2->setAttribute("hp", ($unit2->hasAttribute("hp") ? $unit2->getAttribute("hp") : getUnitStat($unit2->getAttribute("type"), "health")) - limit(getUnitStat($unit1->getAttribute("type"), "strength") - getUnitStat($unit2->getAttribute("type"), "defence")));
		if ($unit2->getAttribute("hp") <= 0) {
			//DESTROYED!
			$tile2->removeChild($unit2);
		}
		
		$dom->save($XML_FILE);
		updateBoard();
		echo printBoard();
	} else if (funcName() == "bombard") {
		$tile1 = getTile(funcParams()[0], funcParams()[1]);
		$tile2 = getTile(funcParams()[2], funcParams()[3]);
		
		if ($tile1->getElementsByTagName("unit")->length == 0) {
			echo printBoard()."php(\"".$_POST["command"]."\"): No unit at [".funcParams()[0].", ".funcParams()[1]."]";
			exit();
		} else if ($tile2->getElementsByTagName("unit")->length == 0) {
			echo printBoard()."php(\"".$_POST["command"]."\"): No unit at [".funcParams()[2].", ".funcParams()[3]."]";
			exit();
		} else if (getUnitStat($tile1->getElementsByTagName("unit")->item(0)->getAttribute("type"), "range") == 0) {
			echo printBoard()."php(\"".$_POST["command"]."\"): The unit at [".funcParams()[0].", ".funcParams()[1]."] is incapable of bombarding!";
			exit();
		} else if (abs(funcParams()[0] - funcParams()[2]) > getUnitStat($tile1->getElementsByTagName("unit")->item(0)->getAttribute("type"), "range") || abs(funcParams()[1] - funcParams()[3]) > getUnitStat($tile1->getElementsByTagName("unit")->item(0)->getAttribute("type"), "range")) {
			echo printBoard()."php(\"".$_POST["command"]."\"): Unit at [".funcParams()[0].", ".funcParams()[1]."] is too far from the unit at unit at [".funcParams()[2].", ".funcParams()[3]."]";
			exit();
		} else if (funcParams()[0] == funcParams()[2] && funcParams()[1] == funcParams()[3]) {
			echo printBoard()."php(\"".$_POST["command"]."\"): [".funcParams()[0].", ".funcParams()[1]."] and [".funcParams()[2].", ".funcParams()[3]."] are the same!";
			exit();
		}
		
		$unit1 = $tile1->getElementsByTagName("unit")->item(0);
		$unit2 = $tile2->getElementsByTagName("unit")->item(0);
		
		$unit2->setAttribute("hp", ($unit2->hasAttribute("hp") ? $unit2->getAttribute("hp") : getUnitStat($unit2->getAttribute("type"), "health")) - limit(getUnitStat($unit1->getAttribute("type"), "bombard") - getUnitStat($unit2->getAttribute("type"), "resistance")));
		if ($unit2->getAttribute("hp") <= 0) {
			//DESTROYED!
			$tile2->removeChild($unit2);
		}
		
		$dom->save($XML_FILE);
		updateBoard();
		echo printBoard();
	} else {
		//echo printBoard()."<img src=\"_.png\" onload=\"console.error('PHP: Invalid command passed in php(): ".$_POST['command']."');alert('PHP: Invalid command passed in php(): ".$_POST['command']."');this.parentNode.removeChild(this);\" />";
		echo printBoard()."php(\"".$_POST['command']."\"): Invalid command passed";
	}
} else { echo "ERR"; }
?>