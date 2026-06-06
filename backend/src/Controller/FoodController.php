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

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(): JsonResponse
    {
      $food = $this->repository->findAll();

      $responseData = $this->serializer->serialize($food, 'json', ['groups' => ['food:read']]);

      return new JsonResponse($responseData,Response::HTTP_OK,[],true);
    }


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
