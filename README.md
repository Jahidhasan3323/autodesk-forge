# Forge PHP SDK

*Forge API*:
[![oAuth2](https://img.shields.io/badge/oAuth2-v1-green.svg)](http://autodesk-forge.github.io/)
[![Data-Management](https://img.shields.io/badge/Data%20Management-v1-green.svg)](http://autodesk-forge.github.io/)
[![OSS](https://img.shields.io/badge/OSS-v2-green.svg)](http://autodesk-forge.github.io/)
[![Model-Derivative](https://img.shields.io/badge/Model%20Derivative-v2-green.svg)](http://autodesk-forge.github.io/)

## Overview
This [PHP](http://php.net/) SDK enables you to easily integrate the Forge REST APIs
into your application, including [OAuth](https://developer.autodesk.com/en/docs/oauth/v2/overview/),
[Data Management](https://developer.autodesk.com/en/docs/data/v2/overview/),
[Model Derivative](https://developer.autodesk.com/en/docs/model-derivative/v2/overview/),
and [Design Automation](https://developer.autodesk.com/en/docs/design-automation/v2/overview/).

### Requirements
* PHP version 8.1 and above.
* A registered app on the [Forge Developer portal](https://developer.autodesk.com/myapps).

### Installation
#### Composer

To install the bindings via [Composer](http://getcomposer.org/), run:
```
composer require jahid/autodesk-forge
```
#### Vendor publish
Run command 
```php
php artisan vendor:publish
```
Then publish the `AutodeskForgeService` service.

#### Add those variable in .env 
```dotenv
AUTODESK_FORGE_CLIENT_ID=
AUTODESK_FORGE_CLIENT_SECRET=
AUTODESK_PROJECT=        #need only for Autodesk 360
AUTODESK_SCOPE=           #i.e. "bucket:read,bucket:create,bucket:delete,data:create,account:read,account:write,data:write,data:read,code:all"
```
## Tutorial
Follow this tutorial to see a step-by-step authentication guide, and examples of how to use the Forge APIs.

### Create an App
Create an app on the [Forge Developer portal](https://developer.autodesk.com/myapps). Note the client ID and client secret.

### Authentication
This SDK comes with an [OAuth 2.0](https://developer.autodesk.com/en/docs/oauth/v2/overview/) client that allows you to
retrieve 2-legged tokens. The tutorial uses 2-legged tokens for calling different Data Management endpoints.

#### 2-Legged Token

This type of token is given directly to the application.

To get a 2-legged token run the following code. There have an optional parameter (bool $isSetSession) also. If you send ``true`` then it save the token in session. If you do not send any parameter or send ``false`` parameter then it not store the token in session.

```php 
(new AutodeskForge\service\AutodeskForgeService)->getToken()
```

``Note:`` If you set the token then you can also access to token as like this ```Session::get('autodeskForgeToken')```. But we recommend to use ```(new AutodeskForge\service\AutodeskForgeService)->getToken(true)``` this process;


## API Documentation

You can get the full documentation for the API on the [Developer Portal](https://developer.autodesk.com/)

### Documentation for API Endpoints

All URIs are relative to https://developer.api.autodesk.com. For example, the *createActivity* URI is 'https://developer.api.autodesk.com/autocad.io/us-east/v2/Activities'.


| Method                                                                                      | Parameter                                                                                                                                                                                                                       | Description                                                                                                                                                                                                            |
|---------------------------------------------------------------------------------------------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| [**getToken**](docs/Authentication/Token.md#getToken)                                       | ``optional`` $isSetSession => bool                                                                                                                                                                                              | Get authentication token. If you ``send`` $isSetSession parameter value ``true``, then it store the token in session. So application not sent api request for every time for token.                                    |
| [**createBucket**](docs/Bucket/Bucket.md#createBucket)                                      | ``optional`` $policyKey => string (persistent, transient, temporary)<br/><br/> ``optional`` $bucketKey => string                                                                                                                | Create a bucket                                                                                                                                                                                                        |
| [**getBuckets**](docs/Bucket/Bucket.md#getBuckets)                                          |                                                                                                                                                                                                                                 | get bucket list                                                                                                                                                                                                        |
| [**getBucketDetails**](docs/Bucket/Bucket.md#getBucketDetails)                              | bucketKey                                                                                                                                                                                                                       | get bucket details                                                                                                                                                                                                     |
| [**deleteBucket**](docs/Bucket/Bucket.md#deleteBucket)                                      | bucketKey                                                                                                                                                                                                                       | delete bucket details                                                                                                                                                                                                  |
| [**upload**](docs/S3File/S3File.md#upload)                                                  | path=> string, fileName => string                                                                                                                                                                                               | upload file in s3 bucket. here `path` is file source path and `fileName` is which name is use in s3                                                                                                                    |
| [**translate**](docs/Manifest/Manifest.md#translate)                                        | urn => string, fileName => string, <br/>type => string (default value  `svf2`), <br/> views => array (default value `["2d", "3d"]`), <br/> compressedUrn => bool (default value `false`), region => string (default value `us`) | You find the `objectId` in upload file response, this is urn. You can translate file for `2d` views only, as well `3d` views only also you can translate both `2d & 3d` [**...**](docs/Manifest/Manifest.md#translate) |
| [**encodeUrn**](docs/Manifest/Manifest.md#encodeUrn)                                        | $urn=> string                                                                                                                                                                                                                   | this method is used for encode `urn`                                                                                                                                                                                   |
| [**getManifest**](docs/Manifest/Manifest.md#getManifest)  (check translate status)          | $encodedUrn=> string (`objectId` is urn, you must encode the urn)                                                                                                                                                               | this method is used for check translate status                                                                                                                                                                         |
| [**deleteManifest**](docs/Manifest/Manifest.md#deleteManifest)  (delete translated file)    | $encodedUrn=> string (`objectId` is urn, you must encode the urn)                                                                                                                                                               | this method is used for delete translated file                                                                                                                                                                         |
| [**checkTranslateStatus**](docs/Manifest/Manifest.md#getManifest)  (check translate status) | $encodedUrn=> string (`objectId` is urn, you must encode the urn)                                                                                                                                                               | this method is used for check translate status                                                                                                                                                                         |
## Future Feature
* Upload file in Autodesk 360<sup>0</sup>

## Support

[Please open an issue on GitHub](https://github.com/Jahidhasan3323/autodesk-forge)


## License

Create CRUD operation for Laravel application under the [MIT license](https://opensource.org/licenses/MIT).
