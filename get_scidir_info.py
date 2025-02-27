#!/usr/bin/python3
from elsapy.elsclient import ElsClient
from elsapy.elsdoc import FullDoc
import sys
pii = sys.argv[1]
client = ElsClient('1f1d592aea7bde7750aaa2701208a90a')
client.inst_token = ''
xml_str = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"
xml_str += "<sciencedirect_info>\n"
pii_doc = FullDoc(sd_pii=pii)
if pii_doc.read(client):
    xml_str += "  <query_str>0</query_str>\n"
    author_dict = pii_doc.data["coredata"]["dc:creator"]
    for author_element in author_dict:
        author_name = author_element["$"]
        xml_str += "  <author>"+author_name+"</author>\n"
    doc_title = pii_doc.title
    xml_str += "  <title>"+doc_title+"</title>\n"
    doc_abstract = pii_doc.data["coredata"]["dc:description"]
    xml_str += "  <abstract>"+doc_abstract+"</abstract>\n"
    xml_str += "</sciencedirect_info>"
else:
    xml_str += "  <query_str>1</query_str>\n"
    xml_str += "</sciencedirect_info>\n"
print(xml_str)
