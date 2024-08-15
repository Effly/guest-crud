<?php

namespace App\Tests\Controller;

use App\Application\DTO\GuestDTO;
use App\Application\Service\GuestService;
use App\Domain\Entity\Guest;
use App\Infrastructure\Exception\GuestNotFoundException;
use App\Infrastructure\Exception\UniqueConstraintViolationException;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class GuestControllerTest extends WebTestCase
{
    private $client;
    private $guestService;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->guestService = $this->createMock(GuestService::class);

        $container = $this->client->getContainer();
        $container->set(GuestService::class, $this->guestService);
    }

    public function testCreateGuestSuccess(): void
    {
        $this->guestService->method('createGuest')->willReturn(null);

        $this->client->request('POST', '/guests', [], [], [], json_encode([
            'firstName' => 'John',
            'lastName' => 'Doe',
            'email' => 'john.doe@example.com',
            'phone' => '+1234567890',
            'country' => 'USA'
        ]));

        $response = $this->client->getResponse();
        $this->assertSame(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString('{"message":"Guest created"}', $response->getContent());
    }

    public function testCreateGuestValidationError(): void
    {
        $this->client->request('POST', '/guests', [], [], [], json_encode([
            'firstName' => '',
            'lastName' => 'Doe',
            'email' => 'invalid-email',
            'phone' => '1234567890',
            'country' => 'USA'
        ]));

        $response = $this->client->getResponse();
        $this->assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

    public function testCreateGuestUniqueConstraintViolation(): void
    {
        $this->guestService->method('createGuest')->willThrowException(new UniqueConstraintViolationException('Phone or email must be unique'));

        $this->client->request('POST', '/guests', [], [], [], json_encode([
            'firstName' => 'John',
            'lastName' => 'Doe',
            'email' => 'john.doe@example.com',
            'phone' => '+1234567890',
            'country' => 'USA'
        ]));

        $response = $this->client->getResponse();
        $this->assertSame(Response::HTTP_CONFLICT, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString('{"error":"Phone or email must be unique"}', $response->getContent());
    }

    public function testUpdateGuestSuccess(): void
    {
        $this->guestService->method('updateGuest')->willReturn(null);

        $this->client->request('PUT', '/guests/1', [], [], [], json_encode([
            'firstName' => 'Jane',
            'lastName' => 'Doe',
            'email' => 'jane.doe@example.com',
            'phone' => '+1234567890',
            'country' => 'USA'
        ]));

        $response = $this->client->getResponse();
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString('{"message":"Guest updated"}', $response->getContent());
    }

    public function testUpdateGuestNotFound(): void
    {
        $this->guestService->method('updateGuest')->willThrowException(new GuestNotFoundException());

        $this->client->request('PUT', '/guests/999', [], [], [], json_encode([
            'firstName' => 'Jane',
            'lastName' => 'Doe',
            'email' => 'jane.doe@example.com',
            'phone' => '+1234567890',
            'country' => 'USA'
        ]));

        $response = $this->client->getResponse();
        $this->assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString('{"error":"Guest not found"}', $response->getContent());
    }

    public function testDeleteGuestSuccess(): void
    {
        $this->guestService->method('deleteGuest')->willReturn(null);

        $this->client->request('DELETE', '/guests/1');

        $response = $this->client->getResponse();
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString('{"message":"Guest deleted"}', $response->getContent());
    }

    public function testDeleteGuestNotFound(): void
    {
        $this->guestService->method('deleteGuest')->willThrowException(new GuestNotFoundException());

        $this->client->request('DELETE', '/guests/999');

        $response = $this->client->getResponse();
        $this->assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString('{"error":"Guest not found"}', $response->getContent());
    }

    public function testGetGuestSuccess(): void
    {
        $guest = new Guest();
        $guest->setFirstName('John');
        $guest->setLastName('Doe');
        $guest->setEmail('john.doe@example.com');
        $guest->setPhone('+1234567890');
        $guest->setCountry('USA');

        $this->guestService->method('getGuest')->willReturn($guest);

        $this->client->request('GET', '/guests/1');

        $response = $this->client->getResponse();
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

    public function testGetGuestNotFound(): void
    {
        $this->guestService->method('getGuest')->willThrowException(new GuestNotFoundException());

        $this->client->request('GET', '/guests/999');

        $response = $this->client->getResponse();
        $this->assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString('{"error":"Guest not found"}', $response->getContent());
    }

    public function testGetAllGuests(): void
    {
        $guests = [
            new Guest(),
            new Guest(),
        ];

        $this->guestService->method('getAllGuests')->willReturn($guests);

        $this->client->request('GET', '/guests');

        $response = $this->client->getResponse();
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }
}
