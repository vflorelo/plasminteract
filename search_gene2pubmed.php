<?php
$db_host         = "localhost";
$db_user         = "root";
$db_pass         = "V84012561m";
$gene2pubmed_db  = "gene2pubmed";
$gene2pubmed_cxn = mysqli_connect($db_host,$db_user,$db_pass,$gene2pubmed_db);
mysqli_set_charset($gene2pubmed_cxn, "utf8");
$organism_tax_id = $_POST['organism_tax_id'];
$protein_gene_id = $_POST['protein_gene_id'];
$direct_hits_q   = "SELECT DISTINCT `PubMed_ID` FROM `gene2pubmed` WHERE `GeneID` = \"$protein_gene_id\" AND `tax_id`  = \"$organism_tax_id\"; ";
$xt_hits_q       = "SELECT DISTINCT `PubMed_ID` FROM `gene2pubmed` WHERE `GeneID` = \"$protein_gene_id\" AND `tax_id` != \"$organism_tax_id\"; ";
$direct_hits_res = mysqli_query($gene2pubmed_cxn,$direct_hits_q);
$xt_hits_res     = mysqli_query($gene2pubmed_cxn,$xt_hits_q);
$inner_xml_str   = "" ;
$num_direct_hits = mysqli_num_rows($direct_hits_res);
$num_xt_hits     = mysqli_num_rows($xt_hits_res);
$xml_str = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".
           "<gene2pubmed>\n";
if($direct_hits_res || $xt_hits_res){
  $query_str = "0" ;
  $inner_xml_str = "  <num_direct_hits>$num_direct_hits</num_direct_hits>\n".
                   "  <num_xt_hits>$num_xt_hits</num_xt_hits>\n".
                   "  <direct_hits>\n";
  foreach($direct_hits_res as $direct_hit){
    $direct_pmid = $direct_hit["PubMed_ID"];
    $inner_xml_str .= "    <entry>$direct_pmid</entry>\n";
    }
  $inner_xml_str .= "  </direct_hits>\n".
                    "  <xt_hits>\n";
  foreach($xt_hits_res as $xt_hit){
    $xt_pmid = $xt_hit["PubMed_ID"];
    $inner_xml_str .= "    <entry>$xt_pmid</entry>\n";
    }
  $inner_xml_str .= "  </xt_hits>\n";
  }
else{
  $query_str = "1" ;
  $inner_xml_str .= "  <num_direct_hits>0</num_direct_hits>\n".
                   "  <num_xt_hits>0</num_xt_hits>\n";
  }
$xml_str .= "  <query_str>$query_str</query_str>\n".
            "$inner_xml_str".
            "</gene2pubmed>\n";
print $xml_str;
?>
