<?php

namespace App\Infrastructure\Service;

use App\Application\Service\Interface\PhoneServiceInterface;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PhoneService implements PhoneServiceInterface
{
    private $phoneNumberUtil;

    public function __construct(
        private readonly HttpClientInterface $httpClient,
    )
    {
        $this->phoneNumberUtil = PhoneNumberUtil::getInstance();
    }
    public function determineCountry(string $phone): string
    {
        try {
            $countryCode = $this->getCountryCode($phone);
            return $this->getCountryNameFromCode($countryCode);
        } catch (NumberParseException $e) {
            return 'Unknown';
        }
    }

    public function cleanPhoneNumber(string $phone): string
    {
        return preg_replace('/[^0-9+]/', '', $phone);
    }

    private function getCountryCode(string $phone): string
    {
        $phoneNumber = $this->phoneNumberUtil->parse($phone, null);
        $regionCode = $this->phoneNumberUtil->getRegionCodeForNumber($phoneNumber);
        return $regionCode ?: 'Unknown';
    }

    private function getCountryNameFromCode(string $code): string
    {
        $response = $this->httpClient->request('GET', 'https://country.io/names.json');
        $countryNames = $response->toArray();
        return $countryNames[$code] ?? 'Unknown';
    }
}