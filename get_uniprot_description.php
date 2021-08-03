<?php
$uniprot_accession = $_POST['uniprot_accession'];
$accession_number  = $_POST['accession_number'];
$curl_handle      = curl_init();
curl_setopt($curl_handle,CURLOPT_URL,"https://www.ebi.ac.uk/proteins/api/proteins?offset=0&size=1&accession=$uniprot_accession");
curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,10);
curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,10);
$uniprot_response = curl_exec($curl_handle);
curl_close($curl_handle);
if (empty($uniprot_response)){
  $local_response = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".
                    "<uniprot xmlns=\"http://uniprot.org/uniprot\" ".
                    "xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" ".
                    "xsi:schemaLocation=\"http://uniprot.org/uniprot ".
                    "http://www.uniprot.org/support/docs/uniprot.xsd\">\n".
                    "  <query_str>1</query_str>\n".
                    "  <accession_number>$accession_number</accession_number>\n".
                    "  <uniprot_accession>$uniprot_accession</uniprot_accession>\n".
                    "</uniprot>";
  print $local_response ;
  }
else{
  $local_response    = new SimpleXMLElement($uniprot_response);
  $query_str         = $local_response->addChild("query_str","0");
  $accession_number  = $local_response->addChild("accession_number","$accession_number");
  $uniprot_accession = $local_response->addChild("uniprot_accession","$uniprot_accession");
  print $local_response->asXML();
  }
?>
