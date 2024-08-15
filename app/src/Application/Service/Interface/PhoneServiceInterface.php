<?php

namespace App\Application\Service\Interface;

interface PhoneServiceInterface
{
    public function determineCountry(string $phone): string;
    public function cleanPhoneNumber(string $phone): string;
}