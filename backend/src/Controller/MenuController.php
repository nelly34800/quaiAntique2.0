<?php

namespace App\Controller;

use App\Entity\Menu;
use App\Repository\MenuRepository;
use App\Repository\FoodRepository;
use Doctrine\ORM\EntityManagerInterface;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use OpenApi\Attributes as OA;

 #[Route('/api/menu', name: 'app_api_menu_')]
class MenuController extends AbstractController
{
    public function __construct(
        private MenuRepository $repository,
        private FoodRepository $foodRepository,
        private EntityManagerInterface $manager,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    #[OA\Post(
        path: '/api/menu',
        summary: 'Créer un nouveau menu'
    )]
    #[OA\RequestBody(
        required: true,
        description: 'Données du menu à créer',
        content: new OA\JsonContent(
            required: ['id', 'title', 'description', 'image', 'createdAt', 'foods'],
            properties: [
                new OA\Property(
                    property: 'id',
                    type: 'integer',
                    example: 1
                ),
                new OA\Property(
                        property: 'title',
                        type: 'string',
                        example: 'menu classique'
                ),
                new OA\Property(
                        property: 'description',
                        type: 'string',
                        example: 'Plats gourmand avec des produits frais et de saison'
                ),
                new OA\Property(
                        property: 'image',
                        type: 'string',
                        example: 'saumon.jpg'
                ),
                new OA\Property(
                    property: 'createdAt',
                    type: 'string',
                    format: 'date-time',
                    example: '2023-01-01T00:00:00Z'
                ),
                new OA\Property(
                    property: 'foods',
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
        description: 'carte créé avec succès',
    )]
    #[Route(methods: ['POST'])]
    public function new(Request $request): JsonResponse
    {
        $menu = $this->serializer->deserialize($request->getContent(), Menu::class, 'json', ['groups' => ['menu:write']]);
        $menu->setCreatedAt(new \DateTimeImmutable());

        $data = json_decode($request->getContent(), true);

        if (!isset($data['foods']) || empty($data['foods'])) {
            return new JsonResponse(['message' => 'Foods required'],Response::HTTP_BAD_REQUEST);
        }

        foreach ($data['foods'] as $foodId) {
            $food = $this->foodRepository->find($foodId);

            if ($food) {
                $menu->addFood($food);
            }
        }

        $this->manager->persist($menu);
        $this->manager->flush(); 

        $responseData = $this->serializer->serialize($menu, 'json', ['groups' => ['menu:read']]);
        $location = $this->urlGenerator->generate(
        'app_api_menu_show',
        ['id' => $menu->getId()],
        UrlGeneratorInterface::ABSOLUTE_URL,
        );

        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[OA\Get(
        path: '/api/menu',
        summary: 'Afficher tous les menus'
    )]
    #[OA\Response(
        response: 200,
        description: 'Menus trouvés avec succès',
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
                        example: 'menu classique'
                    ),
                    new OA\Property(
                        property: 'description',
                        type: 'string',
                        example: 'Plats gourmand avec des produits frais et de saison'
                    ),
                    new OA\Property(
                        property: 'image',
                        type: 'string',
                        example: 'saumon.jpg'
                    ),
                    new OA\Property(
                        property: 'createdAt',
                        type: 'string',
                        format: 'date-time',
                        example: '2023-01-01T00:00:00Z'
                    ),
                    new OA\Property(
                        property: 'foods',
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
                                    example: 'Saumon à l\'oseille'
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
      $menu = $this->repository->findAll();

      $responseData = $this->serializer->serialize($menu, 'json', ['groups' => ['menu:read']]);

      return new JsonResponse($responseData,Response::HTTP_OK,[],true);
    }

    #[OA\Get(
        path: '/api/menu/{id}',
        summary: 'Afficher un menu par son ID'
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        required: true,
        description: 'ID du menu à afficher',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 200,
        description: 'Menu trouvé avec succès',
        content: new OA\JsonContent(
            required: ['id', 'title', 'description', 'image', 'createdAt', 'foods'],
            properties: [
                new OA\Property(
                    property: 'id',
                    type: 'integer',
                    example: 1
                ),
                new OA\Property(
                        property: 'title',
                        type: 'string',
                        example: 'menu classique'
                ),
                new OA\Property(
                    property: 'description',
                    type: 'string',
                    example: 'Plats gourmand avec des produits frais et de saison'
                ),
                new OA\Property(
                        property: 'image',
                        type: 'string',
                        example: 'saumon.jpg'
                ),
                new OA\Property(
                    property: 'createdAt',
                    type: 'string',
                    format: 'date-time',
                    example: '2023-01-01T00:00:00Z'
                ),
                new OA\Property(
                    property: 'foods',
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
                                example: 'Saumon à l\'oseille'
                            )
                        ]
                    )
                )
            ]
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'Menu non trouvé'
    )]
    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $menu = $this->repository->findOneBy(['id' => $id]);
        if ($menu) {
            $responseData = $this->serializer->serialize($menu, 'json', ['groups' => ['menu:read']]);

            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }
        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[OA\Put(
        path: '/api/menu/{id}',
        summary: 'Modifier un menu par son ID'
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        required: true,
        description: 'ID du menu à modifier',
        schema: new OA\Schema(type: 'integer')
    )]
     #[OA\RequestBody(
        required: true,
        description: 'Données du menu à modifier',
        content: new OA\JsonContent(
            required: ['title', 'description', 'image', 'createdAt', 'foods'],
            properties: [
                new OA\Property(
                    property: 'title',
                    type: 'string',
                    example: 'menu classique'
                ),
                new OA\Property(
                    property: 'description',
                    type: 'string',
                    example: 'Plats gourmand avec des produits frais et de saison'
                ),
                new OA\Property(
                    property: 'image',
                    type: 'string',
                    example: 'saumon.jpg'
                ),
                new OA\Property(
                    property: 'createdAt',
                    type: 'string',
                    format: 'date-time',
                    example: '2023-01-01T00:00:00Z'
                    ),
                new OA\Property(
                    property: 'foods',
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
        description: 'Menu modifié avec succès',
        content: new OA\JsonContent(
            required: ['id', 'title', 'description', 'image','createdAt', 'foods'],
            properties: [
                new OA\Property(
                    property: 'id',
                    type: 'integer',
                    example: 1
                ),
                new OA\Property(
                    property: 'title',
                    type: 'string',
                    example: 'menu classique'
                ),
                new OA\Property(
                    property: 'description',
                    type: 'string',
                    example: 'Plats gourmand avec des produits frais et de saison'
                ),
                new OA\Property(
                    property: 'image',
                    type: 'string',
                    example: 'saumon.jpg'
                ),
                new OA\Property(
                    property: 'createdAt',
                    type: 'string',
                    format: 'date-time',
                    example: '2023-01-01T00:00:00Z'
                ),
                new OA\Property(
                    property: 'foods',
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
        description: 'Menu non trouvé'
    )]
    #[Route('/{id}', name: 'edit', methods: ['PUT'])]
    public function edit(int $id, Request $request): JsonResponse
    {
        $menu = $this->repository->findOneBy(['id' => $id]);
        if ($menu) {
            $menu = $this->serializer->deserialize($request->getContent(), Menu::class, 'json', 
            ['groups' => ['menu:write'], AbstractNormalizer::OBJECT_TO_POPULATE => $menu]);
            $menu->setUpdatedAt(new DateTimeImmutable());

            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }
        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[OA\Delete(
        path: '/api/menu/{id}',
        summary: 'Supprimer un menu par son ID'
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        required: true,
        description: 'ID du menu à supprimer',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 204,
        description: 'Menu supprimé avec succès'
    )]
    
    #[OA\Response(
        response: 404,
        description: 'Menu non trouvé'
    )]
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $menu = $this->repository->findOneBy(['id' => $id]);
        if ($menu) {
            $this->manager->remove($menu);
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }
        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
}