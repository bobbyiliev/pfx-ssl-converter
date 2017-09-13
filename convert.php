<!DOCTYPE html>
<html>
   <head>
      <link rel="stylesheet" type="text/css" href="css/style.css">
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
