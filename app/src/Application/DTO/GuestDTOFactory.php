<?php

namespace App\Application\DTO;

class GuestDTOFactory
{
    public static function createFromArray(array $data): GuestDTO
    {
        return new GuestDTO(
            $data['firstName'],
            $data['lastName'],
            $data['email'],
            $data['phone'],
            $data['country'] ?? null
        );
    }
}
