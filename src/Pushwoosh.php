<?php

namespace NotificationChannels\Pushwoosh;

use GuzzleHttp\ClientInterface;
use function GuzzleHttp\json_decode;
use GuzzleHttp\Psr7\Request;
use NotificationChannels\Pushwoosh\Concerns\DetectsPushwooshErrors;
use NotificationChannels\Pushwoosh\Exceptions\PushwooshException;
use NotificationChannels\Pushwoosh\Exceptions\UnknownDeviceException;
use Throwable;

class Pushwoosh
{
    use DetectsPushwooshErrors;

    protected $application;
    protected $client;
    protected $token;

    /**
     * Create a new Pushwoosh API client.
     *
     * @param \GuzzleHttp\ClientInterface $client
     * @param string $application
     * @param string $token
     * @return void
     */
    public function __construct(ClientInterface $client, string $application, string $token)
    {
        $this->application = $application;
        $this->client = $client;
        $this->token = $token;
    }

    /**
     * Create the given message in the Pushwoosh API.
     *
     * @param \NotificationChannels\Pushwoosh\PushwooshPendingMessage $message
     * @return string[]
     */
    public function createMessage(PushwooshPendingMessage $message)
    {
        $headers = ['Accept' => 'application/json', 'Content-Type' => 'application/json'];
        $payload = \GuzzleHttp\json_encode(['request' => $message]);
        $request = new Request('POST', 'https://cp.pushwoosh.com/json/1.3/createMessage', $headers, $payload);

        try {
            $response = $this->client->send($request);
        } catch (Throwable $e) {
            $response = $this->tryAgainIfCausedByPushwooshServerError($request, $e);
        }

        $response = json_decode($response->getBody()->getContents());

        if (isset($response->status_code) && $response->status_code !== 200) {
            throw new PushwooshException($response->status_message);
        }

        if (isset($response->response->UnknownDevices)) {
            throw new UnknownDeviceException($response->response->UnknownDevices);
        }

        $message->wasSent();

        if (isset($response->response->Messages)) {
            # Pushwoosh will not assign IDs to messages sent to less than 10 unique devices
            return array_map(function (string $identifier) {
                return $identifier !== 'CODE_NOT_AVAILABLE' ? $identifier : null;
            }, $response->response->Messages);
        }

        return [];
    }

    /**
     * Get the Pushwoosh API token.
     *
     * @return string
     */
    public function getApiToken()
    {
        return $this->token;
    }

    /**
     * Get the Pushwoosh application code.
     *
     * @return string
     */
    public function getApplicationCode()
    {
        return $this->application;
    }

    /**
     * Send the message.
     *
     * @param \NotificationChannels\Pushwoosh\PushwooshMessage $message
     * @return \NotificationChannels\Pushwoosh\PushwooshPendingMessage
     */
    public function send(PushwooshMessage $message)
    {
        return (new PushwooshPendingMessage($this))->queue($message);
    }

    /**
     * Handle a Pushwoosh communication error.
     *
     * @param \GuzzleHttp\Psr7\Request $request
     * @param \Throwable $e
     * @return \Psr\Http\Message\ResponseInterface
     */
    protected function tryAgainIfCausedByPushwooshServerError(Request $request, Throwable $e)
    {
        if ($this->causedByPushwooshServerError($e)) {
            try {
                return $this->client->send($request);
            } catch (Throwable $e) {
                // Do nothing...
            }
        }

        throw new PushwooshException('Failed to create message(s)', 0, $e);
    }
}
