# **createBucket**

Creates a bucket. Buckets are arbitrary spaces that are created by applications and are used to store objects for later retrieval. A bucket is owned by the application that creates it. 

When creating buckets, it is required that applications set a retention policy for objects stored in the bucket. This cannot be changed at a later time. The retention policy on the bucket applies to all objects stored within. When creating a bucket, specifically set the policyKey to transient, temporary, or persistent

### Transient
Think of this type of storage as a cache. Use it for ephemeral results. For example, you might use this for objects that are part of producing other persistent artifacts, but otherwise are not required to be available later.

Objects older than 24 hours are removed automatically. Each upload of an object is considered unique, so, for example, if the same rendering is uploaded multiple times, each of them will have its own retention period of 24 hours.

### Temporary
This type of storage is suitable for artifacts produced for user-uploaded content where after some period of activity, the user may rarely access the artifacts.

When an object has reached 30 days of age, it is deleted.

### Persistent
Persistent storage is intended for user data. When a file is uploaded, the owner should expect this item to be available for as long as the owner account is active, or until he or she deletes the item.

### Example
```php
(new AutodeskForge\service\BucketService())->createBucket('persistent', 'model123');

```

### Parameters

 Name              | Type                       | Default | Description                                                                                                                                                                                                                                                                                                                                                                                             | Notes
|-------------------|----------------------------|-------------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------| -------------
 **policyKey** | string | persistent | The retention policy on the bucket applies to all objects stored within. When creating a bucket, specifically set the policyKey to transient (Objects older than 24 hours are removed automatically), temporary (When an object has reached 30 days of age, it is deleted.), or persistent (This item to be available for as long as the owner account is active, or until he or she deletes the item.) | optional
**bucketKey** | string | null | If you send any bucket key then it will create a bucket use this name otherwise system generate a bucket name (name prefix will be model and add a random number) | optional

### Output (String)
Bucket name
```
model1681367666
```

# **getBuckets**

This method will return the buckets list owned by the application

### Example
```php
(new AutodeskForge\service\BucketService())->getBuckets();

```

### Parameters

 Name              | Type                       | Default | Description                                                                                                                                                                                                                                                                                                                                                                                             | Notes
|-------------------|----------------------------|-------------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------| -------------


### Output (array | object | null)
Bucket list
```
{
    "items": [
        {
            "bucketKey": "model1681367666",
            "createdDate": 1681367667667,
            "policyKey": "persistent"
        }
    ]
}
```

# **getBucketDetails**

Return bucket details in JSON format if the caller is the owner of the bucket. A request by any other application will result in a response of 403 Forbidden.


### Example
```php
(new AutodeskForge\service\BucketService())->getBucketDetails();

```

### Parameters

 Name              | Type                       | Default | Description    | Notes
|-------------------|----------------------------|-------------|----------------| -------------
 **bucketKey** | string |  |  | 

### Output ( object | null )
Bucket details
```
{
    "bucketKey": "model1681367666",
    "bucketOwner": "F6Dn8ypem1j8P6sUz8IX3pmSsOA99GUT",
    "createdDate": 1681367667667,
    "permissions": [
        {
            "authId": "F6Dn8ypem1j8P6sUz8IX3pmSsOA99GUT",
            "access": "full"
        }
    ],
    "policyKey": "persistent"
}
```

# **deleteBucket**

Deletes a bucket. The bucket must be owned by the application.

We recommend only deleting small buckets used for acceptance testing or prototyping, since it can take a long time for a bucket to be deleted.

Note that the bucket name will not be immediately available for reuse.

### Example
```php
(new AutodeskForge\service\BucketService())->deleteBucket();

```

### Parameters

 Name              | Type                       | Default | Description                          | Notes
|-------------------|----------------------------|-------------|--------------------------------------| -------------
 **bucketKey** | string |  | delete the given buckut key's bucket |

### Output ( null )
If process is completed successfully then return null

