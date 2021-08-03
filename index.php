<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <script src="js/jquery-3.6.0.min.js" ></script>
  <script src="js/bootstrap.min.js" ></script>
  <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css" />
  <link rel="stylesheet" href="css/bootstrap-icons.css  " type="text/css" />
  <meta name="keywords" content="" />
  <meta name="description" content="" />
  <title>.:PlamInteract:. .:Waller Lab:.</title>
  <style>
    #input_area       {position: absolute; right:0 ; bottom:0 ; z-index: 1}
    #show_hide_button {position: absolute; right:0 ; bottom:0 ; z-index: 500}
    .overflowed{height:100%;overflow-y:auto}
    .full{height:100%}
    .half{height:50%}
    .third{height:33%}
  </style>
</head>
<body onload="resize_divs()">
<div id="show_hide_button" class="btn btn-success bi bi-arrow-down-square-fill" type="button" data-bs-toggle="collapse" data-bs-target="#input_area" aria-expanded="false" aria-controls="input_area" onclick="toggle_up_down('down')"></div>
<div class="card col-6 collapse show" id="input_area">
  <div class="card card-header bg-primary">Input area</div>
  <div class="card card-body">
    <div class="row">
      <div class="col-5">
        <label class="control-label" for="accession_number">Accession number
          <span id="str_search_span" class="bi bi-search" onclick=""></span>
        </label>
        <input type="text" class="form-control" id="accession_number" name="accession_number" oninput="update_search_button()"/>
      </div>
      <div class="col-5">
        <label class="control-label" for="tsv_file">From file</label>
        <input type="file" class="form-control-file" id="tsv_file" name="tsv_file" onchange="enable_upload_button(this.value);"/>
      </div>
      <div class="col-2">
        <div class="btn btn-primary bi bi-upload" id="upload_button" onclick="tsv2idlist()"></div>
      </div>
    </div>
  </div>
</div>
<div class="row" id="container">
  <div class="col-3 full">
    <div class="card full">
      <div class="card card-header bg-warning">Accession number list</div>
      <div class="card card-body overflowed" id="accession_number_list"></div>
    </div>
  </div>
  <div class="col-9 full">
    <div class="row half">
      <div class="col-6 full">
        <div class="card full">
          <div class="card card-header bg-primary">Links</div>
          <div class="card card-body overflowed" id="link_list"></div>
        </div>
      </div>
      <div class="col-6 full">
        <div class="card full">
          <div class="card card-header bg-info">Elsevier info</div>
          <div class="card card-body overflowed" id="elsevier_list"></div>
        </div>
      </div>
    </div>
    <div class="row half">
      <div class="col-6 full">
        <div class="card full">
          <div class="card card-header bg-info">PubMed info</div>
          <div class="card card-body overflowed" id="pmid_list"></div>
        </div>
      </div>
      <div class="col-6 full">
        <div class="card full">
          <div class="card card-header bg-info">PubMed additional info</div>
          <div class="card card-body overflowed" id="pmid_list_2"></div>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
<script type="text/javascript">
function resize_divs(){
  var body_height = $(window).height() ;
  var body_width  = $(window).width() ;
  $("#container").css({"height":body_height,"width":body_width,"position":"fixed","top":0,"left":0});
  }
function update_search_button(){
  var accession_number = $("#accession_number").val()
  $("#str_search_span").attr("onclick","get_uniprot_acc(\""+accession_number+"\");get_pubmed_list(\""+accession_number+"\");")
}
function toggle_up_down(direction){
  if(direction == "up"){
    $("#show_hide_button").attr("onclick","toggle_up_down('down')")
    $("#show_hide_button").removeClass("bi-arrow-up-square-fill").addClass("bi-arrow-down-square-fill")
    }
  else{
    $("#show_hide_button").attr("onclick","toggle_up_down('up')")
    $("#show_hide_button").removeClass("bi-arrow-down-square-fill").addClass("bi-arrow-up-square-fill")
    }
  }
function get_uniprot_acc(accession_number){
  clear_divs();
  var form_data  = new FormData()       ;
  var target_url = "get_uniprot_acc.php" ;
  form_data.append("accession_number", accession_number)  ;
  $.ajax({
    url: target_url,
    dataType: 'script',
    cache: false,
    contentType: false,
    processData: false,
    data: form_data,
    type: 'post',
    complete: function (uniprot){
                var uniprot_xml_data = uniprot.responseText ;
								var xml_data         = $.parseXML(uniprot_xml_data);
								$xml_data            = $(xml_data);
                var query_str        = $xml_data.find("query_str").text()
                var accession_number = $xml_data.find("accession_number").text()
                if(query_str==0){
                  var uniprot_accession = $xml_data.find("entry").children("accession").text();
                  var gene_name         = $xml_data.find("entry").children("gene").children("name").text();
                  var protein_name      = $xml_data.find("entry").children("protein").children("submittedName").children("fullName").text();
                  var organism_name     = $xml_data.find("entry").children("organism").children("name").text();
                  if (uniprot_accession != "" && gene_name == accession_number){
                    get_uniprot_description(uniprot_accession,accession_number);
                    search_pubmed(organism_name,protein_name);
                    search_elsevier(organism_name,protein_name);
                    }
                  }
                }
    })
  }
function get_uniprot_description(uniprot_accession,accession_number){
  var form_data  = new FormData()       ;
  var target_url = "get_uniprot_description.php" ;
  form_data.append("uniprot_accession", uniprot_accession)  ;
  form_data.append("accession_number", accession_number)  ;
  $.ajax({
    url: target_url,
    dataType: 'script',
    cache: false,
    contentType: false,
    processData: false,
    data: form_data,
    type: 'post',
    complete: function (uniprot){
                var uniprot_xml_data = uniprot.responseText ;
								var xml_data         = $.parseXML(uniprot_xml_data);
								$xml_data            = $(xml_data);
                var query_str        = $xml_data.find("query_str").text()
                var accession_number = $xml_data.find("accession_number").text()
                if(query_str==0){
                  var uniprot_accession = $xml_data.find("entry").children("accession").text();
                  var gene_name = $xml_data.find("entry").children("gene").children("name").text();
                  if (uniprot_accession != "" && gene_name == accession_number){
                    var list_html_str = "<ul class=\"list-group\">\n";
                    var protein_name = $xml_data.find("entry").children("protein").children("submittedName").children("fullName").text()
                    $xml_data.find("entry").children("dbReference").each(function (){
    	                var dbreference_type = $(this).attr("type");
                      var dbreference_id   = $(this).attr("id");
                      var dest_url      = "" ;
                      var reactome_path = "" ;
                      switch(dbreference_type){
                        case "EMBL"    :
                          dest_url = "https://www.ebi.ac.uk/ena/browser/view"+dbreference_id         ;
                          class_str="list-group-item-success"    ;
                          list_html_str += "<li class=\"list-group-item "+class_str+"\">"+dbreference_type+": <a href=\""+dest_url+"\" target=\"_blank\">"+dbreference_id+"</a></li>\n";
                          break;
                        case "RefSeq"  :
                          dest_url = "https://www.ncbi.nlm.nih.gov/protein/"+dbreference_id          ;
                          class_str="list-group-item-primary"    ;
                          list_html_str += "<li class=\"list-group-item "+class_str+"\">"+dbreference_type+": <a href=\""+dest_url+"\" target=\"_blank\">"+dbreference_id+"</a></li>\n";
                          break;
                        case "IntAct"  :
                          dest_url = "https://www.ebi.ac.uk/intact/query/"+dbreference_id            ;
                          class_str="list-group-item-warning"    ;
                          list_html_str += "<li class=\"list-group-item "+class_str+"\">"+dbreference_type+": <a href=\""+dest_url+"\" target=\"_blank\">"+dbreference_id+"</a></li>\n";
                          break;
                        case "KEGG"    :
                          dest_url = "https://www.genome.jp/entry/"+dbreference_id                   ;
                          class_str="list-group-item-secondnary" ;
                          list_html_str += "<li class=\"list-group-item "+class_str+"\">"+dbreference_type+": <a href=\""+dest_url+"\" target=\"_blank\">"+dbreference_id+"</a></li>\n";
                          break;
                        case "InterPro":
                          dest_url = "https://www.ebi.ac.uk/interpro/entry/InterPro/"+dbreference_id ;
                          class_str="list-group-item-danger"     ;
                          list_html_str += "<li class=\"list-group-item "+class_str+"\">"+dbreference_type+": <a href=\""+dest_url+"\" target=\"_blank\">"+dbreference_id+"</a></li>\n";
                          break;
                        case "Reactome":
                          dest_url       = "https://reactome.org/content/detail/"+dbreference_id ;
                          reactome_type  = $(this).children("property").attr("type");
                          reactome_value = $(this).children("property").attr("value");
                          class_str="list-group-item-info";
                          list_html_str += "<li class=\"list-group-item "+class_str+"\">"+dbreference_type+": <a href=\""+dest_url+"\" target=\"_blank\">"+dbreference_id+"</a></li>\n";
                          break;
                        }

    									});
                    $("#link_list").html(list_html_str);
                    }
                  }
                }
    })
  }
function get_pubmed_list(accession_number){
  clear_divs();
  var form_data  = new FormData()       ;
  var target_url = "get_pubmed_list.php" ;
  form_data.append("accession_number", accession_number)  ;
  $.ajax({
    url: target_url,
    dataType: 'script',
    cache: false,
    contentType: false,
    processData: false,
    data: form_data,
    type: 'post',
    complete: function (eSearchResult){
                var eSearchResult_xml_data = eSearchResult.responseText ;
								var xml_data         = $.parseXML(eSearchResult_xml_data);
								$xml_data            = $(xml_data);
                var query_str        = $xml_data.find("query_str").text();
                var accession_number = $xml_data.find("accession_number").text();
                var html_str = "";
                if(query_str==0){
                  var num_hits = $xml_data.children("eSearchResult").children("Count").text();
                  if (num_hits>=1){
                    html_str = "<ul class=\"list-group\">\n";
                    $xml_data.find("IdList").children("Id").each(function (){
                      html_str += "  <li class=\"list-group-item list-group-item-info\">"+
                                  "    <a href=\"https://pubmed.ncbi.nlm.nih.gov/"+$(this).text()+
                                  "\" target=\"_blank\">"+$(this).text()+"</a>"+
                                  "    <span class=\"bi bi-search\" onclick=\"get_pubmed_info("+$(this).text()+")\" ></span>"+
                                  "  </li>\n";
                    });
                    html_str += "</ul>";
                  }
                  else{
                    html_str = "<div class=\"alert alert-warning\">No hits found</div>"
                  }
                }
                else{
                  html_str = "<div class=\"alert alert-danger\">Error, try again later</div>"
                }
                $("#pmid_list").html(html_str)
              }
    })
  }
function search_pubmed(organism_name,search_term){
  var form_data  = new FormData()       ;
  var target_url = "search_pubmed.php" ;
  form_data.append("organism_name", organism_name)  ;
  form_data.append("search_term", search_term)  ;
  $.ajax({
    url: target_url,
    dataType: 'script',
    cache: false,
    contentType: false,
    processData: false,
    data: form_data,
    type: 'post',
    complete: function (eSearchResult){
                var eSearchResult_xml_data = eSearchResult.responseText ;
								var xml_data         = $.parseXML(eSearchResult_xml_data);
								$xml_data            = $(xml_data);
                var query_str        = $xml_data.find("query_str").text();
                var html_str = "";
                if(query_str==0){
                  var num_hits = $xml_data.children("eSearchResult").children("Count").text();
                  if (num_hits>=1){
                    html_str = "<ul class=\"list-group\">\n";
                    $xml_data.find("IdList").children("Id").each(function (){
                      html_str += "  <li class=\"list-group-item list-group-item-info\">"+
                                  "    <a href=\"https://pubmed.ncbi.nlm.nih.gov/"+$(this).text()+
                                  "\" target=\"_blank\">"+$(this).text()+"</a>"+
                                  "    <span class=\"bi bi-search\" onclick=\"get_pubmed_info("+$(this).text()+")\" ></span>"+
                                  "  </li>\n";
                    });
                    html_str += "</ul>";
                  }
                  else{
                    html_str = "<div class=\"alert alert-warning\">No hits found</div>"
                  }
                }
                else{
                  html_str = "<div class=\"alert alert-danger\">Error, try again later</div>"
                }
                $("#pmid_list_2").html(html_str)
              }
    })
  }
function search_elsevier(organism_name,search_term){
  var form_data  = new FormData()       ;
  var target_url = "search_elsevier.php" ;
  form_data.append("organism_name", organism_name)  ;
  form_data.append("search_term", search_term)  ;
  $.ajax({
    url: target_url,
    dataType: 'script',
    cache: false,
    contentType: false,
    processData: false,
    data: form_data,
    type: 'post',
    complete: function (elsevier_results){
                var elsevier_xml_data = elsevier_results.responseText ;
								var xml_data         = $.parseXML(elsevier_xml_data);
								$xml_data            = $(xml_data);
                var query_str        = $xml_data.find("query_str").text();
                var html_str = "";
                var pii = "";
                var link = "";
                if(query_str==0){
                  html_str = "<ul class=\"list-group\">\n"
                  $xml_data.find("entry").each(function (){
                    pii  = $(this).find("pii").text();
                    link = $(this).find("pii").text();
                    html_str += "  <li class=\"list-group-item\"><a href=\""+link+"\" target=\"_blank\">"+pii+"</a></li>\n";
                    });
                  html_str = "</ul>\n"
                  }
                else{
                  html_str = "<div class=\"alert alert-warning\">No hits found</div>\n"
                  }
                $("#elsevier_list").html(html_str)
                }
    })
  }
function get_pubmed_info(pubmed_id){
  var form_data  = new FormData()       ;
  var target_url = "get_pubmed_info.php" ;
  form_data.append("pubmed_id", pubmed_id)  ;
  $.ajax({
    url: target_url,
    dataType: 'script',
    cache: false,
    contentType: false,
    processData: false,
    data: form_data,
    type: 'post',
    complete: function (PubmedArticleSet){
                var PubmedArticleSet_xml_data = PubmedArticleSet.responseText ;
								var xml_data         = $.parseXML(PubmedArticleSet_xml_data);
								$xml_data            = $(xml_data);
                var query_str        = $xml_data.find("query_str").text();
                var accession_number = $xml_data.find("accession_number").text();
                var html_str = "";
                if(query_str==0){
                  var title_str      = $xml_data.find("PubmedArticle").find("Article").find("ArticleTitle").text();
                  var abstract_str   = $xml_data.find("PubmedArticle").find("Article").find("Abstract").find("AbstractText").text();
                  var author_str     = ""
                  var auth_lastname  = ""
                  var auth_firstname = ""
                  $xml_data.find("PubmedArticle").find("Article").find("AuthorList").find("Author").each(function(){
                    auth_lastname    = $(this).find("LastName").text()
                    auth_firstname   = $(this).find("ForeName").text()
                    author_str += auth_lastname+" "+auth_firstname+", "
                  });
                  var html_str = "<div class=\"card\" >\n"+
                                 "  <div class=\"card-header\">"+title_str+"</div>\n"+
                                 "  <div class=\"card-body\">"+abstract_str+"</div>\n"+
                                 "  <div class=\"card-footer\">"+author_str+"</div>\n"+
                                 "</div>\n";
                }
                $("#article_div").html(html_str)
              }
    })
  }
function enable_upload_button(value){
  (value=="")? $("#upload_button").attr("disabled",true).attr("enabled",false): $("#upload_button").attr("disabled",false).attr("enabled",true) ;
  }
function tsv2idlist() {
  var form_data = new FormData();
  var tsv_file  = $("#tsv_file").prop("files")[0];
  form_data.append("tsv_file",tsv_file);
  $.ajax({
    url: "tsv2idlist.php",
    dataType: 'json',
    cache: false,
    contentType: false,
    processData: false,
    data: form_data,
    type: 'post',
    complete: function (tsv2idlist){
                var tsv2idlist_xml_data  = tsv2idlist.responseText ;
                var xml_data          = $.parseXML(tsv2idlist_xml_data);
                $xml_data             = $(xml_data);
                var query_str         = $xml_data.find("query_str").text();
                var entry_name        = ""
                var accession         = ""
                if (query_str==0){
                  html_str = "<ul class=\"list-group\">\n";
                  $xml_data.find("entry").each(function(){
                    entry_name=$(this).find("name").text()
                    html_str += "  <li class=\"list-group-item list-group-item-primary\">"+entry_name+"</li>\n"
                    $(this).find("accession").each(function(){
                      accession = $(this).text()
                      html_str += "  <li class=\"list-group-item\">"+accession+" <span class=\"bi bi-search\" onclick=\"get_uniprot_acc('"+accession+"');get_pubmed_list('"+accession+"')\"></span></li>\n"
                    });
                  });
                  html_str +="</ul>"
                  $("#accession_number_list").html(html_str)
                  }
                }
    })
  }
function clear_divs(){
  $("#link_list").html("");
  $("#pmid_list").html("");
  $("#article_div").html("");
}
</script>
</html>
