<?php
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
$uuid = uniqid();
$tsv_file_name     = $_FILES["tsv_file"]["name"];
$tsv_tmp_file_name = $_FILES["tsv_file"]["tmp_name"];
$tsv_file_error    = $_FILES["tsv_file"]["error"];
$xml_str  = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".
            "<tsv2idlist>\n";
if (move_uploaded_file($tsv_tmp_file_name, "$uuid.tsv")) {
  $xml_str  .= "  <query_str>0</query_str>\n";
  $file_str = file_get_contents("$uuid.tsv");
  $file_arr = explode("\n",$file_str);
  $row_count = count($file_arr);
  $col_count = count(explode(",",$file_arr[0]));
  for ($i=0;$i<$col_count;$i++){
    $xml_str  .= "  <entry>\n";
    for ($j=0;$j<$row_count;$j++){
      $row_str = $file_arr[$j];
      $row_arr = explode(",",$row_str);
      $val_str = $row_arr[$i];
      if(!empty($val_str)){
        if($j==0){
          $xml_str  .= "    <name>$val_str</name>\n";
          }
        else{
          $xml_str  .= "    <accession>$val_str</accession>\n";
          }
        }
      }
      $xml_str  .= "  </entry>\n";
    }
    $xml_str  .= "</tsv2idlist>\n";
    unlink(realpath("$uuid.tsv"));
  }
print $xml_str;
?>
