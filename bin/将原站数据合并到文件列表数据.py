import json

fromData = json.load(open('allData.json', 'r', encoding='utf-8'))
toData = json.load(open('dataFront.json', 'r', encoding='utf-8'))
data = []
for i in fromData:
    try:
        title = i['FileName'].split('.mp3')[0]
        j = toData[title]
        i['musicType'] = j['musicType']
        i['msg'] = j['msg']
        i['time'] = j['time']
        data.append(i)
    except:
        pass
json.dump(data, open('data.good.full.json', 'w', encoding='utf-8'))
print('ok')
