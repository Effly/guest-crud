<?php

namespace App\Infrastructure\Controller;

use App\Application\DTO\GuestDTOFactory;
use App\Application\Service\GuestService;
use App\Infrastructure\Request\CreateGuestRequest;
use App\Infrastructure\Request\UpdateGuestRequest;
use App\Infrastructure\Exception\GuestNotFoundException;
use App\Infrastructure\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GuestController extends AbstractController
{
    public function __construct(
        private readonly GuestService $guestService
    )
    {
    }

    #[Route('/guests', name: 'create_guest', methods: ['POST'])]
    public function createGuest(Request $request): Response
    {
        $form = $this->createForm(CreateGuestRequest::class);
        $form->submit(json_decode($request->getContent(), true));

        if (!$form->isValid()) {
            return $this->json(['errors' => (string)$form->getErrors(true, false)], Response::HTTP_BAD_REQUEST);
        }

        $data = $form->getData();
        $guestDTO = GuestDTOFactory::createFromArray((array)$data);

        try {
            $this->guestService->createGuest($guestDTO);
        } catch (UniqueConstraintViolationException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_CONFLICT);
        }

        return $this->json(['message' => 'Guest created'], Response::HTTP_CREATED);
    }

    #[Route('/guests/{id}', name: 'update_guest', methods: ['PUT'])]
    public function updateGuest(int $id, Request $request): Response
    {
        $form = $this->createForm(UpdateGuestRequest::class);
        $form->submit(json_decode($request->getContent(), true));

        if (!$form->isValid()) {
            return $this->json(['errors' => (string)$form->getErrors(true, false)], Response::HTTP_BAD_REQUEST);
        }

        $data = $form->getData();
        $guestDTO = GuestDTOFactory::createFromArray((array)$data);

        try {
            $this->guestService->updateGuest($id, $guestDTO);
        } catch (GuestNotFoundException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        } catch (UniqueConstraintViolationException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_CONFLICT);
        }

        return $this->json(['message' => 'Guest updated'], Response::HTTP_OK);
    }

    #[Route('/guests/{id}', name: 'delete_guest', methods: ['DELETE'])]
    public function deleteGuest(int $id): Response
    {
        try {
            $this->guestService->deleteGuest($id);
        } catch (GuestNotFoundException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }

        return $this->json(['message' => 'Guest deleted'], Response::HTTP_OK);
    }

    #[Route('/guests/{id}', name: 'get_guest', methods: ['GET'])]
    public function getGuest(int $id): Response
    {
        try {
            $guest = $this->guestService->getGuest($id);
        } catch (GuestNotFoundException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }

        return $this->json($guest);
    }

    #[Route('/guests', name: 'get_all_guests', methods: ['GET'])]
    public function getAllGuests(): Response
    {
        $guests = $this->guestService->getAllGuests();

        return $this->json($guests);
    }
}
