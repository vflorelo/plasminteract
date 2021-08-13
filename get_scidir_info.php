<?php
$pii = $_POST['pii'];
$uuid = uniqid();
shell_exec("python3 get_scidir_info.py \"$pii\" > $uuid.xml 2> $uuid.err");
if(file_exists("$uuid.xml")){
  $xml_str    = file_get_contents("$uuid.xml");
  unlink(realpath("$uuid.xml"));
  unlink(realpath("$uuid.err"));
  }
else{
  $xml_str = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".
             "<sciencedirect_info>\n".
             "  <query_str>2</query_str>\n".
             "</sciencedirect_info>";
  }
print $xml_str ;
?>
