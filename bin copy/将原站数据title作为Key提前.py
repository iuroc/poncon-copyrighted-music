import json

fromData = json.load(open('data.json', 'r', encoding='utf-8'))
data = {}

for i in fromData:
    data[i['title']] = i
json.dump(data, open('dataFront.json', 'w', encoding='utf-8'))
print('ok')
