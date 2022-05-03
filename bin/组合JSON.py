import json

data = []
for i in range(1, 12):
    filename = 'files_json/p' + str(i) + '.txt'
    subData = json.load(
        open(filename, 'r', encoding='utf-8'))['data']['InfoList']
    for j in subData:
        data.append({
            'etag': j['Etag'],
            'fileId': j['FileId'],
            'downloadUrl': j['DownloadUrl'],
            's3keyFlag': j['S3KeyFlag'],
            'type': j['Type'],
            'fileName': j['FileName'],
            'size': j['Size'],
        })

json.dump(data, open('allData.min.json', 'w', encoding='utf-8'))
print('ok')
