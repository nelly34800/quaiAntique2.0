<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Repository\BookingRepository;
use App\Repository\RestaurantRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use OpenApi\Attributes as OA;


#[Route('/api/booking', name: 'app_api_booking_')]
class BookingController extends AbstractController
{
    public function __construct(
        private BookingRepository $repository,
        private RestaurantRepository $restaurantRepository,
        private UserRepository $userRepository,
        private EntityManagerInterface $manager,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }
    #[OA\Post(
        path: '/api/booking',
        summary: 'Créer un nouvelle réservation'
    )]
    #[OA\RequestBody(
        required: true,
        description: 'Données de la réservation à créer',
        content: new OA\JsonContent(
            required: ['id', 'guestNumber', 'orderDate', 'orderHour', 'allergy', 'createdAt', 'restaurantId', 'userId'],
            properties: [
                new OA\Property(
                    property: 'id',
                    type: 'integer',
                    example: 1
                ),
                new OA\Property(
                    property: 'guestNumber',
                    type: 'integer',
                    example: 4
                ),
                new OA\Property(
                    property: 'orderDate',
                    type: 'string',
                    format: 'date',
                    example: '26-06-24'
                ),
                new OA\Property(
                    property: 'orderHour',
                    type: 'string',
                    format: 'date-time',
                    example: '2026-06-24T12:00:00Z'
                ),
                new OA\Property(
                    property: 'allergy',
                    type: 'string',
                    example: 'crustacés'
                ),
                new OA\Property(
                    property: 'createdAt',
                    type: 'string',
                    format: 'date-time',
                    example: '2023-01-01T00:00:00Z'
                ),
                new OA\Property(
                    property: 'restaurant',
                    type: 'integer',
                    example: 1
                ),
                new OA\Property(
                    property: 'user',
                    type: 'integer',
                    example: 2
                )
            ]
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Réservation créée avec succès',
    )]
    #[Route(methods: ['POST'])]
    public function new(Request $request): JsonResponse
    {
        $booking = $this->serializer->deserialize($request->getContent(), Booking::class, 'json', ['groups' => ['booking:write']]);
        $booking->setCreatedAt(new \DateTimeImmutable());

        $data = json_decode($request->getContent(), true);

        if (!isset($data['restaurant']) || empty($data['restaurant'])) {
            return new JsonResponse(['message' => 'Restaurant id required'],Response::HTTP_BAD_REQUEST);
        }

        $restaurant = $this->restaurantRepository->find($data['restaurant']);

        if (!$restaurant) {
            return new JsonResponse(['message' => 'Restaurant not found'], Response::HTTP_NOT_FOUND);
        }
        $booking->setRestaurant($restaurant);

        if (!isset($data['user']) || empty($data['user'])) {
            return new JsonResponse(['message' => 'User id required'],Response::HTTP_BAD_REQUEST);
        }

        $user = $this->userRepository->find($data['user']);

        if (!$user) {
            return new JsonResponse(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }
        $booking->setUser($user);

        $this->manager->persist($booking);
        $this->manager->flush(); 

        $responseData = $this->serializer->serialize($booking, 'json', ['groups' => ['booking:read']] );
        $location = $this->urlGenerator->generate(
        'app_api_booking_show',
        ['id' => $booking->getId()],
        UrlGeneratorInterface::ABSOLUTE_URL,
        );

        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
    }
    #[OA\Get(
        path: '/api/booking',
        summary: 'Afficher toutes les réservations'
    )]
    #[OA\Response(
        response: 200,
        description: 'Réservations trouvées avec succès',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(
                properties: [
                    new OA\Property(
                        property: 'id',
                        type: 'integer',
                        example: 1
                    ),
                    new OA\Property(
                        property: 'guestNumber',
                        type: 'integer',
                        example: 4
                    ),
                    new OA\Property(
                        property: 'orderDate',
                        type: 'string',
                        format: 'date',
                        example: '26-06-24'
                    ),
                    new OA\Property(
                        property: 'orderHour',
                        type: 'string',
                        format: 'date-time',
                        example: '2026-06-24T12:00:00Z'
                    ),
                    new OA\Property(
                        property: 'allergy',
                        type: 'string',
                        example: 'crustacés'
                    ),
                    new OA\Property(
                        property: 'createdAt',
                        type: 'string',
                        format: 'date-time',
                        example: '2023-01-01T00:00:00Z'
                    ),
                    new OA\Property(
                    property: 'restaurant',
                    type: 'integer',
                    example: 1
                ),
                    new OA\Property(
                    property: 'user',
                    type: 'integer',
                    example: 2
                )
                ]
            )
        )
    )]
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(): JsonResponse
    {
      $booking = $this->repository->findAll();

      $responseData = $this->serializer->serialize($booking, 'json', ['groups' => ['booking:read']]);

      return new JsonResponse($responseData,Response::HTTP_OK,[],true);
    }

    #[OA\Get(
        path: '/api/booking/{id}',
        summary: 'Afficher une réservation par son ID'
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        required: true,
        description: 'ID de la réservation à afficher',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 200,
        description: 'Réservation trouvée avec succès',
        content: new OA\JsonContent(
            required: ['id', 'guestNumber', 'orderDate', 'orderHour', 'allergy', 'createdAt', 'restaurantId', 'userId'],
            properties: [
                new OA\Property(
                    property: 'id',
                    type: 'integer',
                    example: 1
                ),
                new OA\Property(
                    property: 'guestNumber',
                    type: 'integer',
                    example: 4
                ),
                new OA\Property(
                    property: 'orderDate',
                    type: 'string',
                    format: 'date',
                    example: '26-06-24'
                ),
                new OA\Property(
                    property: 'orderHour',
                    type: 'string',
                    format: 'date-time',
                    example: '2026-06-24T12:00:00Z'
                ),
                new OA\Property(
                    property: 'allergy',
                    type: 'string',
                    example: 'crustacés'
                ),
                new OA\Property(
                    property: 'createdAt',
                    type: 'string',
                    format: 'date-time',
                    example: '2023-01-01T00:00:00Z'
                ),
                new OA\Property(
                    property: 'restaurant',
                    type: 'integer',
                    example: 1
                ),
                    new OA\Property(
                    property: 'user',
                    type: 'integer',
                    example: 2
                )
            ]
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'Réservation non trouvée'
    )]
    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $booking = $this->repository->find($id);

        if (!$booking) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse([
            'id' => $booking->getId(),
            'guestNumber' => $booking->getGuestNumber()
        ]);
    }

    #[OA\Put(
        path: '/api/booking/{id}',
        summary: 'Modifier une réservation par son ID'
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        required: true,
        description: 'ID dd la réservation à modifier',
        schema: new OA\Schema(type: 'integer')
    )]
     #[OA\RequestBody(
        required: true,
        description: 'Données de la réservation à modifier',
        content: new OA\JsonContent(
            required: ['guestNumber', 'orderDate', 'orderHour', 'allergy', 'createdAt'],
            properties: [
                new OA\Property(
                    property: 'guestNumber',
                    type: 'integer',
                    example: 4
                ),
                new OA\Property(
                    property: 'orderDate',
                    type: 'string',
                    format: 'date',
                    example: '26-06-24'
                ),
                new OA\Property(
                    property: 'orderHour',
                    type: 'string',
                    format: 'date-time',
                    example: '2026-06-24T12:00:00Z'
                ),
                new OA\Property(
                    property: 'allergy',
                    type: 'string',
                    example: 'crustacés'
                ),
                new OA\Property(
                    property: 'createdAt',
                    type: 'string',
                    format: 'date-time',
                    example: '2023-01-01T00:00:00Z'
                )
            ]
        )
    )]
    #[OA\Response(
        response: 204,
        description: 'Réservation modifiée avec succès',
        content: new OA\JsonContent(
            required: ['id', 'guestNumber', 'orderDate', 'orderHour', 'allergy', 'createdAt', 'restaurantId', 'userId'],
            properties: [
                new OA\Property(
                    property: 'id',
                    type: 'integer',
                    example: 1
                ),
                new OA\Property(
                    property: 'guestNumber',
                    type: 'integer',
                    example: 4
                ),
                new OA\Property(
                    property: 'orderDate',
                    type: 'string',
                    format: 'date',
                    example: '26-06-24'
                ),
                new OA\Property(
                    property: 'orderHour',
                    type: 'string',
                    format: 'date-time',
                    example: '2026-06-24T12:00:00Z'
                ),
                new OA\Property(
                    property: 'allergy',
                    type: 'string',
                    example: 'crustacés'
                ),
                new OA\Property(
                    property: 'createdAt',
                    type: 'string',
                    format: 'date-time',
                    example: '2023-01-01T00:00:00Z'
                ),
                new OA\Property(
                    property: 'restaurant',
                    type: 'integer',
                    example: 1,
                ),
                    new OA\Property(
                    property: 'user',
                    type: 'integer',
                    example: 2
                )
            ]
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'Réservation non trouvée'
    )]
    #[Route('/{id}', name: 'edit', methods: ['PUT'])]
    public function edit(int $id, Request $request): JsonResponse
    {
        $booking = $this->repository->findOneBy(['id' => $id]);
        if ($booking) {
            $booking = $this->serializer->deserialize($request->getContent(), Booking::class, 'json',
            ['groups' => ['booking:write'], AbstractNormalizer::OBJECT_TO_POPULATE => $booking]);
            $booking->setUpdatedAt(new DateTimeImmutable());

            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }
        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[OA\Delete(
        path: '/api/booking/{id}',
        summary: 'Supprimer une réservation par son ID'
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        required: true,
        description: 'ID de la réservation à supprimer',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 204,
        description: 'Réservation supprimée avec succès'
    )]
    #[OA\Response(
        response: 404,
        description: 'Réservation non trouvée'
    )]
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $booking = $this->repository->findOneBy(['id' => $id]);
        if ($booking) {
            $this->manager->remove($booking);
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }
        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
}