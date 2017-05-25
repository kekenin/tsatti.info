<?php session_start(); ?>
<?php
/*if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
*/

date_default_timezone_set('Europe/Helsinki');
//LOGINFORM FUNCTION, GOES HERE IF SESSION['name'] IS NOT SET...
function loginForm(){
    
    echo'
    
    <div id="loginform">
    <form action="http://www.tsatti.info/folio.php" method="post">
        <p>Please enter your name to continue:</p>
        <label for="name">Name:</label>
        <input type="text" name="name" id="name" maxlength="10" autofocus/>
        <input type="submit" name="enter" id="enter" value="Enter" />
    </form>
    </div>
    ';
    
    
    
}


//EXPIRED FUNCTION, THIS WILL CHECK IF SESSION HAS EXPIRED... WRITES AFK MESSAGE TO LOG, REMOVES USER FROM USERS. DESTROYS SESSION, REDIRECTS USER. WILL BE USED LATER
function expired() {
    if( $_SESSION['last_activity'] < time()-$_SESSION['expire_time'] ) {


    $contents = file_get_contents("users.txt");
    $contents = str_replace($_SESSION['name'], '', $contents);
    file_put_contents("users.txt", $contents);

    session_destroy();
    header("Location: http://www.tsatti.info/folio.php");
    exit();
} else{
    $_SESSION['last_activity'] = time();
}    
    
}
 
//ASKS USERNAME, STORES SESSION['name'] INTO FILE, SETS EXPIRE TIME FOR SESSION
if(isset($_POST['enter'])){
    
    if($_POST['name'] != ""){
    	session_save_path('/home/tsatti/sessions');
    	session_start();
        $_SESSION['name'] = stripslashes(htmlspecialchars($_POST['name']));
        $fp = fopen("users.txt", 'a');
        $name = $_SESSION['name'] . " ";
        fwrite($fp, $name);
        fclose($fp);
        
            //ENTER MESSAGE
        $fp = fopen("log.html", 'a');
        fwrite($fp, "<div style='color:rgb(255,87,255)' class='msgln'>". $_SESSION['name'] ." has entered the tsatti.<br></div>");
        fclose($fp);
        
        $_SESSION['logged_in'] = true; //set you've logged in
        $_SESSION['last_activity'] = time(); //your last activity was now, having logged in.
        $_SESSION['expire_time'] = 595; //expire time in seconds
        
        //CREATES MIRROR IMAGES OF /TMP SESSION FILES TO TSATTIS SESSION'S FOLDER FOR THE JAVASCRIPT CHECKING
        $i = 1;
        $files = glob('/tmp/sess*'); // get all file names
	foreach($files as $file){ // iterate files
 	if(is_file($file)){
    		copy($file, "/home/tsatti/public_html/sessions/session$i.txt");
    		$i++;
    		}
	}
        
        
    }
    else{
        echo '<span class="error">Please type in a name</span>';
    }
}


//GETS USERMSG SAFELY FROM FORM, STORES DATE AND MSG INTO FILE, REDIRECTS BACK TO FOLIO.PHP
if(isset($_POST['usermsg'])){
    expired();
    $date = date('G:i:s', time());
    $msg = stripslashes(htmlspecialchars($_POST["usermsg"], ENT_QUOTES, 'ISO-8859-1', false));
    utf8_encode($msg);
    $fp = fopen("/tmp/folio.html", 'a');
    fwrite($fp, "<div style='color:rgb(128,128,128)' class='msgln'>".$date." " .$_SESSION['name'] .": <i>$msg</i><br></div>");
    fclose($fp);
header("Location: folio.php");
}




//WRITES LOGOFF MESSAGE INTO FILE, REMOVES CURRENT USERNAME FROM USERS.TXT, DESTROYS SESSION, REDIRECTS BACK TO INDEX.PHP
if(isset($_GET['logout'])){

    expired();
    
    $fp = fopen("log.html", 'a');
    fwrite($fp, "<div style='color:rgb(255,255,87)' class='msgln'>". $_SESSION['name'] ." has left the tsatti.<br></div>");
    fclose($fp);
    

    $contents = file_get_contents("users.txt");
    $contents = str_replace($_SESSION['name'], '', $contents);
    file_put_contents("users.txt", $contents);

    session_destroy();
    header("Location: http://www.tsatti.info");
}

// CLEARS LOGS
if(isset($_POST['clear'])){
    expired();
    $path = "log.html";
    $path2 = "/tmp/folio.html";
    if (file_exists($path)) {
    $fp = fopen($path, 'w');
    fclose($fp);
    }
    if (file_exists($path2)) {
    $fp = fopen($path2, 'w');
    fclose($fp);
    }

    header("Location: http://www.tsatti.info/folio.php");
}

// KILL ALL SESSIONS
if(isset($_POST['destroy'])){

    $fp = fopen("log.html", 'a');
    fwrite($fp, "<div style='color:rgb(255,255,87)' class='msgln'>"."All sessions killed.<br></div>");
    fclose($fp);
    
    $fp = fopen("/tmp/folio.html", 'a');
    fwrite($fp, "<div style='color:rgb(255,255,87)' class='msgln'>"."All sessions killed.<br></div>");
    fclose($fp);
    
//CLEAR USER.TXT FILE
$path = "users.txt";
    if (file_exists($path)) {
    $fp = fopen($path, 'w');
    fclose($fp);
	}
//DESTROY ORIGINAL SESSION FILES FROM /TMP FOLDER
$files = glob('/tmp/sess*'); // get all file names
foreach($files as $file){ // iterate files
  if(is_file($file))
    unlink($file); // delete file
}

//ALSO DESTROY CREATED MIRROR SESSION FILES FROM /HOME/TSATTI/PUBLIC_HTML/USERS FOLDER FOR JAVASCRIPT...
$files = glob('/home/tsatti/public_html/sessions/*'); // get all file names
foreach($files as $file){ // iterate files
  if(is_file($file))
    unlink($file); // delete file
}

    header("Location: http://www.tsatti.info/");
}

//FOLIO-MODE************************************************************************************************************************************
if(isset($_POST['folio'])){
    expired();
    var_dump("FDSAFDAS");


    header("Location: http://www.tsatti.info/index.php");
    var_dump("fu");
}


?>

<!DOCTYPE html>

<head>
<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
<meta content="utf-8" http-equiv="encoding">
<title>tsatti</title>
<link type="text/css" rel="stylesheet" href="style.css" />

</head>


<?php
if(!isset($_SESSION['name'])){
    loginForm();
}
else{

    expired();   

?>
<div id="mode">
<p>**FOLIO-CHANNEL**</p>
</div>
<div id="wrapper">
    <div id="menu">
        <p class="welcome">Welcome to Folio-mode, <?php echo $_SESSION['name']; ?></p>
        <p name="logout" class="logout"><a id="exit" href=#>Exit tsatti</a></p>
        <div style="clear:both"></div>
    </div>    
<div id="chatbox"><?php


//LOADS LOGS FROM FILE INTO CHATBOX AREA
if(file_exists("/tmp/folio.html") && filesize("/tmp/folio.html") > 0){
    expired();
    $handle = fopen("/tmp/folio.html", "r");
    $contents = fread($handle, filesize("/tmp/folio.html"));
    fclose($handle);
     
    echo $contents;
}
?>

</div>
<form id="receive" name="receivemsg" action="" method="post">
	<input id="receive" name="receive" type="submit" value="Receive messages"/>
</form>
<div id="users">
<p>Currently online:<?php

//LOADS ONLINE USERS FROMFILE INTO ONLINE USERS AREA
if(file_exists("users.txt") && filesize("users.txt") > 0){
    expired();
    $handle = fopen("users.txt", "r");
    $contents = fread($handle, filesize("users.txt"));
    fclose($handle);
    echo $contents;
}
?>
</p>
</div>
    <form name="message" action="" method="post">
        <input name="usermsg" type="text" id="usermsg" maxlength="50" size="63" autofocus />
        <input name="submitmsg" type="submit" id="submitmsg" value="Send" />
    </form>
</div>

<div id="controlpanel">
<div id="tools">System tools</div>
    <form name="control" id="control" action="" method="post">
        <input name="clear" id="clear" type="submit" value="Clear logs"></input>
        <br>
        <input name="destroy" id="destroy" type="submit" value="Kill all sessions"></input>
        <br>
        
<a href="http://www.tsatti.info/">Normal mode</a>

    </form>
</div>


<script src="/script_folio.js">
</script>


<?php
}?>