<?php

namespace ViewStatsWrapper;

use ViewStatsWrapper\Exceptions\APIException;
use ViewStatsWrapper\Exceptions\DecryptionException;

class ViewStatsAPI {
    private string $apiToken;
    private string $baseUrl;
    private Logger $logger;

    public function __construct(string $apiToken, string $baseUrl = 'https://api.viewstats.com') {
        $this->apiToken = $apiToken;
        $this->baseUrl = $baseUrl;
        $this->logger = new Logger();
    }

    public function request(string $method, string $endpoint, array $data = []): array {
        try {
            $url = $this->baseUrl . $endpoint;
            $headers = $this->getHeaders();

            if ($method === 'GET' && !empty($data)) {
                $url .= '?' . http_build_query($data);
            }

            $responseData = $this->sendRequest($method, $url, $headers, $data);
            return $this->processResponse($responseData);
        } catch (\Exception $e) {
            $this->logger->error("Error in request: " . $e->getMessage());
            throw new APIException("Failed to make request: " . $e->getMessage());
        }
    }

    public function get(string $endpoint, array $params = []): array {
        return $this->request('GET', $endpoint, $params);
    }

    public function post(string $endpoint, array $data = []): array {
        return $this->request('POST', $endpoint, $data);
    }

    private function getHeaders(): array {
        return [
            'sec-ch-ua: "Chromium";v="128", "Not;A=Brand";v="24", "Google Chrome";v="128"',
            'Referer: https://www.viewstats.com/',
            'sec-ch-ua-mobile: ?0',
            'Authorization: Bearer ' . $this->apiToken,
            'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36',
            'sec-ch-ua-platform: "macOS"',
            'Content-Type: application/json'
        ];
    }

    private function sendRequest(string $method, string $url, array $headers, array $data = []): array {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        if ($method === 'POST' && !empty($data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        
        if (curl_errno($ch)) {
            $this->logger->error('Curl error: ' . curl_error($ch));
        }
        
        curl_close($ch);

        $this->logger->info("API Response - HTTP Code: $httpCode, Content-Type: $contentType");
        $this->logger->debug("Response body: " . substr($response, 0, 1000) . (strlen($response) > 1000 ? '...' : ''));

        if ($httpCode >= 400) {
            throw new APIException("HTTP error! status: " . $httpCode);
        }

        return ['response' => $response, 'contentType' => $contentType];
    }

    private function processResponse(array $responseData): array {
        $this->logger->info("Processing response. Content-Type: " . $responseData['contentType']);
        
        if (strpos($responseData['contentType'], 'application/json') !== false) {
            $decodedData = json_decode($responseData['response'], true);
            $this->logger->debug("JSON decoded data: " . print_r($decodedData, true));
            return $decodedData;
        } else {
            return $this->decryptResponse($responseData['response']);
        }
    }

    private function decryptResponse(string $response): array {
        $n = "Wzk3LCAxMDksIC0xMDAsIC05MCwgMTIyLCAtMTI0LCAxMSwgLTY5LCAtNDIsIDExNSwgLTU4LCAtNjcsIDQzLCAtNzUsIDMxLCA3NF0=";
        $r = "Wy0zLCAtMTEyLCAxNSwgLTEyNCwgLTcxLCAzMywgLTg0LCAxMDksIDU3LCAtMTI3LCAxMDcsIC00NiwgMTIyLCA0OCwgODIsIC0xMjYsIDQ3LCA3NiwgLTEyNywgNjUsIDc1LCAxMTMsIC0xMjEsIDg5LCAtNzEsIDUwLCAtODMsIDg2LCA5MiwgLTQ2LCA0OSwgNTZd";

        $keyArray = json_decode(base64_decode($r), true);
        $ivArray = json_decode(base64_decode($n), true);

        $key = pack('C*', ...$keyArray);
        $iv = pack('C*', ...$ivArray);

        $this->logger->debug("Attempting to decrypt response. Length: " . strlen($response));
        $this->logger->debug("Raw response (hex): " . bin2hex(substr($response, 0, 100)) . "...");

        $ciphertext = substr($response, 0, -16);
        $tag = substr($response, -16);

        $this->logger->debug("Ciphertext length: " . strlen($ciphertext));
        $this->logger->debug("Tag length: " . strlen($tag));

        $decrypted = openssl_decrypt($ciphertext, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);
        
        if ($decrypted === false) {
            $this->logger->error("Decryption failed. OpenSSL error: " . openssl_error_string());
            throw new DecryptionException("Failed to decrypt response");
        }

        $this->logger->debug("Decrypted data: " . substr($decrypted, 0, 1000) . (strlen($decrypted) > 1000 ? '...' : ''));

        $decodedData = json_decode($decrypted, true);
        $this->logger->debug("JSON decoded data after decryption: " . print_r($decodedData, true));

        return $decodedData;
    }
}