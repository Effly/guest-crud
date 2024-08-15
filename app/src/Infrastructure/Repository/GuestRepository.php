<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Guest;
use App\Domain\Repository\GuestRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class GuestRepository extends ServiceEntityRepository implements GuestRepositoryInterface
{
    public function __construct(
        ManagerRegistry                         $registry,
        private readonly EntityManagerInterface $_em
    )
    {
        parent::__construct($registry, Guest::class);
    }

    public function create(Guest $guest): void
    {
        $this->_em->persist($guest);
        $this->_em->flush();
    }

    public function update(Guest $guest): void
    {
        $this->_em->flush($guest);
    }
    public function findByID(int $id): ?Guest
    {
        return $this->find($id);
    }

    public function getAll(): array
    {
        return $this->findAll();
    }

    public function delete(int $id): void
    {
        $guest = $this->find($id);
        if ($guest) {
            $this->_em->remove($guest);
            $this->_em->flush();
        }
    }
}
