<?php

namespace App\Application\DTO;

class GuestDTO
{
    public function __construct(
        public string $firstName,
        public string $lastName,
        public string $email,
        public string $phone,
        public ?string $country = null
    )
    {}
}
