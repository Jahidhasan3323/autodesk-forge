<?php

namespace AutodeskForge\service;

use Exception;
use Illuminate\Support\Facades\Http;

class UploadFileService extends AutodeskForgeService
{
    /**
     * @throws Exception
     */
    public function upload($path, $fileName): array|object
    {
        //Step 1: create bucket
        $bucketKey = $this->createBucket();

        //Step 2: Initiate a direct to s3 multipart upload
        list($uploadKey, $signedUrl) = $this->s3MultipartUpload($bucketKey, $fileName);

        //Step 3: Split the file, and upload
        $this->splitAndUpload($path, $signedUrl);

        // Step 4: Step 4: Complete the upload
        return $this->completeUpload($bucketKey, $fileName, $uploadKey);
    }

    /**
     * @param $bucketKey
     * @param $fileName
     * @return array
     * @throws Exception
     */
    public function s3MultipartUpload($bucketKey, $fileName): array
    {
        //Step 2: Initiate a direct to s3 multipart upload
        $s3UploadUrlResponse = Http::withToken($this->getToken(true))->get("{$this->apiBaseUrl}/oss/v2/buckets/{$bucketKey}/objects/{$fileName}/signeds3upload?part=1");

        if ($s3UploadUrlResponse->status() == 200) {
            $s3UploadUrlResponseDecode = $s3UploadUrlResponse->object();
            $uploadKey = $s3UploadUrlResponseDecode->uploadKey;
            $signedUrl = $s3UploadUrlResponseDecode->urls[0];
        } else {
            throw new Exception ('direct to s3 multipart upload failed ' . $s3UploadUrlResponse->body());
        }

        return array($uploadKey, $signedUrl);
    }


    /**
     * @param $path
     * @param $signedUrl
     * @return void
     * @throws Exception
     */
    public function splitAndUpload($path, $signedUrl): void
    {
        //Step 3: Split the file, and upload
        $file_path = storage_path($path);
        $fileSize = filesize($file_path);

        $handle = fopen($file_path, "r");
        $POST_DATA = fread($handle, filesize($file_path));

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $signedUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_POSTFIELDS => $POST_DATA,
        ));

        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/octet-stream',
            "Content-Length: {$fileSize}"
        ]);

        curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            throw new Exception ($err);
        }
    }


    /**
     * @param $bucketKey
     * @param $fileName
     * @param $uploadKey
     * @return array|object
     * @throws Exception
     */
    public function completeUpload($bucketKey, $fileName, $uploadKey): array|object
    {
        // Step 4: Step 4: Complete the upload
        $completeUpload = Http::withToken($this->getToken(true))->post("{$this->apiBaseUrl}/oss/v2/buckets/{$bucketKey}/objects/{$fileName}/signeds3upload", [
            'uploadKey' => $uploadKey
        ]);

        if ($completeUpload->status() == 200) {
            $complete_upload_response_decoded = $completeUpload->object();
            if ($complete_upload_response_decoded->bucketKey) {
                return $complete_upload_response_decoded;
            }
            throw new Exception ('bucket key is missing');
        } else {
            throw new Exception ('file upload complete failed ' . $completeUpload->body());
        }
    }


}
