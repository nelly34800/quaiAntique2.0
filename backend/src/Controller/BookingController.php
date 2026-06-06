<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Repository\BookingRepository;
use App\Repository\RestaurantRepository;
use Doctrine\ORM\EntityManagerInterface;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;


#[Route('/api/booking', name: 'app_api_booking_')]
class BookingController extends AbstractController
{
    public function __construct(
        private BookingRepository $repository,
        private RestaurantRepository $restaurantRepository,
        private EntityManagerInterface $manager,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

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

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(): JsonResponse
    {
      $booking = $this->repository->findAll();

      $responseData = $this->serializer->serialize($booking, 'json', ['groups' => ['booking:read']]);

      return new JsonResponse($responseData,Response::HTTP_OK,[],true);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $booking = $this->repository->findOneBy(['id' => $id]);
        if ($booking) {
            $responseData = $this->serializer->serialize($booking, 'json', ['groups' => ['booking:read']]);

            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }
        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

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