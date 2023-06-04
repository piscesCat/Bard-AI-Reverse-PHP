<?php
/**
 * Reverse Google Bard AI Library
 *
 * Author: Khai Phan
 *
 * Github: https://github.com/piscesCat/Bard-AI-Reverse-PHP
 *
 * This class provides a convenient interface to interact with the Google Bard AI service.
 * It allows you to query the AI for answers based on input text and retrieve the response.
 */

namespace KhaiPhan\Google;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Exception;

class Bard
{
    private $cookie_value;
    private $proxy;
    private $timeout;
    private $reqid;
    private $conversation_id;
    private $response_id;
    private $choice_id;
    private $session;
    private $SNlM0e;
    private $headers;
    private $req_options;

    /**
     * Constructor for Bard class.
     *
     * @param string $cookie_value
     * @param array $data
     * @param int $timeout
     * @param string|array|null $proxy
     */
    public function __construct(
        $cookie_value,
        $data = [],
        $timeout = 120,
        $proxy = null
    ) {
        $this->cookie_value = $cookie_value;
        $this->proxy = $proxy;
        $this->timeout = $timeout;
        $this->reqid = $this->getReqID();

        // Set request headers
        $this->headers = [
            "X-Same-Domain" => "1",
            "User-Agent" =>
                "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.114 Safari/537.36",
            "Content-Type" => "application/x-www-form-urlencoded;charset=UTF-8",
            "Origin" => "https://bard.google.com",
            "Referer" => "https://bard.google.com/",
            "Cookie" => "__Secure-1PSID=" . $this->cookie_value,
        ];

        // Set request options
        $this->req_options = [
            "timeout" => $this->timeout,
            "proxy" => $this->proxy,
            "headers" => $this->headers,
        ];

        // Create a new HTTP client session
        $this->session = new Client($this->req_options);

        // Set class properties from input data
        $this->conversation_id = $data["conversation_id"] ?? "";
        $this->response_id = $data["response_id"] ?? "";
        $this->choice_id = $data["choice_id"] ?? "";
        $this->SNlM0e = $data["session_id"] ?? $this->getSNlM0e();
    }

    /**
     * Get the answer from Bard.
     *
     * @param string $prompt
     * @return array
     */
    public function getAnswer($prompt)
    {
        // Prepare the input text structure
        $prompt_struct = [
            [$prompt],
            null,
            [$this->conversation_id, $this->response_id, $this->choice_id],
        ];

        // Set the parameters and form data
        $params = [
            "bl" => "boq_assistant-bard-web-server_20230530.14_p0",
            "_reqid" => $this->reqid,
            "rt" => "c",
        ];
        $form_params = [
            "f.req" => json_encode([null, json_encode($prompt_struct)]),
            "at" => $this->SNlM0e,
        ];

        // Perform the POST request to Bard
        $url =
            "https://bard.google.com/_/BardChatUi/data/assistant.lamda.BardFrontendService/StreamGenerate";
        try {
            $resp = $this->session->post($url, [
                "query" => $params,
                "form_params" => $form_params,
            ]);
            if ($resp->getStatusCode() !== 200) {
                throw new Exception(
                    "Response code not 200. Response Status is " .
                        $resp->getStatusCode()
                );
            }
            $bard_resp = $resp->getBody()->getContents();
            $answers = $this->parseAnswers($bard_resp);

            return $answers;
        } catch (RequestException $e) {
            // Handle request exception
            throw new Exception("Request error maybe you have to use proxy: " . $e->getMessage());
        }
    }

    /**
     * Parse the images from the Bard response.
     *
     * @param array $response
     * @return array
     */
    private function parseImages($response)
    {
        $images = [];
        foreach ($response as $item) {
            $images[] = [
                "command" => $item[2],
                "query" => $item[0][4],
                "image_url" => $item[0][0][0],
                "length" => $item[0][2],
                "width" => $item[0][3],
            ];
        }

        return $images;
    }

    /**
     * Parse the answers from the Bard response.
     *
     * @param string $response
     * @return array
     */
    private function parseAnswers($response)
    {
        $resp_dict = json_decode(explode("\n", $response)[3], true)[0][2];
        if (!$resp_dict) {
            return [
                "content" =>
                    "Response Error: " . $response,
            ];
        }
        $parsed_answer = json_decode($resp_dict, true);
        $choices = array_map(function ($item) {
            return [
                "choice_id" => $item[0],
                "content" => $item[1],
                "images" => $this->parseImages($item[4]),
            ];
        }, $parsed_answer[4]);
        $bard_resp = [
            "content" => $choices[0]["content"][0],
            "images" => $choices[0]["images"],
            "choice_id" => $choices[0]["choice_id"],
            "conversation_id" => $parsed_answer[1][0],
            "response_id" => $parsed_answer[1][1],
            "session_id" => $this->SNlM0e,
            "factualityQueries" => $parsed_answer[3],
            "text_query" => $parsed_answer[2][0] ?: "",
            "choices" => $choices,
        ];

        return $bard_resp;
    }

    /**
     * Generate a request ID.
     *
     * @return string
     */
    private function getReqID()
    {
        $req_id = str_pad(rand(111111, 999999), 6, "0", STR_PAD_LEFT);

        return (string) $req_id;
    }

    /**
     * Get the SNlM0e value.
     *
     * @return string
     * @throws Exception
     */
    private function getSNlM0e()
    {
        if (!$this->cookie_value || substr($this->cookie_value, -1) !== ".") {
            throw new Exception(
                "__Secure-1PSID value must end with a single dot. Enter correct __Secure-1PSID value."
            );
        }
        $url = "https://bard.google.com";
        try {
            $resp = $this->session->get($url);
            if ($resp->getStatusCode() !== 200) {
                throw new Exception(
                    "Response code not 200. Response Status is " .
                        $resp->getStatusCode()
                );
            }
            $data = $resp->getBody()->getContents();
            preg_match('/SNlM0e":"(.*?)"/', $data, $matches);
            if (!$matches) {
                throw new Exception(
                    "SNlM0e value not found in response. Check __Secure-1PSID value."
                );
            }

            $session_id = $matches[1];

            return $session_id;
        } catch (RequestException $e) {
            // Handle request exception
            throw new Exception("Request error maybe you have to use proxy: " . $e->getMessage());
        }
    }
}
