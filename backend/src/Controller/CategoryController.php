<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use OpenApi\Attributes as OA;

#[Route('/api/category', name: 'app_api_category_')]
class CategoryController extends AbstractController
{
    public function __construct(
        private CategoryRepository $repository,
        private EntityManagerInterface $manager,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    #[OA\Post(
        path: '/api/category',
        summary: 'Ajouter une nouvelle catégorie'
    )]
    #[OA\RequestBody(
        required: true,
        description: 'Données de la catégorie à ajouter',
        content: new OA\JsonContent(
            required: ['id', 'title', 'createdAt'],
            properties: [
                new OA\Property(
                    property: 'id',
                    type: 'integer',
                    example: 1
                ),
                new OA\Property(
                    property: 'title',
                    type: 'string',
                    example: 'Entrée'
                ),
                new OA\Property(
                    property: 'createdAt',
                    type: 'string',
                    format: 'date-time',
                    example: '2023-01-01T00:00:00Z'
                ),
            ]
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Catégorie ajoutée avec succès',
    )]
    #[Route(methods: ['POST'])]
    public function new(Request $request): JsonResponse
    {
        $category = $this->serializer->deserialize($request->getContent(), Category::class, 'json', ['groups' => ['category:write']]);
        $category->setCreatedAt(new \DateTimeImmutable());

        $this->manager->persist($category);
        $this->manager->flush(); 

        $responseData = $this->serializer->serialize($category, 'json', ['groups' => ['category:read']] );
        $location = $this->urlGenerator->generate(
        'app_api_category_show',
        ['id' => $category->getId()],
        UrlGeneratorInterface::ABSOLUTE_URL,
        );

        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[OA\Get(
        path: '/api/category',
        summary: 'Afficher toutes les catégories'
    )]
    #[OA\Response(
        response: 200,
        description: 'Categories trouvées avec succès',
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
                        example: 'Entrée'
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
      $category = $this->repository->findAll();

      $responseData = $this->serializer->serialize($category, 'json', ['groups' => ['category:read']]);

      return new JsonResponse($responseData,Response::HTTP_OK,[],true);
    }

    #[OA\Get(
        path: '/api/category/{id}',
        summary: 'Afficher une catégorie par son ID'
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        required: true,
        description: 'ID de la catégorie à afficher',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 200,
        description: 'Catégorie trouvée avec succès',
        content: new OA\JsonContent(
            required: ['id', 'title', 'createdAt'],
            properties: [
                new OA\Property(
                    property: 'id',
                    type: 'integer',
                    example: 1
                ),
                new OA\Property(
                    property: 'title',
                    type: 'string',
                    example: 'Entrée'
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
        description: 'Catégorie non trouvée'
    )]
    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $category = $this->repository->findOneBy(['id' => $id]);
        if ($category) {
            $responseData = $this->serializer->serialize($category, 'json', ['groups' => ['category:read']] );

            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }
        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[OA\Put(
        path: '/api/category/{id}',
        summary: 'Modifier une catégorie par son ID'
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        required: true,
        description: 'ID de la catégorie à modifier',
        schema: new OA\Schema(type: 'integer')
    )]
     #[OA\RequestBody(
        required: true,
        description: 'Données de la catégorie à modifier',
        content: new OA\JsonContent(
            required: ['title', 'createdAt'],
            properties: [
                new OA\Property(
                    property: 'title',
                    type: 'string',
                    example: 'Plat principal'
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
        description: 'Catégorie modifiée avec succès',
        content: new OA\JsonContent(
            required: ['id', 'title', 'createdAt'],
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
        description: 'Catégorie non trouvée'
    )]

    #[Route('/{id}', name: 'edit', methods: ['PUT'])]
    public function edit(int $id, Request $request): JsonResponse
    {
        $category = $this->repository->findOneBy(['id' => $id]);
        if ($category) {
            $category = $this->serializer->deserialize($request->getContent(), Category::class, 'json', 
            ['groups' => ['category:write'], AbstractNormalizer::OBJECT_TO_POPULATE => $category]);
            $category->setUpdatedAt(new DateTimeImmutable());

            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }
        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[OA\Delete(
        path: '/api/category/{id}',
        summary: 'Supprimer une catégorie par son ID'
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        required: true,
        description: 'ID de la catégorie à supprimer',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 204,
        description: 'Catégorie supprimée avec succès'
    )]
    #[OA\Response(
        response: 404,
        description: 'Catégorie non trouvée'
    )]
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $category = $this->repository->findOneBy(['id' => $id]);
        if ($category) {
            $this->manager->remove($category);
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }
        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
}
