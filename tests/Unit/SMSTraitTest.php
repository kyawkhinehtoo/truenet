<?php

namespace Tests\Unit;

use Tests\TestCase;
use Mockery;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use App\Models\SmsGateway;
use App\Traits\SMSTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
class SMSTraitTest extends TestCase
{
    use SMSTrait;

    protected $mockHttpClient;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Mock SmsGateway
        $this->mock(SmsGateway::class, function ($mock) {
            $mock->shouldReceive('first')->andReturn((object) [
                'status' => '1',
                'sid' => 'test_sid',
                'token' => 'test_token',
                'sender_id' => 'TestSender',
                'api_url' => 'https://api.smsbrix.com/v1/message',
                'single_sms' => 'send',
                'bulk_sms' => 'send_bulk',
            ]);
        });
        
        // Mock HTTP Client
        $this->mockHttpClient = Mockery::mock(Client::class);
        $this->app->instance(Client::class, $this->mockHttpClient);
    }

    public function testSendSingleSMSSuccess()
    {
        // Arrange: Set up response
        $mockResponse = new Response(200, [], json_encode([
            'status' => 'success',
            'messageId' => '12345',
        ]));

        $this->mockHttpClient->shouldReceive('request')
            ->once()
            ->with(
                'POST',
                'https://api.smsbrix.com/v1/message/send',
                Mockery::on(function ($options) {
                    return isset($options['json']) &&
                        $options['json']['senderid'] === 'TestSender' &&
                        $options['json']['number'] === '09420043911' &&
                        $options['json']['message'] === 'Test message' &&
                        isset($options['headers']) &&
                        $options['headers']['Authorization'] === 'Basic ' . base64_encode('test_sid:test_token');
                })
            )
            ->andReturn($mockResponse);

        // Act: Call the sendSMS method
        $response = $this->sendSMS('09420043911', 'Test message', false);

        // Assert: Check the response
        $this->assertIsArray($response);
        $this->assertEquals('success', $response['status']);
        $this->assertEquals('12345', $response['messageId']);
    }

    public function testSendBulkSMSSuccess()
    {
        // Arrange: Set up response
        $mockResponse = new Response(200, [], json_encode([
            'status' => 'success',
        ]));

        $this->mockHttpClient->shouldReceive('request')
            ->once()
            ->with(
                'POST',
                'https://api.smsbrix.com/v1/message/send_bulk',
                Mockery::on(function ($options) {
                    return isset($options['json']['messages'][0]) &&
                        $options['json']['messages'][0]['to'] === ['09123456789', '09234567890'] &&
                        $options['json']['messages'][0]['message'] === 'Bulk test message' &&
                        $options['json']['messages'][0]['from'] === 'TestSender' &&
                        isset($options['headers']) &&
                        $options['headers']['Authorization'] === 'Basic ' . base64_encode('test_sid:test_token');
                })
            )
            ->andReturn($mockResponse);

        // Act: Call the sendSMS method
        $response = $this->sendSMS(['09123456789', '09234567890'], 'Bulk test message', true);

        // Assert: Check the response
        $this->assertIsArray($response);
        $this->assertEquals('success', $response['status']);
    }

    public function testSendSMSFailure()
    {
        // Arrange: Set up response
        $mockResponse = new Response(400, [], json_encode([
            'status' => 'error',
            'error' => 'Invalid phone number',
        ]));

        $this->mockHttpClient->shouldReceive('request')
            ->once()
            ->with(
                'POST',
                'https://api.smsbrix.com/v1/message/send',
                Mockery::on(function ($options) {
                    return isset($options['json']) &&
                        $options['json']['senderid'] === 'TestSender' &&
                        $options['json']['number'] === 'invalid_number' &&
                        $options['json']['message'] === 'Test message' &&
                        isset($options['headers']) &&
                        $options['headers']['Authorization'] === 'Basic ' . base64_encode('test_sid:test_token');
                })
            )
            ->andReturn($mockResponse);

        // Act: Call the sendSMS method
        $response = $this->sendSMS('invalid_number', 'Test message', false);

        // Assert: Check the response
        $this->assertFalse($response);
    }
}