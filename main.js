//Version id - using semantic versioning
var version = "0.0.1";

window.selectedTile = "";
function tileClick(e, mode) {
	if (selectedTile == "") {
		if (e.getElementsByClassName("stats").length) {
			var speed = parseInt(e.innerHTML.split(">")[11].split("<")[0].split(": ")[1]);
			for (var y = -speed; y <= speed; y++) {
				for (var x = -speed; x <= speed; x++) {
					if (!!x || !!y) {
						if (parseInt(e.id.split("_")[0]) + y >= 0 && parseInt(e.id.split("_")[0]) + y < height) {
							if (parseInt(e.id.split("_")[1]) + x >= 0 && parseInt(e.id.split("_")[1]) + x < width) {
								if (!document.getElementById((parseInt(e.id.split("_")[0]) + y) + "_" + (parseInt(e.id.split("_")[1]) + x)).getElementsByClassName("stats").length) {
									document.getElementById((parseInt(e.id.split("_")[0]) + y) + "_" + (parseInt(e.id.split("_")[1]) + x)).className += " move";
								} else {
									var range = mode ? 1 : parseInt(e.innerHTML.split(">")[12].split("<")[0].split(": ")[1]);
									if (Math.abs(y) <= range && Math.abs(x) <= range) {
										document.getElementById((parseInt(e.id.split("_")[0]) + y) + "_" + (parseInt(e.id.split("_")[1]) + x)).className += mode ? " att" : " bom";
									} else {
										document.getElementById((parseInt(e.id.split("_")[0]) + y) + "_" + (parseInt(e.id.split("_")[1]) + x)).className += " no";
									}
								}
							}
						}
					}
				}
			}
			e.className += mode ? " left" : " right";
			selectedTile = e.id.split("_")[1] + "," + e.id.split("_")[0] + "," + mode;
		}
	} else {
		var st = selectedTile.split(",");
		selectedTile = "";
		var range = parseInt(document.getElementById(st[1] + "_" + st[0]).innerHTML.split(">")[12].split("<")[0].split(": ")[1]);
		for (var y = -range; y <= range; y++)
			for (var x = -range; x <= range; x++)
				if (e.id.split("_")[0] + y >= 0 && e.id.split("_")[0] + y < height)
					if (e.id.split("_")[1] + x >= 0 && e.id.split("_")[1] + x < width)
						document.getElementById((st[1] + y) + "_" + (st[0] + x)).className = "tile";
		if (st[0] == e.id.split("_")[1] && st[1] == e.id.split("_")[0]) { return; }
		if (e.getElementsByClassName("stats").length)
			attackUnit(st[2] == 1 ? "a" : "b", st[0], st[1], e.id.split("_")[1], e.id.split("_")[0]);
		else
			moveUnit(st[0], st[1], e.id.split("_")[1], e.id.split("_")[0]);
	}
}

function php(command) {
	var ajax;
	if (window.XMLHttpRequest) {
    ajax = new XMLHttpRequest();
	} else if (window.ActiveXObject) {
			ajax = new ActiveXObject("Microsoft.XMLHTTP");
	}
	ajax.onreadystatechange = function () {
		try {
			if (ajax.readyState === XMLHttpRequest.DONE) {
				if (ajax.status === 200) {
					document.getElementById("content").innerHTML = ajax.responseText;
				} else {
					console.error("Problem loading AJAX response: " + ajax.status === 200 + " error received!");
				}
			}
		} catch (e) { console.error("Problem sending AJAX: " + e); }
	};
	ajax.open("POST", "response.php", true);
	ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	ajax.send("command="+command);
}

function newBoard() {
	php("new");
	php("update");
}

function updateBoard() {
	php("update");
}

function moveUnit(x1, y1, x2, y2) {
	if (x1 == x2 && y1 == y2) { return; }
	php("move(" + x1 + "," + y1 + "," + x2 + "," + y2 + ")");
}

function attackUnit(type, x1, y1, x2, y2) {
	type = type.toLowerCase() == "attack" || type.toLowerCase() == "att" || type.toLowerCase() == "a" ? "attack" : type.toLowerCase();
	type = type == "range" || type == "rng" || type == "r" ? "bombard" : type;
	type = type == "bombard" || type == "bomb" || type == "b" ? "bombard" : type;
	if (type != "attack" && type != "bombard") { console.error("attackUnit(): Invalid attack type '" + type + "'"); return false; }
	php(type + "(" + x1 + "," + y1 + "," + x2 + "," + y2 + ")");
}
