# **upload**

Here explain how to upload a file to that bucket.

#### Before You Begin
Make sure that you have registered an app and successfully acquired an OAuth token with scopes `bucket:create`, `bucket:read`, and `data:write`. This will allow you to create a bucket, get bucket details, and upload a file.

Note: In this method there have 4 step. But don't worry this method internally handel those step

* [**Step 1: Create a bucket**](../Bucket/Bucket.md#createBucket)
* Step 2: Initiate a direct to s3 multipart upload
* Step 3: Split the file, and upload.
* Step 4: Complete the upload
 


### Example
```php
$path = 'your_path' // path should be storage path
$fileName = "file_name";
(new AutodeskForge\service\UploadFileService())->upload($path, $fileName);

```

### Parameters

| Name          | Type   | Default | Description                 | Notes |
|---------------|--------|---------|-----------------------------|-------|
| **$path**     | string |         | path should be storage path |       |
| **$fileName** | string |         | File name in s3             |       |

### Output ( null )
Return upload file info as like below 

```php
{
  "bucketKey" : "mybucket",
  "objectId" : "urn:adsk.objects:os.object:mybucket/skyscpr1.3ds",
  "objectKey" : "skyscpr1.3ds",
  "sha1" : "e84021849a9f5d1842bf792bbcbc6445c280e15b",
  "size" : 20971520,
  "content-type": "application/octet-stream",
  "location": "https://developer.api.autodesk.com/oss/v2/buckets/mybucket/objects/skyscpr1.3ds"
}
```
