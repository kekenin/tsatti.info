window.onload = function() {  
  inactivityTime();
  scrolldown();
}

function scrolldown() {
document.getElementById('chatbox').scrollTop = document.getElementById('chatbox').scrollHeight;
}




function loadusers() {
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
        var vastaus = this.responseText;
        document.getElementById("users").innerHTML = "Currently online:" +vastaus;
    }
  };
  xhttp.open("POST", "users.txt", true);
  xhttp.send();
}

var exit = document.getElementById("exit");
exit.onclick = function() {

    var exit = confirm("Are you sure you want to end the session?");
		if(exit==true){
		    
		    window.location = 'index.php?logout=true';
		    
		}		
}



var inactivityTime = function () {
    var t;
    // DOM Events
    window.onmousemove = resetTimer;
    window.onkeypress = resetTimer;

    function logout() {
        exit = true
	    	if(exit==true){
		    window.location.href = 'index.php?logout=true'
      }
    }
    
    function resetTimer() {
        clearTimeout(t);
        t = setTimeout(logout, 600000)
        // 1000 milisec = 1 sec
    }
};


setInterval (loadusers, 2500);


