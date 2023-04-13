# **getToken**

Get an authentication token.

If you set the token then you can also access to token as like this ```Session::get('autodeskForgeToken')```. But we recommend to use ```(new AutodeskForge\service\AutodeskForgeService)->getToken(true)``` this process;

### Example
```php
(new AutodeskForge\service\AutodeskForgeService)->getToken(true)

```

### Parameters

 Name              | Type    | Description  | Notes
|-------------------|---------| ------------- | -------------
 **isSetSession** | boolean | If you send ``true`` then it save the token in session. If you do not send any parameter or send ``false`` parameter then it not store the token in session. | optional

### Output (String)
Token
```
eyJhbGciOiJSUzI1NiIsImtpZCI6IlU3c0dGRldUTzlBekNhSzBqZURRM2dQZXBURVdWN2VhIiwicGkuYXRtIjoiYXNzYyJ9.eyJzY29wZSI6WyJidWNrZXQ6cmVhZCIsImJ1Y2tldDpjcmVhdGUiLCJidWNrZXQ6ZGVsZXRlIiwiZGF0YTpjcmVhdGUiLCJhY2NvdW50OnJlYWQiLCJhY2NvdW50OndyaXRlIiwiZGF0YTp3cml0ZSIsImRhdGE6cmVhZCIsImNvZGU6YWxsIl0sImNsaWVudF9pZCI6IkY2RG44eXBlbTFqOFA2c1V6OElYM3BtU3NPQTk5R1VUIiwiaXNzIjoiaHR0cHM6Ly9kZXZlbG9wZXIuYXBpLmF1dG9kZXNrLmNvbSIsImF1ZCI6Imh0dHBzOi8vYXV0b2Rlc2suY29tIiwianRpIjoiWEdMODdJT1pldEE1QjhBY3JYQWhEYVBoWEdtN2FHd2FtOEJxSWRNSTEydXg2Yko0VDN4emU4aDRodWJFcE1vUyIsImV4cCI6MTY4MTM2Nzg0Nn0.Vozaz5P0GJftJJOz8Y-7UZl2IX4YVNcJzxqY8EYU-oS-c-3EaUB2jTjvX7pOA8gFrclxjjbZ5iAQI0GEr0L0MXF05PDXu-ygaXvDoXBQKlfH15k-obP7kD-7MfBHPHimsPTm-Bl0g-m6PEru9zOSKWKn0PNQ5rsPKWI0E-D4eecKNFkyYE_beSAq8XOkrnztSy46zmL8hG0oLzjbFEjBgkbtHW7mgHLlBKRPqftwG38R8p34qHDdCub26BL5UQnqT9l-xA_eE4z-9gQGtWcsHy3PZIgeTmrUFMnP94BSdrrfv5xw2yZANk0A7cEBKNlnCQPI6ulRsz8MOpI8pbR0rQ
```
