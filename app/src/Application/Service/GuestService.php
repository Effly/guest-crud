<?php

namespace App\Application\Service;

use App\Application\DTO\GuestDTO;
use App\Application\Service\Interface\PhoneServiceInterface;
use App\Domain\Entity\Guest;
use App\Domain\Repository\GuestRepositoryInterface;
use App\Infrastructure\Exception\GuestNotFoundException;
use App\Infrastructure\Exception\UniqueConstraintViolationException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException as DoctrineUniqueConstraintViolationException;

class GuestService
{
    public function __construct(
        private readonly GuestRepositoryInterface $guestRepository,
        private readonly PhoneServiceInterface $phoneService,
    )
    {}

    public function createGuest(GuestDTO $guestDTO): void
    {
        $guest = new Guest();
        $guest->setFirstName($guestDTO->firstName);
        $guest->setLastName($guestDTO->lastName);
        $guest->setEmail($guestDTO->email);
        $guest->setPhone($this->phoneService->cleanPhoneNumber($guestDTO->phone));
        $guest->setCountry(
            $guestDTO->country ?? $this->phoneService->determineCountry($guestDTO->phone)
        );

        try {
            $this->guestRepository->create($guest);
        } catch (DoctrineUniqueConstraintViolationException $e) {
            throw new UniqueConstraintViolationException('Phone or email must be unique', 409, $e);
        }
    }

    public function updateGuest(int $id, GuestDTO $guestDTO): void
    {
        $guest = $this->guestRepository->find($id);
        if (!$guest) {
            throw new GuestNotFoundException();
        }

        $guest->setFirstName($guestDTO->firstName);
        $guest->setLastName($guestDTO->lastName);
        $guest->setEmail($guestDTO->email);
        $guest->setPhone($this->phoneService->cleanPhoneNumber($guestDTO->phone));
        $guest->setCountry(
            $guestDTO->country ?? $this->phoneService->determineCountry($guestDTO->phone)
        );

        try {
            $this->guestRepository->update($guest);
        } catch (DoctrineUniqueConstraintViolationException $e) {
            throw new UniqueConstraintViolationException('Phone or email must be unique', 409, $e);
        }
    }

    public function deleteGuest(int $id): void
    {
        $guest = $this->guestRepository->find($id);
        if (!$guest) {
            throw new GuestNotFoundException();
        }

        $this->guestRepository->delete($id);
    }

    public function getGuest(int $id): Guest
    {
        $guest = $this->guestRepository->find($id);
        if (!$guest) {
            throw new GuestNotFoundException();
        }

        return $guest;
    }

    public function getAllGuests(): array
    {
        return $this->guestRepository->findAll();
    }
}
