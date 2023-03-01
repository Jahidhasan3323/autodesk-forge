<?php

namespace AutodeskForge\service;

use Exception;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class UploadFileUsingBim360Service extends AutodeskForgeService
{
    /**
     * @param $path
     * @param $fileName
     * @return array|object
     * @throws Exception
     */
    public function uploadS3Bim360($path, $fileName): object|array
    {
        try {
            // Step 1: Find the hub that has your resource
            $hub_id = $this->getHubId();

            // Step 2: Find the project that has your resource
            $project_filter_info = $this->getProjectFilterInfo($hub_id);
            $project_id = $project_filter_info->id;
            $folder_id = $this->getTopFolder($hub_id, $project_id);

            //Step 3: Create a storage location
            $storage_location_id = $this->getStorageLocationId($project_id, $folder_id, $fileName);
            $storage_location_id_without_urn = str_replace('urn:adsk.objects:os.object:', '', $storage_location_id);
            $storage_location_id_array = explode('/', $storage_location_id_without_urn);

            // Step 4: Generate a signed S3 url
            $signed_s3_upload_response_decoded = $this->getSignedS3UploadResponse($storage_location_id_array);

            // Step 5: upload file to s3
            $this->uploadFileS3($path, $signed_s3_upload_response_decoded);

            // Step 6: complete upload file to s3
            $complete_upload_response = $this->completeUploadS3($storage_location_id_array, $signed_s3_upload_response_decoded);
            if ($complete_upload_response->status() == '200') {
                $complete_upload_response_decoded = $complete_upload_response->object();
                if ($complete_upload_response_decoded->bucketKey) {
                    return $complete_upload_response_decoded;
                }
                throw new Exception ('bucket key missing');
            } else {
                throw new Exception ('upload process failed');
            }
        }catch (Exception $e){
            throw new Exception ($e->getMessage());
        }
    }

    /**
     * @return mixed
     * @throws Exception
     */
    private function getHubId(): mixed
    {
        $hub_response = Http::withToken($this->getToken(true))->get("{$this->apiBaseUrl}/project/v1/hubs");
        if ($hub_response->status() == 200){
            return json_decode($hub_response->body())->data[0]->id;
        } else{
            throw new Exception ('get hub id failed '. $hub_response->body());
        }
    }


    /**
     * @param $hub_id
     * @return mixed
     * @throws Exception
     */
    private function getProjectFilterInfo($hub_id): mixed
    {
        $project_response = Http::withToken($this->getToken(true))->get("{$this->apiBaseUrl}/project/v1/hubs/{$hub_id}/projects");
        if ($project_response->status() == 200){
            $project_response_collection = collect($project_response->object()->data);
            $project_name =  config('autodeskForge.project');
            return $project_response_collection->firstWhere('attributes.name', $project_name);
        } else{
            throw new Exception ('get project filter info failed '. $project_response->body());
        }
    }

    /**
     * @param $hub_id
     * @param $project_id
     * @return mixed
     * @throws Exception
     */
    private function getTopFolder($hub_id, $project_id): mixed
    {
        $top_folder_response = Http::withToken($this->getToken(true))->get("{$this->apiBaseUrl}/project/v1/hubs/{$hub_id}/projects/{$project_id}/topFolders");

        if ($top_folder_response->status() == 200){
            $top_folder_response_collection = collect($top_folder_response->object()->data);
            $top_folder_response_collection = $top_folder_response_collection->firstWhere('attributes.name', "Project Files");
            return $top_folder_response_collection->id;
        } else{
            throw new Exception ('get top folders failed '.$top_folder_response->body());
        }
    }

    /**
     * @param $project_id
     * @param $folder_id
     * @param $fileName
     * @return mixed
     * @throws Exception
     */
    private function getStorageLocationId($project_id, $folder_id, $fileName): mixed
    {
        $storage_location_response = Http::withToken($this->getToken(true))->post("{$this->apiBaseUrl}/data/v1/projects/{$project_id}/storage",[
            "jsonapi" => [
                "version" => "1.0"
            ],
            "data" => [
                "type" => "objects",
                "attributes" => [
                    "name" => $fileName
                ],
                "relationships" => [
                    "target" => [
                        "data" => [
                            "type" => "folders",
                            "id" => $folder_id
                        ]
                    ]
                ]
            ]
        ]);

        if ($storage_location_response->status() == 201){
            return json_decode($storage_location_response->body())->data->id;
        } else{
            throw new Exception ('get storage location id failed '. $storage_location_response->body());
        }
    }

    /**
     * @param array $storage_location_id_array
     * @return mixed
     * @throws Exception
     */
    private function getSignedS3UploadResponse(array $storage_location_id_array): mixed
    {
        $signed_s3_upload_response = Http::withToken($this->getToken(true))->get("{$this->apiBaseUrl}/oss/v2/buckets/{$storage_location_id_array[0]}/objects/{$storage_location_id_array[1]}/signeds3upload");

        if ($signed_s3_upload_response->status() == 200){
            return json_decode($signed_s3_upload_response->body());
        } else{
            throw new Exception ('get signed s3 upload url failed '. $signed_s3_upload_response->body());
        }
    }

    /**
     * @param $path
     * @param $signed_s3_upload_response_decoded
     * @return void
     * @throws Exception
     */
    private function uploadFileS3($path, $signed_s3_upload_response_decoded): void
    {
        $file_path = storage_path($path);
        $handle = fopen($file_path, "r");
        $POST_DATA = fread($handle, filesize($file_path));

        $url=$signed_s3_upload_response_decoded->urls[0];
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_POSTFIELDS=>$POST_DATA ,

        ));
        curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err){
            throw new Exception ($err);
        }
    }

    /**
     * @param $storage_location_id_array
     * @param $signed_s3_upload_response_decoded
     * @return PromiseInterface|Response
     * @throws Exception
     */
    private function completeUploadS3($storage_location_id_array, $signed_s3_upload_response_decoded): PromiseInterface|Response
    {
        $response = Http::withToken($this->getToken(true))->post("{$this->apiBaseUrl}/oss/v2/buckets/{$storage_location_id_array[0]}/objects/{$storage_location_id_array[1]}/signeds3upload", [
            "uploadKey"=>$signed_s3_upload_response_decoded->uploadKey
        ]);
        if ($response->status() == 200){
            return $response;
        } else{
            throw new Exception ('file upload complete failed '.$response->body());
        }
    }
}
