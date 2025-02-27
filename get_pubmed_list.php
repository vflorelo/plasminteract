<?php
$accession_number = $_POST['accession_number'];
$curl_handle      = curl_init();
curl_setopt($curl_handle,CURLOPT_URL,"https://eutils.ncbi.nlm.nih.gov/entrez/eutils/esearch.fcgi?api_key=42bcbdb562c2d7210f2287de0a7e6b0c7d09&db=pubmed&term=$accession_number&retmode=xml");
curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,10);
curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,10);
$pubmed_response = curl_exec($curl_handle);
if (empty($pubmed_response)){
  $local_response = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".
                    "<eSearchResult>\n".
                    "  <query_str>1</query_str>\n".
                    "  <accession_number>$accession_number</accession_number>\n".
                    "</eSearchResult>";
  print $local_response ;
  }
else{
  $local_response   = new SimpleXMLElement($pubmed_response);
  $query_str        = $local_response->addChild("query_str","0");
  $accession_number = $local_response->addChild("accession_number","$accession_number");
  print $local_response->asXML();
  }
?>
