//Version id - using semantic versioning
var version = "0.0.1";

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
	php("move(" + x1 + "," + y1 + "," + x2 + "," + y2 + ")");
}

function attackUnit(type, x1, y1, x2, y2) {
	type = type.toLowerCase() == "attack" || type.toLowerCase() == "att" || type.toLowerCase() == "a" ? "attack" : type;
	type = type.toLowerCase() == "range" || type.toLowerCase() == "rng" || type.toLowerCase() == "r" ? "bombard" : type;
	type = type.toLowerCase() == "bombard" || type.toLowerCase() == "bomb" || type.toLowerCase() == "b" ? "bombard" : type;
	if (type != "attack" && type != "bombard") { console.error("attackUnit(): Invalid attack type '" + type + "'"); return false; }
	php(type + "(" + x1 + "," + y1 + "," + x2 + "," + y2 + ")");
}
