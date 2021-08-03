<?php
$pubmed_id = $_POST['pubmed_id'];
$curl_handle      = curl_init();
curl_setopt($curl_handle,CURLOPT_URL,"https://eutils.ncbi.nlm.nih.gov/entrez/eutils/efetch.fcgi?api_key=42bcbdb562c2d7210f2287de0a7e6b0c7d09&db=pubmed&id=$pubmed_id&retmode=xml");
curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,10);
curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,10);
$pubmed_response = curl_exec($curl_handle);
if (empty($pubmed_response)){
  $local_response = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".
                    "<eSearchResult>\n".
                    "  <query_str>1</query_str>\n".
                    "  <pubmed_id>$pubmed_id</pubmed_id>\n".
                    "</eSearchResult>";
  print $local_response ;
  }
else{
  $local_response   = new SimpleXMLElement($pubmed_response);
  $query_str        = $local_response->addChild("query_str","0");
  $pubmed_id = $local_response->addChild("pubmed_id","$pubmed_id");
  print $local_response->asXML();
  }
?>
