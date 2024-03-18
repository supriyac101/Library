<?php
    $_fileName = "";
    $path = pathinfo(realpath($_fileName), PATHINFO_DIRNAME);
    
    $zip = new ZipArchive;
    $res = $zip->open($_fileName);
    if ($res === TRUE) {
        $zip->extractTo($path);
        $zip->close();
        echo 'woot!';
    } else {
        echo 'doh!';
    }
?>