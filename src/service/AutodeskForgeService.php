<?php

namespace AutodeskForge\service;

use App\Jobs\AutodeskFileExport;
use Exception;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use RDDM\Art\Stores\Models\BimUploadFiles;


class AutodeskForgeService
{
    public string $apiBaseUrl = 'https://developer.api.autodesk.com';


    /**
     * @param bool $isSetSession
     * @return mixed|null
     * @throws Exception
     */
    public function getToken(bool $isSetSession = false): mixed
    {
        $this->getTokenInfo($isSetSession);
        if (Session::exists('autodeskForgeToken')) {
            $autodeskTokenInfo = Session::get('autodeskForgeToken');
            if (time() + $autodeskTokenInfo['expires_in'] > time() + 30) { // time() + 30 second
                return $autodeskTokenInfo['access_token'];
            } else {
                return $this->getTokenInfo($isSetSession);
            }
        } else {
            return $this->getTokenInfo($isSetSession);
        }

    }


    /**
     * @param bool $isSetSession
     * @return mixed|void
     * @throws Exception
     */
    public function getTokenInfo(bool $isSetSession)
    {
        $clientId = config('autodeskForge.clientId');
        $clientSecret = config('autodeskForge.clientSecret');
        $scope = config('autodeskForge.scope');
        $bucket_response = Http::withHeaders([
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'Authorization' => 'Basic RjZEbjh5cGVtMWo4UDZzVXo4SVgzcG1Tc09BOTlHVVQ6QVNOa3c4S3F6MXQwV1hISw=='
        ])->asForm()->post("{$this->apiBaseUrl}/authentication/v2/token", [
            "grant_type" => "client_credentials",
            "scope" => str_replace(',', ' ', $scope)
        ]);
        if ($bucket_response->status() == 200) {
            if ($isSetSession) {
                Session::put('autodeskForgeToken', $bucket_response->json());
            }
            return $bucket_response->json()['access_token'];
        } else {
            throw new Exception ('token create failed ' . $bucket_response->body());
        }
    }


    /**
     * @param $urn
     * @param $fileName
     * @return array|object
     * @throws Exception
     */
    public function translateFile($urn, $fileName): object|array
    {
        $body = [
            "input" => [
                "urn" => $urn,
                "compressedUrn" => false,
                "rootFilename" => $fileName
            ],
            "output" => [
                "destination" => [
                    "region" => "us"
                ],
                "formats" => [
                    [
                        "type" => "svf2",
                        "views" => [
                            "2d", "3d"
                        ]
                    ]
                ],
            ],
        ];

        $response = Http::withToken($this->getToken(true))->post("{$this->apiBaseUrl}/oss/v2/buckets", $body);

        if ($response->status() == 200) {
            return $response->object();
        } else {
            throw new Exception ('get bucket failed ' . $response->body());
        }
    }


    /**
     * @param $encodedUrn
     * @return array|object|void
     * @throws Exception
     */
    public function checkTranslateStatus($encodedUrn)
    {
        $response = Http::withToken($this->getToken(true))->get("{$this->apiBaseUrl}/modelderivative/v2/designdata/{$encodedUrn}/manifest");
        if ($response) {
            if ($response->status() == '200') {
                return $response->object();
            } else {
                throw new Exception ('The file is not a Revit file or is not a supported version.');
            }
        }
    }

    /**
     * @param $encodedUrn
     * @return array|object|null
     * @throws Exception
     */
    public function getManifest($encodedUrn): object|array|null
    {
        return $this->checkTranslateStatus($encodedUrn);
    }

    /**
     * @param $encodedUrn
     * @return array|object|void
     * @throws Exception
     */
    public function deleteManifest($encodedUrn)
    {
        $response = Http::withToken($this->getToken(true))->delete("{$this->apiBaseUrl}/modelderivative/v2/designdata/{$encodedUrn}/manifest");
        if ($response) {
            if ($response->status() == '200') {
                return $response->object();
            } else {
                throw new Exception ('The file is not a Revit file or is not a supported version.');
            }
        }
    }

}
