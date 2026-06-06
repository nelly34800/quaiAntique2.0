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

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(): JsonResponse
    {
      $picture = $this->repository->findAll();

      $responseData = $this->serializer->serialize($picture, 'json', ['groups' => ['picture:read']]);

      return new JsonResponse($responseData,Response::HTTP_OK,[],true);
    }


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