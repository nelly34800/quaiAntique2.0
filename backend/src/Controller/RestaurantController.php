<?php

namespace App\Controller;

use App\Entity\Restaurant;
use App\Repository\RestaurantRepository;
use Doctrine\ORM\EntityManagerInterface;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use OpenApi\Attributes as OA;

#[Route('/api/restaurant', name: 'app_api_restaurant_')]
class RestaurantController extends AbstractController
{
    public function __construct(
        private RestaurantRepository $repository,
        private EntityManagerInterface $manager,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    #[OA\Post(
        path: '/api/restaurant',
        summary: 'Créer un nouveau restaurant'
    )]
    #[OA\RequestBody(
        required: true,
        description: 'Données du restaurant à créer',
        content: new OA\JsonContent(
            required: ['id', 'name', 'description', 'amOpeningTime', 'pmOpeningTime', 'maxGuest', 'createdAt'],
            properties: [
                new OA\Property(
                    property: 'id',
                    type: 'integer',
                    example: 4
                ),
                new OA\Property(
                    property: 'name',
                    type: 'string',
                    example: 'Quai Antique'
                ),
                new OA\Property(
                    property: 'description',
                    type: 'string',
                    example: 'Restaurant du chef Arnaud Michant'
                ),
                new OA\Property(
                    property: 'amOpeningTime',
                    type: 'array',
                    items: new OA\Items(type: 'string', 
                    example: 'du mardi au dimanche')
                ),
                new OA\Property(
                    property: 'pmOpeningTime',
                    type: 'array',
                    items: new OA\Items(type: 'string', 
                    example: '12h00 - 14h00 et 18h00 - 23h00')
                ),
                new OA\Property(
                    property: 'maxGuest',
                    type: 'integer',
                    example: 60
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
        response: 201,
        description: 'Restaurant créé avec succès',
    )]
    #[Route(methods: ['POST'])]
    public function new(Request $request): JsonResponse
    {
        $restaurant = $this->serializer->deserialize($request->getContent(), Restaurant::class, 'json', ['groups' => ['restaurant:write']]);
        $restaurant->setCreatedAt(new \DateTimeImmutable());

        $this->manager->persist($restaurant);
        $this->manager->flush(); 

        $responseData = $this->serializer->serialize($restaurant, 'json', ['groups' => ['restaurant:read']] );
        $location = $this->urlGenerator->generate(
        'app_api_restaurant_show',
        ['id' => $restaurant->getId()],
        UrlGeneratorInterface::ABSOLUTE_URL,
        );

        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[OA\Get(
        path: '/api/restaurant',
        summary: 'Afficher tous les restaurants'
    )]
    #[OA\Response(
        response: 200,
        description: 'Restaurants trouvés avec succès',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(
                properties: [
                    new OA\Property(
                        property: 'id',
                        type: 'integer',
                        example: 4
                    ),
                    new OA\Property(
                        property: 'name',
                        type: 'string',
                        example: 'Le Quai Antique'
                    ),
                    new OA\Property(
                        property: 'description',
                        type: 'string',
                        example: 'description du restaurant'
                    ),
                    new OA\Property(
                        property: 'amOpeningTime',
                        type: 'array',
                        items: new OA\Items(type: 'string', 
                        example: 'du mardi au dimanche')
                    ),
                    new OA\Property(
                        property: 'pmOpeningTime',
                        type: 'array',
                        items: new OA\Items(type: 'string', 
                        example: '12h00 - 14h00 et 18h00 - 23h00')
                    ),
                    new OA\Property(
                        property: 'maxGuest',
                        type: 'integer',
                        example: 50
                    ),
                    new OA\Property(
                        property: 'createdAt',
                        type: 'string',
                        format: 'date-time',
                        example: '2023-01-01T00:00:00Z'
                    )
                ]
            )
        )
    )]
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(): JsonResponse
    {
      $restaurants = $this->repository->findAll();

      $responseData = $this->serializer->serialize($restaurants, 'json', ['groups' => ['restaurant:read']]);

      return new JsonResponse($responseData,Response::HTTP_OK,[],true);
    }

    #[OA\Get(
        path: '/api/restaurant/{id}',
        summary: 'Afficher un restaurant par son ID'
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        required: true,
        description: 'ID du restaurant à afficher',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 200,
        description: 'Restaurant trouvé avec succès',
        content: new OA\JsonContent(
            required: ['id', 'name', 'description', 'amOpeningTime', 'pmOpeningTime', 'maxGuest', 'createdAt'],
            properties: [
                new OA\Property(
                    property: 'id',
                    type: 'integer',
                    example: 4
                ),
                new OA\Property(
                    property: 'name',
                    type: 'string',
                    example: 'Le Quai Antique'
                ),
                new OA\Property(
                    property: 'description',
                    type: 'string',
                    example: 'description du restaurant'
                ),
                new OA\Property(
                    property: 'amOpeningTime',
                    type: 'array',
                    items: new OA\Items(type: 'string', 
                    example: 'du mardi au dimanche')
                ),
                new OA\Property(
                    property: 'pmOpeningTime',
                    type: 'array',
                    items: new OA\Items(type: 'string', 
                    example: '12h00 - 14h00 et 18h00 - 23h00')
                ),
                new OA\Property(
                    property: 'maxGuest',
                    type: 'integer',
                    example: 50
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
        response: 404,
        description: 'Restaurant non trouvé'
    )]
    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $restaurant = $this->repository->findOneBy(['id' => $id]);
        if ($restaurant) {
            $responseData = $this->serializer->serialize($restaurant, 'json', ['groups' => ['restaurant:read']]);

            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }
        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[OA\Put(
        path: '/api/restaurant/{id}',
        summary: 'Modifier un restaurant par son ID'
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        required: true,
        description: 'ID du restaurant à modifier',
        schema: new OA\Schema(type: 'integer')
    )]
     #[OA\RequestBody(
        required: true,
        description: 'Données du restaurant à modifier',
        content: new OA\JsonContent(
            required: ['name', 'description', 'amOpeningTime', 'pmOpeningTime', 'maxGuest', 'createdAt'],
            properties: [
                new OA\Property(
                    property: 'name',
                    type: 'string',
                    example: 'Quai Antique'
                ),
                new OA\Property(
                    property: 'description',
                    type: 'string',
                    example: 'Restaurant du chef Arnaud Michant'
                ),
                new OA\Property(
                    property: 'amOpeningTime',
                    type: 'array',
                    items: new OA\Items(type: 'string', 
                    example: 'du mardi au dimanche')
                ),
                new OA\Property(
                    property: 'pmOpeningTime',
                    type: 'array',
                    items: new OA\Items(type: 'string', 
                    example: '12h00 - 14h00 et 18h00 - 23h00')
                ),
                new OA\Property(
                    property: 'maxGuest',
                    type: 'integer',
                    example: 60
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
        description: 'Restaurant modifié avec succès',
        content: new OA\JsonContent(
            required: ['id', 'name', 'description', 'amOpeningTime', 'pmOpeningTime', 'maxGuest', 'createdAt'],
            properties: [
                new OA\Property(
                    property: 'id',
                    type: 'integer',
                    example: 2
                ),
                new OA\Property(
                    property: 'name',
                    type: 'string',
                    example: 'Le Quai Antique2'
                ),
                new OA\Property(
                    property: 'description',
                    type: 'string',
                    example: 'description du restaurant'
                ),
                new OA\Property(
                    property: 'amOpeningTime',
                    type: 'array',
                    items: new OA\Items(type: 'string', 
                    example: 'du mardi au dimanche')
                ),
                new OA\Property(
                    property: 'pmOpeningTime',
                    type: 'array',
                    items: new OA\Items(type: 'string', 
                    example: '12h00 - 14h00 et 18h00 - 23h00')
                ),
                new OA\Property(
                    property: 'maxGuest',
                    type: 'integer',
                    example: 50
                ),
                new OA\Property(
                    property: 'createdAt',
                    type: 'string',
                    format: 'date-time',
                    example: '2025-01-01T00:00:00Z'
                )
            ]
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'Restaurant non trouvé'
    )]
    #[Route('/{id}', name: 'edit', methods: ['PUT'])]
    public function edit(int $id, Request $request): JsonResponse
    {
        $restaurant = $this->repository->findOneBy(['id' => $id]);
        if ($restaurant) {
            $restaurant = $this->serializer->deserialize($request->getContent(), Restaurant::class, 'json',
            ['groups' => ['restaurant:write'], AbstractNormalizer::OBJECT_TO_POPULATE => $restaurant]);
            $restaurant->setUpdatedAt(new DateTimeImmutable());

            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }
        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[OA\Delete(
        path: '/api/restaurant/{id}',
        summary: 'Supprimer un restaurant par son ID'
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        required: true,
        description: 'ID du restaurant à supprimer',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 204,
        description: 'Restaurant supprimé avec succès'
    )]
    #[OA\Response(
        response: 404,
        description: 'Restaurant non trouvé'
    )]
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $restaurant = $this->repository->findOneBy(['id' => $id]);
        if ($restaurant) {
            $this->manager->remove($restaurant);
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }
        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
}