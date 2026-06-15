<?php

namespace App\Controller;

use App\Entity\Food;
use App\Repository\FoodRepository;
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

 #[Route('/api/food', name: 'app_api_food_')]
class FoodController extends AbstractController
{
    public function __construct(
        private FoodRepository $repository,
        private CategoryRepository $categoryRepository,
        private EntityManagerInterface $manager,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    #[OA\Post(
        path: '/api/food',
        summary: 'Ajouter un nouveau plat'
    )]
    #[OA\RequestBody(
        required: true,
        description: 'Données du plat à ajouter',
        content: new OA\JsonContent(
            required: ['id', 'title', 'description', 'price', 'createdAt', 'categories'],
            properties: [
                new OA\Property(
                    property: 'id',
                    type: 'integer',
                    example: 1
                ),
                new OA\Property(
                        property: 'title',
                        type: 'string',
                        example: 'Saumon à l\'oseille'
                ),
                new OA\Property(
                    property: 'description',
                    type: 'string',
                    example: 'Saumon à l\'oseille avec son accompagnement de légumes de saison'
                ),
                new OA\Property(
                        property: 'price',
                        type: 'integer',
                        example: 19
                    ),
                new OA\Property(
                    property: 'createdAt',
                    type: 'string',
                    format: 'date-time',
                    example: '2023-01-01T00:00:00Z'
                    ),
                new OA\Property(
                    property: 'categories',
                    type: 'array',
                    items: new OA\Items(
                        type: 'integer',
                        example: 1
                    )
                )
            ]
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'plat ajouté avec succès',
    )]
    #[Route(methods: ['POST'])]
    public function new(Request $request): JsonResponse
    {
        $food = $this->serializer->deserialize($request->getContent(), Food::class, 'json', ['groups' => ['food:write']]);
        $food->setCreatedAt(new \DateTimeImmutable());

        $data = json_decode($request->getContent(), true);

        if (!isset($data['categories']) || empty($data['categories'])) {
            return new JsonResponse(['message' => 'Categories required'],Response::HTTP_BAD_REQUEST);
        }

        foreach ($data['categories'] as $categoryId) {
            $category = $this->categoryRepository->find($categoryId);

            if ($category) {
                $food->addCategory($category);
            }
        }

        $this->manager->persist($food);
        $this->manager->flush(); 

        $responseData = $this->serializer->serialize($food, 'json', ['groups' => ['food:read']]);
        $location = $this->urlGenerator->generate(
        'app_api_food_show',
        ['id' => $food->getId()],
        UrlGeneratorInterface::ABSOLUTE_URL,
        );

        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[OA\Get(
        path: '/api/food',
        summary: 'Afficher tous les plats'
    )]
    #[OA\Response(
        response: 200,
        description: 'Plats trouvés avec succès',
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
                        example: 'Saumon à l\'oseille'
                    ),
                    new OA\Property(
                        property: 'description',
                        type: 'string',
                        example: 'Saumon à l\'oseille avec son accompagnement de légumes de saison'
                    ),
                    new OA\Property(
                        property: 'price',
                        type: 'integer',
                        example: 19
                    ),
                    new OA\Property(
                        property: 'createdAt',
                        type: 'string',
                        format: 'date-time',
                        example: '2023-01-01T00:00:00Z'
                    ),
                    new OA\Property(
                        property: 'categories',
                        type: 'array',
                        items: new OA\Items(
                            properties: [
                                new OA\Property(
                                    property: 'id',
                                    type: 'integer',
                                    example: 1
                                ),
                                new OA\Property(
                                    property: 'name',
                                    type: 'string',
                                    example: 'Plat principal'
                                )
                            ]
                        )
                    )
                ]
            )
        )
    )]
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(): JsonResponse
    {
      $food = $this->repository->findAll();

      $responseData = $this->serializer->serialize($food, 'json', ['groups' => ['food:read']]);

      return new JsonResponse($responseData,Response::HTTP_OK,[],true);
    }

    #[OA\Get(
        path: '/api/food/{id}',
        summary: 'Afficher un plat par son ID'
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        required: true,
        description: 'ID du plat à afficher',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 200,
        description: 'Plat trouvé avec succès',
        content: new OA\JsonContent(
            required: ['id', 'title', 'description', 'price', 'createdAt', 'categories'],
            properties: [
                new OA\Property(
                    property: 'id',
                    type: 'integer',
                    example: 1
                ),
                new OA\Property(
                    property: 'title',
                    type: 'string',
                    example: 'Saumon à l\'oseille'
                ),
                new OA\Property(
                    property: 'description',
                    type: 'string',
                    example: 'Saumon à l\'oseille avec son accompagnement de légumes de saison'
                ),
                new OA\Property(
                    property: 'price',
                    type: 'integer',
                    example: 19
                ),
                new OA\Property(
                    property: 'createdAt',
                    type: 'string',
                    format: 'date-time',
                    example: '2023-01-01T00:00:00Z'
                ),
                new OA\Property(
                    property: 'categories',
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(
                                property: 'id',
                                type: 'integer',
                                example: 1
                            ),
                            new OA\Property(
                                property: 'name',
                                type: 'string',
                                example: 'Plat principal'
                            )
                        ]
                    )
                )
            ]
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'Plat non trouvé'
    )]
    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $food = $this->repository->findOneBy(['id' => $id]);
        if ($food) {
            $responseData = $this->serializer->serialize($food, 'json', ['groups' => ['food:read']]);

            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }
        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[OA\Put(
        path: '/api/food/{id}',
        summary: 'Modifier un plat par son ID'
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        required: true,
        description: 'ID du plat à modifier',
        schema: new OA\Schema(type: 'integer')
    )]
     #[OA\RequestBody(
        required: true,
        description: 'Données du plat à modifier',
        content: new OA\JsonContent(
            required: ['title', 'description', 'price', 'createdAt', 'categories'],
            properties: [
                new OA\Property(
                    property: 'title',
                    type: 'string',
                    example: 'Saumon à l\'oseille'
                ),
                new OA\Property(
                    property: 'description',
                    type: 'string',
                    example: 'Saumon à l\'oseille avec son accompagnement de légumes de saison'
                ),
                new OA\Property(
                    property: 'price',
                    type: 'integer',
                    example: 19
                ),
                new OA\Property(
                    property: 'createdAt',
                    type: 'string',
                    format: 'date-time',
                    example: '2023-01-01T00:00:00Z'
                ),
                new OA\Property(
                property: 'categories',
                type: 'array',
                items: new OA\Items(
                    type: 'integer'
                    )
                )
            ]
        )
    )]
    #[OA\Response(
        response: 204,
        description: 'Plat modifié avec succès',
        content: new OA\JsonContent(
            required: ['id', 'title', 'description', 'price', 'createdAt', 'categories'],
            properties: [
                new OA\Property(
                    property: 'id',
                    type: 'integer',
                    example: 1
                ),
                new OA\Property(
                    property: 'title',
                    type: 'string',
                    example: 'Saumon à l\'oseille'
                ),
                new OA\Property(
                    property: 'description',
                    type: 'string',
                    example: 'Saumon à l\'oseille avec son accompagnement de légumes de saison'
                ),
                new OA\Property(
                    property: 'price',
                    type: 'integer',
                    example: 19
                ),
                new OA\Property(
                    property: 'createdAt',
                    type: 'string',
                    format: 'date-time',
                    example: '2023-01-01T00:00:00Z'
                ),
                new OA\Property(
                    property: 'categories',
                    type: 'array',
                    items: new OA\Items(
                        type: 'integer'
                    )
                )
            ]
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'Plat non trouvé'
    )]
    #[Route('/{id}', name: 'edit', methods: ['PUT'])]
    public function edit(int $id, Request $request): JsonResponse
    {
        $food = $this->repository->findOneBy(['id' => $id]);
        if ($food) {
            $food = $this->serializer->deserialize($request->getContent(), Food::class, 'json', 
            ['groups' => ['food:write'], AbstractNormalizer::OBJECT_TO_POPULATE => $food]);
            $food->setUpdatedAt(new DateTimeImmutable());

            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }
        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[OA\Delete(
        path: '/api/food/{id}',
        summary: 'Supprimer un plat par son ID'
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        required: true,
        description: 'ID du plat à supprimer',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 204,
        description: 'Plat supprimé avec succès'
    )]
    #[OA\Response(
        response: 404,
        description: 'Plat non trouvé'
    )]
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $food = $this->repository->findOneBy(['id' => $id]);
        if ($food) {
            $this->manager->remove($food);
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }
        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
}
