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
