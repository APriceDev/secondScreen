<?php
/*
Uploadify
Copyright (c) 2012 Reactive Apps, Ronnie Garcia
Released under the MIT License <http://www.opensource.org/licenses/mit-license.php> 
*/
set_time_limit(900);
if (!empty($_FILES)) {
    try {
        $tempFile = $_FILES['Filedata']['tmp_name'];
        
        $id = md5(date("YmdHisT") . $_FILES['Filedata']['name']);
        $ext  = explode('.', $_FILES['Filedata']['name']);
        $ext  = strtolower(end($ext));
        
        $targetFile = '/tmp/' . $id . '.' . $ext;
        
        move_uploaded_file($tempFile, $targetFile);
        
        echo json_encode(array('name' => $id, 'ext' => $ext, 'type' => $_FILES['Filedata']['type']));
    } catch (Exception $e) {
        $log_dir = '/tmp/';
        file_put_contents($log_dir . 'swfupload.log', date('Y-m-d H:i:s', time()) . ' : ' . $e->getMessage() . "\n", FILE_APPEND);
    }
}
