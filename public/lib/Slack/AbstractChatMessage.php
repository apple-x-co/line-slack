<?php


namespace Slack;


use Slack\MessageBuilder\MessageBuilderInterface;

abstract class AbstractChatMessage
{

    /**
     * @param array|MessageBuilderInterface $data
     * @param ChatOptions $options
     *
     * @return PostResult
     */
    abstract public function post($data, $options);

    /**
     * @param string $url
     * @param array $data
     * @param string|null $token
     *
     * @return PostResult
     *
     * @see https://api.slack.com/methods/chat.postMessage
     */
    protected function _post($url, $data, $token = null)
    {
        error_log(print_r($data, true));

        $headers = ['Content-Type: application/json; charset=utf-8'];
        if ($token !== null) {
            $headers[] = 'Authorization: Bearer ' . $token;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_VERBOSE, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        $result = curl_exec($ch);
        curl_close($ch);

        return new PostResult($result);
    }
}