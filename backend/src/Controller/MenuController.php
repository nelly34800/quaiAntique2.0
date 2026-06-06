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

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(): JsonResponse
    {
      $menu = $this->repository->findAll();

      $responseData = $this->serializer->serialize($menu, 'json', ['groups' => ['menu:read']]);

      return new JsonResponse($responseData,Response::HTTP_OK,[],true);
    }

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
