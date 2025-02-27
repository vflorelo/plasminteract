#!/usr/bin/python3
from elsapy.elsclient import ElsClient
from elsapy.elssearch import ElsSearch
import sys
search_term = sys.argv[1]
client = ElsClient('1f1d592aea7bde7750aaa2701208a90a')
client.inst_token = ''
search_obj = ElsSearch(search_term, 'sciencedirect')
search_obj.execute(client, get_all=True)
xml_str = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"
xml_str += "<elsevier_results>\n"
hit_count = len(search_obj.results_df)
if(hit_count > 0):
    xml_str += "  <count>"+str(hit_count)+"</count>\n"
    search_res = search_obj.results_df[["pii", "link"]]
    for i in range(len(search_res)):
        pii = search_res["pii"][i]
        link = search_res["link"][i]["scidir"]
        xml_str += "  <entry>\n"
        xml_str += "    <pii>"+pii+"</pii>\n"
        xml_str += "    <link>"+link+"</link>\n"
        xml_str += "  </entry>\n"
    xml_str += "</elsevier_results>\n"
else:
    xml_str += "  <query_str>1</query_str>\n"
    xml_str += "</elsevier_results>\n"
print(xml_str)
