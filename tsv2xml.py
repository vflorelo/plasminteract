#!/usr/bin/python
import pandas as pd
df = pd.read_csv("file.tsv").fillna("")
df = df.transpose()
xml_str = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"
xml_str += "<tsv2xml>\n"
if len(df>0):
    xml_str += "  <query_str>0</query_str>\n"
    for col_name in list(df.index):
        xml_str += "  <entry>\n"
        xml_str += "    <name>"+str(col_name)+"</name>\n"
        col_data = filter(None,df.loc[col_name])
        for cell_data in col_data:
            xml_str += "    <accession>"+str(cell_data)+"</accession>\n"
        xml_str += "  </entry>\n"
    xml_str += "</tsv2xml>"
else:
    xml_str += "  <query_str>1</query_str>\n"
    xml_str += "</tsv2xml>"
with open("test.xml","w") as file:
    file.write(xml_str)
print(xml_str)
