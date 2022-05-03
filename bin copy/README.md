# 无版权音乐数据整理

## 更新数据的流程

1. 采集原站数据

    1. 打开如 `https://freepd.com/upbeat.php` 的网页
    2. 打开控制台，注入以下代码：

        ```js
        var lis = document.querySelectorAll('body > div.LineDSectional.mainContent > div > table > tbody > tr')
        var data = []
        for (var i = 0; i < lis.length; i++) {
            var item = lis[i]
            var title = item.querySelector('b').innerText
            var url = item.querySelector('source').src
            var time = item.querySelectorAll('td')[3].innerHTML.split('&nbsp;')[1]
            var msg = item.querySelectorAll('td')[5].innerText
            data.push({title: title, url: url, time: time, msg: msg, type: 'Misc'})
        }
        console.log(JSON.stringify(data))
        ```

    3. 最后有一个网页是 `page2`，结构与前面的几个网页不同，而生成的数据需要能合并到上面的数据中，脚本需要另外写
    4. 获得数据 `[{},{},...]`，将 `[]` 中的内容复制保存下来，连续对多个网页执行此操作，最后将获取到的所有内容存到一个大的 JSON 文件中，从而生成 `data.json`

2. 将 `data.json` 放到本程序的目录中，运行 `将原站数据title作为Key提前.py`，将原站采集数据的各项 title 前置为 Key ，组成对象，并生成 `dataFront.json`
3. 采集云盘文件目录数据
   1. 根据下面的 API，获得 `p1.txt` - `p11.txt`，放入本程序目录的 `files_json` 文件夹内

      - 请求地址：`https://www.123pan.com/a/api/file/list/new?driveId=0&limit=100&next=0&orderBy=fileId&orderDirection=desc&parentFileId=1671325&trashed=false&SearchData=&Page=1`
  
      - 参数说明：将 `page` 从 1 加到 11，采集后分别保存到 `1.txt` 到 `11.txt` 中
      - Header
  
        ```http
        Content-type: application/x-www-form-urlencoded
        authorization: 身份令牌
        ```
    
    1. 运行 `组合JSON.py` 将获取的 11 个 TXT 文件中的 JSON 数据进行合并，生成 `allData.min.json`
4. 运行 `将原站数据合并到文件列表数据.py`，将原站获取的数据和云盘文件列表数据进行合并，生成 `data.good.full.json`
5. 在服务器上运行 `写入数据库.php`，将数据写入到数据库，数据更新就此完成


## 对一堆JSON文件说明

### `data.json` -- 原站采集数据

```json
{
    "title": "Advertime",
    "url": "https://freepd.com/music/Advertime.mp3",
    "time": "2:14",
    "msg": "Written by Rafael Krux. Uplifting, positive and light track made with acoustic guitars, piano and light percussion. Perfect for commercials, advertising or generally happy moments. This music is available for commercial and non-commercial purposes.",
    "musicType": "upbeat"
}
```

### `dataFront.json` -- 原站采集数据（title前置为Key版本）

```json
"Advertime": {
    "title": "Advertime",
    "url": "https://freepd.com/music/Advertime.mp3",
    "time": "2:14",
    "msg": "Written by Rafael Krux. Uplifting, positive and light track made with acoustic guitars, piano and light percussion. Perfect for commercials, advertising or generally happy moments. This music is available for commercial and non-commercial purposes.",
    "musicType": "upbeat"
}
```

### `allData.json` -- 云盘文件列表数据

```json
{
    "FileId": 1671329,
    "FileName": "120 Monster.mp3",
    "Type": 0,
    "Size": 1034973,
    "ContentType": "0",
    "S3KeyFlag": "1811719527-0",
    "CreateAt": "2022-05-04T01:32:25+08:00",
    "UpdateAt": "2022-05-04T01:32:25+08:00",
    "Hidden": false,
    "Etag": "ce5bbc41dead305724638a394b6dc439",
    "Status": 101,
    "ParentFileId": 1671325,
    "Category": 0,
    "PunishFlag": 0,
    "ParentName": "",
    "DownloadUrl": "https://download.123pan.cn/123-57/ce5bbc41/1811719527-0/ce5bbc41dead305724638a394b6dc439?v=1&t=1651689011&s=bc1a35d280bfe52d4aaaf42a01249d3e&filename=120 Monster.mp3"
}
```

### `allData.min.json` -- 云盘文件列表数据（提取出的关键数据）

```json
{
    "etag": "e5e919cd53a18d809f32f0c77a68aa6b",
    "fileId": 1672351,
    "downloadUrl": "https://download.123pan.cn/123-619/e5e919cd/1811719527-0/e5e919cd53a18d809f32f0c77a68aa6b?v=1&t=1651689249&s=8a99a4d403eb6ea6b059ae5c7a79cad1&filename=You, Yourself and the Main Character.mp3",
    "s3keyFlag": "1811719527-0",
    "type": 0,
    "fileName": "You, Yourself and the Main Character.mp3",
    "size": 12820990
}
```

### `data.good.json` -- 原站采集数据与云盘文件列表数据合并后数据

```json
{
    "FileId": 1671329,
    "FileName": "120 Monster.mp3",
    "Type": 0,
    "Size": 1034973,
    "ContentType": "0",
    "S3KeyFlag": "1811719527-0",
    "CreateAt": "2022-05-04T01:32:25+08:00",
    "UpdateAt": "2022-05-04T01:32:25+08:00",
    "Hidden": false,
    "Etag": "ce5bbc41dead305724638a394b6dc439",
    "Status": 101,
    "ParentFileId": 1671325,
    "Category": 0,
    "PunishFlag": 0,
    "ParentName": "",
    "DownloadUrl": "https://download.123pan.cn/123-57/ce5bbc41/1811719527-0/ce5bbc41dead305724638a394b6dc439?v=1&t=1651689011&s=bc1a35d280bfe52d4aaaf42a01249d3e&filename=120 Monster.mp3",
    "musicType": "Other",
    "msg": "Bizarre This is a piece that was constructed as a demo for piano loops that I had released at one time. It is still one of the more popular pieces I have, but the quality is such that I can't license it in good conscience. :-) Source: Kevin MacLeod - Kevin MacLeod",
    "time": ""
}
```

### `data.good.min.json` -- 原站采集数据与云盘列表关键数据合并后数据

```json
{
    "etag": "ce5bbc41dead305724638a394b6dc439",
    "fileId": 1671329,
    "downloadUrl": "https://download.123pan.cn/123-57/ce5bbc41/1811719527-0/ce5bbc41dead305724638a394b6dc439?v=1&t=1651689011&s=bc1a35d280bfe52d4aaaf42a01249d3e&filename=120 Monster.mp3",
    "s3keyFlag": "1811719527-0",
    "type": 0,
    "fileName": "120 Monster.mp3",
    "size": 1034973,
    "musicType": "Other",
    "msg": "Bizarre This is a piece that was constructed as a demo for piano loops that I had released at one time. It is still one of the more popular pieces I have, but the quality is such that I can't license it in good conscience. :-) Source: Kevin MacLeod - Kevin MacLeod",
    "time": ""
}
```