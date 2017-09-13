<!DOCTYPE html>
<html>
   <head>
	<style>
	/* Style inputs with type="text", select elements and textareas */
	input[type=text1], select, textarea {
	    //margin-right: auto; margin-left: auto; width: 40%;
	    width: 100%; /* Full width */
	    padding: 8px; /* Some padding */  
	    border: 1px solid #ccc; /* Gray border */
	    border-radius: 4px; /* Rounded borders */
	    box-sizing: border-box; /* Make sure that padding and width stays in place */
	    margin-top: 4px; /* Add a top margin */
	    margin-bottom: 8px; /* Bottom margin */
	    resize: vertical /* Allow the user to vertically resize the textarea (not horizontally) */
	}

	/* Style the submit button with a specific background color etc */
	input[type=submit] {
	    background-color: #4CAF50;
	    color: white;
	    padding: 10px 18px;
	    border: none;
	    border-radius: 4px;
	    cursor: pointer;
	}

	/* When moving the mouse over the submit button, add a darker green color */
	input[type=submit]:hover {
	    background-color: #45a049;
	}

	/* Add a background color and some padding around the form */
	.container {
	    margin-right: auto; margin-left: auto; width: 40%;
	    border-radius: 5px;
	    background-color: #f2f2f2;
	    padding: 10px;
	}

	.text {
	    align: center
	    width: 160%;
	    padding: 4px 8px;
	    margin: 6px 0;
	    box-sizing: border-box;
	    border: 1px solid #555;
	}

	.message {
	    margin-right: auto; margin-left: auto; width: 40%;
	    text: bold;
	    padding: 12px; /* Some padding */
	    border: 1px solid #ccc; /* Gray border */
	    border-radius: 4px; /* Rounded borders */
	    box-sizing: border-box; /* Make sure that padding and width stays in place */
	    margin-top: 6px; /* Add a top margin */
	    margin-bottom: 16px; /* Bottom margin */

	}	

	.downloadme {
	    margin-right: auto; margin-left: auto; width: 40%;
	    text: bold;
	}
	
	download {
	    background-color: #4CAF50;
	    color: white;
	    padding: 4px 8px;
	    border: none;
	    border-radius: 4px;
	    cursor: pointer;
	    float: right;
	}
	</style>
   <head>
         <div class="container">
            <form method="post">
               <div class="ssl1">
                  <p> Convert to pfx format. BETA</p>
                  <p> Certificate: </p>
                  <textarea class="ssl" id="cert" name="cert" COLS=65 ROWS=8></textarea>
               </div>
               <div class="ssl1">
                  <p> Private Key: </p>
                  <textarea class="ssl" id="key" name="key" COLS=65 ROWS=8></textarea>
               </div>
               <div class="ssl1">
                  <p> CaBundle: </p>
                  <textarea class="ssl" id="ca" name="ca" COLS=65 ROWS=8></textarea>
               </div>
               <div> Password: 
                  <input class="text" type="password" name="pass">
                  Domain:
                  <input class="text" type="text" name="domain">
               </div>
               <div> 
                  <input type="submit" value="Convert" name="subtmit" class="button1">
               </div>
            </form>
         </div>
</html>

<?php

if (isset($_POST['cert'])) {
    session_start();
    $_SESSION['post-data'] = $_POST;
    
    $cert   = $_SESSION['post-data']['cert'];
    $key    = $_SESSION['post-data']['key'];
    $ca     = $_SESSION['post-data']['ca'];
    $pass   = $_SESSION['post-data']['pass'];
    $domain = $_SESSION['post-data']['domain'] . ".pfx";
    
    $fileSSL = date('YmdHis', time()) . "-ssl.txt";
    $fileKEY = date('YmdHis', time()) . "-key.txt";
    $fileCA  = date('YmdHis', time()) . "-ca.txt";
    $tempDir = date('YmdHis', time()) . "-" . $_SESSION['post-data']['domain'];
    
    $createTempDir = mkdir("tmp/$tempDir", 0755, TRUE);
        
    if ($cert != null) {
        $fileSSLwrite = fopen("./tmp/$tempDir/" . $fileSSL, "w");
        fwrite($fileSSLwrite, $cert);
        $certificate = "./tmp/$tempDir/$fileSSL";
    }
    
    if ($key != null) {
        $fileKEYwrite = fopen("./tmp/$tempDir/" . $fileKEY, "w");
        fwrite($fileKEYwrite, $key);
        $private = "./tmp/$tempDir/$fileKEY";
    }
    
    if ($ca != null) {
        $fileCAwrite = fopen("./tmp/$tempDir/" . $fileCA, "w");
        fwrite($fileCAwrite, $ca);
        $bundle = "./tmp/$tempDir/$fileCA";
    }
    
    if ($domain !== null) {
        $pfx = shell_exec("/usr/bin/openssl pkcs12 -export -out ./tmp/$tempDir/$domain -inkey $private -in $certificate -certfile $bundle -password pass:$pass >> ./tmp/$tempDir/$domain ");
        echo $pfx;
    }
    
    $theFile             = "tmp/$tempDir/$domain";
    $_SESSION['thefile'] = $theFile;
    
    if (file_exists("./tmp/$tempDir/$domain") && (filesize($theFile) != 0)) {
        
        $fileArray = array(
            "$private",
            "$certificate",
            "$bundle"
        );
        
        foreach ($fileArray as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
        
        echo "<div class='message' >Click here to download the SSL: <a class='download' href='https://ssl.bobbyiliev.com/tmp/$tempDir/$domain'> DOWNLOAD </a>  <br /> Hurry up! The file will be deleted in 
59 seconds! </div>";
        echo '<div class="message" > <form method="post">Or pess this button delete the file now: <input type="submit" value="Delete" name="delete" class="button1"></form></div>';
        
    } else {
        //Deletes files if the conversion is not successful:
        
        $fileArray = array(
            "$private",
            "$certificate",
            "$bundle",
            "$theFile"
        );
        
        foreach ($fileArray as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
        
        echo "<div class='message'> Something went wrong, upload the file again! </div>";
    };
    
};

if (isset($_POST['delete'])) {
    session_start();
    echo "<div class='message' >The Certificate has been deleted from the server </div>";
    unlink($_SESSION['thefile']);
    session_unset();
}

?>
