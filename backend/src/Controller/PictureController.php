<?php

namespace App\Controller;

use App\Entity\Picture;
use App\Repository\PictureRepository;
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


#[Route('/api/picture', name: 'app_api_picture_')]
class PictureController extends AbstractController
{
    public function __construct(
        private PictureRepository $repository,
        private RestaurantRepository $restaurantRepository,
        private EntityManagerInterface $manager,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    #[OA\Post(
        path: '/api/picture',
        summary: 'Ajouter un nouvelle image'
    )]
    #[OA\RequestBody(
        required: true,
        description: 'Données de l\'image à ajouterer',
        content: new OA\JsonContent(
            required: ['id', 'title', 'slug', 'createdAt', 'restaurantId'],
            properties: [
                new OA\Property(
                    property: 'id',
                    type: 'integer',
                    example: 1
                ),
                new OA\Property(
                    property: 'title',
                    type: 'string',
                    example: 'Image 1'
                ),
                new OA\Property(
                    property: 'slug',
                    type: 'string',
                    example: 'image-1'
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
                )
            ]
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Image ajoutée avec succès',
    )]
    #[Route(methods: ['POST'])]
    public function new(Request $request): JsonResponse
    {
        $picture = $this->serializer->deserialize($request->getContent(), Picture::class, 'json', ['groups' => ['picture:write']]);
        $picture->setCreatedAt(new \DateTimeImmutable());

        $data = json_decode($request->getContent(), true);

        if (!isset($data['restaurant']) || empty($data['restaurant'])) {
            return new JsonResponse(['message' => 'Restaurant id required'],Response::HTTP_BAD_REQUEST);
        }
        
        $restaurant = $this->restaurantRepository->find($data['restaurant']);

        if (!$restaurant) {
            return new JsonResponse(['message' => 'Restaurant not found'], Response::HTTP_NOT_FOUND);
        }
        $picture->setRestaurant($restaurant);

        $this->manager->persist($picture);
        $this->manager->flush(); 

        $responseData = $this->serializer->serialize($picture, 'json', ['groups' => ['picture:read']] );
        $location = $this->urlGenerator->generate(
        'app_api_picture_show',
        ['id' => $picture->getId()],
        UrlGeneratorInterface::ABSOLUTE_URL,
        );

        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[OA\Get(
        path: '/api/picture',
        summary: 'Afficher toutes les images'
    )]
    #[OA\Response(
        response: 200,
        description: 'Images trouvées avec succès',
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
                        property: 'title',
                        type: 'string',
                        example: 'Image 1'
                    ),
                    new OA\Property(
                        property: 'slug',
                        type: 'string',
                        example: 'image-1'
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
                    )
                ]
            )
        )
    )]
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(): JsonResponse
    {
      $picture = $this->repository->findAll();

      $responseData = $this->serializer->serialize($picture, 'json', ['groups' => ['picture:read']]);

      return new JsonResponse($responseData,Response::HTTP_OK,[],true);
    }

    #[OA\Get(
        path: '/api/picture/{id}',
        summary: 'Afficher une image par son ID'
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        required: true,
        description: 'ID de l\'image à afficher',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 200,
        description: 'Image trouvée avec succès',
        content: new OA\JsonContent(
            required: ['id', 'title', 'slug', 'createdAt', 'restaurantId'],
            properties: [
                new OA\Property(
                    property: 'id',
                    type: 'integer',
                    example: 1
                ),
                new OA\Property(
                    property: 'title',
                    type: 'string',
                    example: 'Image 1'
                ),
                new OA\Property(
                    property: 'slug',
                    type: 'string',
                    example: 'image-1'
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
                )
            ]
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'Image non trouvée'
    )]
    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $picture = $this->repository->findOneBy(['id' => $id]);
        if ($picture) {
            $responseData = $this->serializer->serialize($picture, 'json', ['groups' => ['picture:read']]);

            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }
        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[OA\Put(
        path: '/api/picture/{id}',
        summary: 'Modifier une image par son ID'
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        required: true,
        description: 'ID de l\'image à modifier',
        schema: new OA\Schema(type: 'integer')
    )]
     #[OA\RequestBody(
        required: true,
        description: 'Données de l\'image à modifier',
        content: new OA\JsonContent(
            required: ['title', 'slug', 'createdAt'],
            properties: [
                new OA\Property(
                    property: 'title',
                    type: 'string',
                    example: 'Image 1'
                ),
                new OA\Property(
                    property: 'slug',
                    type: 'string',
                    example: 'image-1'
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
        description: 'Image modifiée avec succès',
        content: new OA\JsonContent(
            required: ['id', 'title', 'slug', 'createdAt', 'restaurantId'],
            properties: [
                new OA\Property(
                    property: 'id',
                    type: 'integer',
                    example: 1
                ),
                new OA\Property(
                    property: 'title',
                    type: 'string',
                    example: 'Image 1'
                ),
                new OA\Property(
                    property: 'slug',
                    type: 'string',
                    example: 'image-1'
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
                )
            ]
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'Image non trouvée'
    )]
    #[Route('/{id}', name: 'edit', methods: ['PUT'])]
    public function edit(int $id, Request $request): JsonResponse
    {
        $picture = $this->repository->findOneBy(['id' => $id]);
        if ($picture) {
            $picture = $this->serializer->deserialize($request->getContent(), Picture::class, 'json', 
            ['groups' => ['picture:write'], AbstractNormalizer::OBJECT_TO_POPULATE => $picture]);
            $picture->setUpdatedAt(new DateTimeImmutable());

            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }
        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[OA\Delete(
        path: '/api/picture/{id}',
        summary: 'Supprimer une image par son ID'
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        required: true,
        description: 'ID de l\'image à supprimer',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 204,
        description: 'Image supprimée avec succès'
    )]
    #[OA\Response(
        response: 404,
        description: 'Image non trouvée'
    )]
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $picture = $this->repository->findOneBy(['id' => $id]);
        if ($picture) {
            $this->manager->remove($picture);
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }
        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
}