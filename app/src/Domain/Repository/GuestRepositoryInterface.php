<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Guest;

interface GuestRepositoryInterface
{
    public function create(Guest $guest): void;
    public function update(Guest $guest): void;
    public function findByID(int $id): ?Guest;
    public function getAll(): array;
    public function delete(int $id): void;
}