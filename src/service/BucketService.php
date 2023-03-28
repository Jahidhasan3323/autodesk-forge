<?php

namespace AutodeskForge\service;

use Exception;
use Illuminate\Support\Facades\Http;

class BucketService extends AutodeskForgeService
{
    /**
     * @param string $policyKey
     * @param string|null $bucketKey
     * @return mixed
     * @throws Exception
     */
    public function createBucket(string $policyKey = 'persistent', string $bucketKey = null): mixed
    {
        $bucket_response = Http::withToken($this->getToken(true))->post("{$this->apiBaseUrl}/oss/v2/buckets", [
            "bucketKey" => $bucketKey ?? $this->generateBucketKey(),
            "policyKey" => $policyKey
        ]);

        if ($bucket_response->status() == 200) {
            return $bucket_response->object()->bucketKey;
        } else {
            throw new Exception ('get bucket failed ' . $bucket_response->body());
        }
    }


    /**
     * @return array|object|null
     * @throws Exception
     */
    public function getBuckets(): array|object|null
    {
        $bucket_response = Http::withToken($this->getToken(true))->get("{$this->apiBaseUrl}/oss/v2/buckets");

        if ($bucket_response->status() == 200) {
            return $bucket_response->object();
        } else {
            throw new Exception ('get bucket failed ' . $bucket_response->body());
        }
    }

    /**
     * @param string $bucketKey
     * @return array|object|null
     * @throws Exception
     */
    public function getBucketDetails(string $bucketKey): array|object|null
    {
        $bucket_response = Http::withToken($this->getToken(true))->get("{$this->apiBaseUrl}/oss/v2/buckets/{$bucketKey}/details");

        if ($bucket_response->status() == 200) {
            return $bucket_response->object();
        } else {
            throw new Exception ('get bucket failed ' . $bucket_response->body());
        }
    }

    /**
     * @param string $bucketKey
     * @return object|array|null
     * @throws Exception
     */
    public function deleteBucket(string $bucketKey): object|array|null
    {
        $bucket_response = Http::withToken($this->getToken(true))->delete("{$this->apiBaseUrl}/oss/v2/buckets/{$bucketKey}");

        if ($bucket_response->status() == 200) {
            return $bucket_response->object();
        } else {
            throw new Exception ('get bucket failed ' . $bucket_response->body());
        }
    }


    /**
     * @return string
     */
    public function generateBucketKey(): string
    {
        return 'model' . time();
    }


}
