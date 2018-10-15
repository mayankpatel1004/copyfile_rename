<?php
$old_modulename = "Blog";
$new_modulename = "Link";
$basedirectory = "D:/wamp/www/Dropbox/testing/copyfile_rename/";
$controllerdirectory = $basedirectory."controller/";
$modeldirectory = $basedirectory."model/";
$viewdirectory = $basedirectory."view/";

// Copying controller from one directory to another directory //
$file_controller = $controllerdirectory.$old_modulename.'Controller.php';
$newfile_controller = $controllerdirectory.$new_modulename.'Controller.php';
if(!copy($file_controller, $newfile_controller)){
    echo "failed to create controller $newfile_controller";
}else{
    $fname = $newfile_controller;
    $fhandle = fopen($fname,"r");
    $content = fread($fhandle,filesize($fname));
    $content = str_replace($old_modulename,$new_modulename, $content);
    $fhandle = fopen($fname,"w");
    fwrite($fhandle,$content);
    fclose($fhandle);
}

// Copying model from one directory to another directory //
$file_model = $modeldirectory.$old_modulename.'.php';
$newfile_model = $modeldirectory.$new_modulename.'.php';
if(!copy($file_model, $newfile_model)){
    echo "failed to create model $newfile_model";
}else{
    $fname = $newfile_model;
    $fhandle = fopen($fname,"r");
    $content = fread($fhandle,filesize($fname));
    $content = str_replace($old_modulename,$new_modulename, $content);
    $fhandle = fopen($fname,"w");
    fwrite($fhandle,$content);
    fclose($fhandle);
}

// Copying view from one directory to another directory //
$file_view = $viewdirectory.strtolower($old_modulename);
$newfile_view = $viewdirectory.strtolower($new_modulename);
if ($handle = opendir($file_view)) {
    @mkdir(strtolower($newfile_view));
    while ($entry = readdir($handle)) {
        if(is_file($viewdirectory.strtolower($old_modulename)."/".$entry)){
            copy($viewdirectory.strtolower($old_modulename)."/".$entry,$viewdirectory.strtolower($new_modulename)."/".$entry);
            $fname = $viewdirectory.strtolower($new_modulename)."/".$entry;
            $fhandle = fopen($fname,"r");
            $content = fread($fhandle,filesize($fname));
            $content = str_replace($old_modulename,$new_modulename, $content);
            $fhandle = fopen($fname,"w");
            fwrite($fhandle,$content);
            fclose($fhandle);
        }
    }
    closedir($handle);
}