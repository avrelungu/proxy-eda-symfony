<?php // tests/Controller/WebhooksControllerTest.php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\CDP\Analytics\Model\Subscription\Identify\IdentifyModel;
use App\CDP\Analytics\Model\Subscription\Track\TrackModel;
use App\CDP\Http\CdpClient;
use App\CDP\Http\CdpClientInterface;
use App\Tests\TestDoubles\CDP\Http\FakeCdpClient;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

class WebhookControllerTest extends WebTestCase
{
    private KernelBrowser $webTester;
    private ContainerInterface $container;
    private FakeCdpClient $cdpClient;

    protected function setUp(): void
    {
        $this->webTester = static::createClient();
        $this->container = $this->webTester->getContainer();
        $this->cdpClient = $this->container->get(CdpClientInterface::class);
    }

    public function testWebhooksAreHandled(): void
    {
        /** @phpcs:disable */
        $incomingWebhookPayload = '{"event":"newsletter_subscribed","id":"12345","origin":"www","timestamp":"2024-12-12T12:00:00Z",
        "user": {"client_id":"4a2b342d-6235-46a9-bc95-6e889b8e5de1","email":"email@example.com","region":"EU"},
        "newsletter": {"newsletter_id":"newsletter-001","topic":"N/A","product_id":"TechGadget-3000X"}}';
        /** @phpcs:enable */

        $this->webTester->request(
            method: 'POST',
            uri: '/webhook',
            server: [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT' => '*/*',
            ],
            content: $incomingWebhookPayload
        );

        // Assert CdpClient::identify() called once
        $this->assertSame(1, $this->cdpClient->getIdentifyCallCount());

        // Assert correct IdentifyModel is passed to CdpClient::identify() method
        $identifyModel = $this->cdpClient->getIdentifyModel();
        assert($identifyModel instanceof IdentifyModel);

        // Assert IdentifyModel::toArray() organizes data into format expected by CDP
        $this->assertSame([
            'type' => 'identify',
            'context' => [
                'product' => 'TechGadget-3000X', // newsletter.product_id
                'event_date' => '2024-12-12' // timestamp
            ],
            'traits' => [
                'subscription_id' => '12345', // id
                'email' => 'email@example.com' // user.email
            ],
            'id' => '4a2b342d-6235-46a9-bc95-6e889b8e5de1' // user.client_id
        ], $identifyModel->toArray());

        // Assert CdpClient::track() called once
        $this->assertSame(1, $this->cdpClient->getTrackCallCount());

        // Assert correct TrackModel is passed to CdpClient::track() method
        $trackModel = $this->cdpClient->getTrackModel();
        assert($trackModel instanceof TrackModel);

        // Assert TrackModel::toArray() organizes data into format expected by CDP
        $this->assertSame([
            'type' => 'track',
            'event' => 'newsletter_subscribed', // event
            'context' => [
                'product' => 'TechGadget-3000X', // newsletter.product_id
                'event_date' => '2024-12-12', // timestamp
                'traits' => [
                    'subscription_id' => '12345', // id
                    'email' => 'email@example.com', // user.email
                ],
            ],
            'properties' => [
                'requires_consent' => true, // from user.region
                'platform' => 'web', // origin
                'product_name' => 'newsletter-001', // newsletter.newsletter_id
                'renewal_date' => '2025-12-12', // start date + 1 year if not provided
                'start_date' => '2024-12-12', // timestamp
                'status' => 'subscribed', // set by api
                'type' => 'newsletter', // set by api
                'is_promotion' => false, // use default
            ],
            'id' => '4a2b342d-6235-46a9-bc95-6e889b8e5de1' // user.client_id
        ], $trackModel->toArray());

        $this->assertSame(Response::HTTP_NO_CONTENT, $this->webTester->getResponse()->getStatusCode());
    }
}