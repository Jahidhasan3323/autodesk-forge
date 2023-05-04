# **translate**

Translate a source file from one format to another. Derivatives are stored in a manifest that is updated each time this endpoint is used on a source file. Note that this endpoint is asynchronous and initiates a process that runs in the background, rather than keeping an open HTTP connection until completion. Use the [**getManifest**](#getmanifest) to poll for the job’s completion.


### Example
```php
$objectId= "urn:adsk.objects:os.object:mybucket/skyscpr1.3ds";
$encodedUrn = (new AutodeskForge\service\AutodeskForgeService)->encodeUrn($objectId)
(new AutodeskForge\service\AutodeskForgeService)->translateFile($encodedUrn, $fileName, $type = 'svf2', $views = ["2d", "3d"], $compressedUrn = false, $region = 'us')

```

### Parameters
| Name               | Type                                    | Description                                                                                                                                                                                                                                                                                     | Notes    |
|--------------------|-----------------------------------------|-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|----------|
| **$encodedUrn**    | string                                  | You find the `objectId` in upload file response, this is urn. You have to `encode` this urn as base64. You can also use [**encodeUrn**](#encodeUrn) for encode the `urn`                                                                                                                        |          |
| **$fileName**      | string                                  | The name of the top-level design file in the compressed file. Mandatory if the `compressedUrn` is set to `true`.                                                                                                                                                                                |          |
| **$type**          | string <br/>(default value `svf2`)      | The requested output types. Possible values: `dwg`, `fbx`, `ifc`, `iges`, `obj`, `step`, `stl`, `svf`, `svf2`, `thumbnail`. For a list of supported types, call the [**GET formats**](https://aps.autodesk.com/en/docs/model-derivative/v2/reference/http/informational/formats-GET/) endpoint. | optional |
| **$views**         | array<br/> default value `["2d", "3d"]` | You can translate file for `2d` views only, as well `3d` views only also you can translate both `2d & 3d` .                                                                                                                                                                                     | optional |
| **$compressedUrn** | bool<br/> default value `false`         | Set this to `true` if the source design is compressed as a zip file. The design can consist of a single file or as in the case of Autodesk Inventor, multiple files. If set to `true`, you must specify the `filename` attribute.                                                               | optional |
| **$region**        | string <br/> default value `us`         | Region in which to store outputs. Possible values: `US`, `EMEA`. By default, it is set to `US`.                                                                                                                                                                                                 | optional |

### Output (object)

```
{
  "result": "success",
  "urn": "dXJuOmFkc2sub2JqZWN0czpvcy5vYmplY3Q6bW9kZWxkZXJpdmF0aXZlL0E1LnppcA",
  "acceptedJobs": {
    "output": {
      "destination": {
        "region": "us"
      },
      "formats": [
        {
          "type": "svf2",
          "views": [
            "2d",
            "3d"
          ]
        }
      ]
    }
  }
}
```


# **encodeUrn**
This method encode the provided string in base64 


### Example
```php
$urn = "urn:adsk.objects:os.object:mybucket/skyscpr1.3ds";
(new AutodeskForge\service\AutodeskForgeService)->encodeUrn($urn)

```

### Parameters
| Name     | Type   | Description        | Notes |
|----------|--------|--------------------|-------|
| **$urn** | string | encode into base64 |       |


### Output (string)

```
dXJuOmFkc2sub2JqZWN0czpvcy5vYmplY3Q6d2lwLmRtLnByb2QvNTI5ODBkM2ItZjBlZi00ZGU1LTkwMmQtNjZkNDU2OTViNGVmLnJ2dA
```

# **getManifest**
### Check translate status
Retrieves the manifest for the source design specified by the `urn` URI parameter. The manifest is a list containing information about the derivatives generated while translating a source file. The manifest contains information such as the URNs of the derivatives, the translation status of each derivative, and much more.

The URNs of the derivatives are used to download the generated derivatives by calling the GET ``/{urn}/manifest/{derivativeurn}`` endpoint.

Note: You cannot download 3D SVF2 derivatives.

The statuses are used to check whether the translation of the requested output files is complete. The output files produced by a translation job may complete at different times. Therefore, each output file can have a different `status`.

The first time you translate a source design, the Model Derivative service creates a manifest for that source design. Thereafter, every time you translate that source design, the Model Derivative service updates that manifest. It does not create a new manifest each time you initiate a translation job, even if you are translating to a different format.


### Example
```php
$objectId= "urn:adsk.objects:os.object:mybucket/skyscpr1.3ds";
$encodedUrn = (new AutodeskForge\service\AutodeskForgeService)->encodeUrn($objectId)
(new AutodeskForge\service\AutodeskForgeService)->getManifest($encodedUrn)

```

### Parameters

 | Name            | Type   | Description                                                                                                                                                              | Notes |
|-----------------|--------|--------------------------------------------------------------------------------------------------------------------------------------------------------------------------|-------|
| **$encodedUrn** | string | You find the `objectId` in upload file response, this is urn. You have to `encode` this urn as base64. You can also use [**encodeUrn**](#encodeUrn) for encode the `urn` |       |

### Output (String)

```
{
  "type": "manifest",
  "hasThumbnail": "false",
  "status": "pending",
  "progress": "0% complete",
  "region": "US",
  "urn": "dXJuOmFkc2sub2JqZWN0czpvcy5vYmplY3Q6bW9kZWxkZXJpdmF0aXZlL0E1LnppcA",
  "derivatives": [
  ]
}
```

# **deleteManifest**
### Delete translated file

Deletes the manifest and all its translated output files (derivatives). However, it does not delete the design source file.

### Example
```php
$objectId= "urn:adsk.objects:os.object:mybucket/skyscpr1.3ds";
$encodedUrn = (new AutodeskForge\service\AutodeskForgeService)->encodeUrn($objectId)
(new AutodeskForge\service\AutodeskForgeService)->deleteManifest($encodedUrn)

```

### Parameters

| Name            | Type   | Description                                                                                                                                                              | Notes |
|-----------------|--------|--------------------------------------------------------------------------------------------------------------------------------------------------------------------------|-------|
| **$encodedUrn** | string | You find the `objectId` in upload file response, this is urn. You have to `encode` this urn as base64. You can also use [**encodeUrn**](#encodeUrn) for encode the `urn` |       |

### Output (object)
If success the response as below
```
{
  "result":"success"
}
```
