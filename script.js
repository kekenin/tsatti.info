window.onload = function() {  
  inactivityTime();
  scrolldown();
}

function scrolldown() {
document.getElementById('chatbox').scrollTop = document.getElementById('chatbox').scrollHeight;
}


function loadsessions() {
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4){
    	if (this.status == 404) {
        
        exit = true;
	    	if(exit===true){
		    window.location.href = 'index.php?logout=true';
		    }
      }
    }
  };
  xhttp.open("GET", "/sessions/session1.txt", true);
  xhttp.send();
}



function loadlog() {
  
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
        var vastaus = this.responseText;
        document.getElementById("chatbox").innerHTML = vastaus;
        scrolldown();
    }
  };
  xhttp.open("POST", "log.html", true);
  xhttp.send();
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
		if(exit===true){
		    
		    window.location = 'index.php?logout=true';
		    
		}		
}

var folio = document.getElementById("folio");
folio.onclick = function() {
    var folio = confirm("You are entering extra safe Folio-channel. Folio-messages can be seen only by other tsatti users in Folio-channel. To receive messages in this channel, use the receive button.");
		if(folio===true){
		    
		    window.location = 'http://www.tsatti.info/folio.php';
		    
		}		
}


var inactivityTime = function () {
    var t;
    // DOM Events
    window.onmousemove = resetTimer;
    window.onkeypress = resetTimer;

    function logout() {
        exit = true
	    	if(exit===true){
		    window.location.href = 'index.php?logout=true'
      }
    }
    
    function resetTimer() {
        clearTimeout(t);
        t = setTimeout(logout, 600000)
        // 1000 milisec = 1 sec
    }
};

setInterval (loadlog, 2500);
setInterval (loadusers, 2500);
//setInterval (loadsessions, 2500);

