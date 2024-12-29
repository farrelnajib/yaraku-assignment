<?php
namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use PhpMqtt\Client\ConnectionSettings;
use PhpMqtt\Client\Exceptions\ConfigurationInvalidException;
use PhpMqtt\Client\Exceptions\ConnectingToBrokerFailedException;
use PhpMqtt\Client\Exceptions\DataTransferException;
use PhpMqtt\Client\Exceptions\MqttClientException;
use PhpMqtt\Client\Exceptions\ProtocolNotSupportedException;
use PhpMqtt\Client\Exceptions\RepositoryException;
use PhpMqtt\Client\MqttClient;

class MqttService
{
    protected MqttClient $client;

    /**
     * Construct MQTT client using provided configuration
     *
     * @throws ProtocolNotSupportedException
     */
    public function __construct()
    {
        $host = config('mqtt.host');
        $port = (int) config('mqtt.port');
        $username = config('mqtt.username');
        $password = config('mqtt.password');
        $clientId = config('mqtt.client_id');

        $this->client = new MqttClient($host, $port, $clientId, MqttClient::MQTT_3_1);

        $connectionSettings = new ConnectionSettings();

        if ($username !== null) {
            $connectionSettings->setUsername($username);
        }

        if ($password !== null) {
            $connectionSettings->setPassword($password);
        }

        try {
            $this->client->connect($connectionSettings);
        } catch (MqttClientException $e) {
            // Handle exception (e.g., log the error or rethrow it)
            report($e);  // Laravel's built-in logging system
            throw new \RuntimeException("Failed to connect to the MQTT broker: " . $e->getMessage());
        }
    }

    /**
     * Publish a message to a topic.
     *
     * @param string $topic
     * @param object $data
     * @return void
     * @throws RepositoryException|DataTransferException
     */
    public function publish(string $topic, object $data): void
    {
        $message = json_encode($data);
        $this->client->publish($topic, $message);
    }

    /**
     * Disconnect the MQTT client.
     *
     * @return void
     * @throws DataTransferException
     */
    public function disconnect(): void
    {
        $this->client->disconnect();
    }
}
