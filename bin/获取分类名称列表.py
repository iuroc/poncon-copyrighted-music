import json

data = json.load(open('bin/data.good.min.json', 'r', encoding='utf-8'));
types = []
for i in data:
    if i['musicType'] not in types:
        types.append(i['musicType'])
json.dump(types, open('bin/types.json', 'w', encoding='utf-8'))