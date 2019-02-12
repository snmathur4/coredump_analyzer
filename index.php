<html>
    <head>
        <style>
            body  {background-color: #E6F3F7;}
            #p01  {color: blue;}
        </style>
    </head>
    <body>
        <h1>Core dump backtrace tool</h1>
        <form action="" method="post" enctype="multipart/form-data">
          <p id="p01">Upload <b>coredump file</b> (ex. "eDelta.dp.gz")</p>
          <p><input type="file" name="file_array[]"></p>
          <p id="p01">Upload <b>symbols file</b> (ex. "RFS_irc_am335x_with_symbols.tar.gz")</p>
          <p><input type="file" name="file_array[]"></p>
          <input type="submit" value="Upload and get Stacktrace"> (Note: Takes upto <font color="red">30 seconds</font> to display the trace)
        </form>
        <hr color="green" size="2">
    </body>
</html>
<?php
    if(isset($_FILES['file_array'])){
        $errors= array();
        $name_array = $_FILES['file_array']['name'];
        $tmp_name_array = $_FILES['file_array']['tmp_name'];
        $type_array = $_FILES['file_array']['type'];
        $size_array = $_FILES['file_array']['size'];
        $error_array = $_FILES['file_array']['error'];
        
        $cores_base_dir = "/home/u-pc/Downloads/coredumps/";
        $analyze_core_script = $cores_base_dir."analyze.sh";
        $trace_file = $cores_base_dir."gdb.txt";
        
        for($i = 0; $i < count($name_array); $i++){
            $fileExt = pathinfo($name_array[$i], PATHINFO_EXTENSION);
            if ($fileExt != 'gz') {
                $errors[]='File extension not allowed: ".'.$fileExt.'"';
            } else {
                if (strpos($name_array[$i], 'eDelta.dp') !== false) {
                    $coreFileName = $name_array[$i];
                } elseif (strpos($name_array[$i], 'with_symbols.tar') !== false) {
                    $symbolsFileName = $name_array[$i];
                }
            }
        }
        
        if(empty($errors)==true) {
            $folderNum = 1;
            $folderExists = True;
            $cores_dir = "";
            while($folderExists == True){
                if (file_exists($cores_base_dir.$folderNum)) {
                    $folderNum++;
                } else {
                    $cores_dir = $cores_base_dir.$folderNum."/";
                    mkdir($cores_dir, 0777, true);
                    $folderExists = False;
                }
            }
            
            for($i = 0; $i < count($tmp_name_array); $i++){
                move_uploaded_file($tmp_name_array[$i], $cores_dir.$name_array[$i]);
            }
            
            shell_exec("sh ".$analyze_core_script." ".$coreFileName." ".$symbolsFileName);
            $myfile = fopen($trace_file, "r") or die("Unable to open file!");
            $readText = nl2br(fread($myfile,filesize($trace_file)));
            echo '<br><span style="color:#0000FF;text-align:left;">Showing GDB results for <b>"bt"</b> and <b>"thread apply all bt full"</b> commands</span><br><br>';
            echo $readText;
            fclose($myfile);
        }else{
            echo '<br><span style="color:red;text-align:left;"><b>Error: </b></span><br><br>';
            print_r($errors);
        }
    }
?>