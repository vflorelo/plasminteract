<?php
$output = shell_exec("python search_elsevier.py \"Plasmodium falciparum AND 26S proteasome regulatory subunit RPN1\" > test.xml 2> test.err") ;
$output = shell_exec("cat test.xml test.err");
print $output;
?>
