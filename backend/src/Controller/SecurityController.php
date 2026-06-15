<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response, JsonResponse};
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use OpenApi\Attributes as OA;

#[Route('/api', name: 'app_api_')]
class SecurityController extends AbstractController
{
    public function __construct(private EntityManagerInterface $manager, private SerializerInterface $serializer)
    {
    }

   #[OA\Post(
        path: '/api/registration',
        summary: 'Inscription d\'un nouvel utilisateur'
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['firstName', 'lastName', 'email', 'password'],
            properties: [
              new OA\Property(
                    property: 'firstName',
                    type: 'string',
                    example: 'John'
                ),
                new OA\Property(
                    property: 'lastName',
                    type: 'string',
                    example: 'Doe'
                ),
                new OA\Property(
                    property: 'email',
                    type: 'string',
                    example: 'test3@test3.fr'
                ),
                new OA\Property(
                    property: 'password',
                    type: 'string',
                    example: 'motdepasse123'
                )
            ]
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Utilisateur inscrit avec succès'
    )]
    #[Route('/registration', name: 'registration', methods: ['POST'])]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $user = $this->serializer->deserialize($request->getContent(), User::class, 'json');

        $user->setPassword($passwordHasher->hashPassword($user, $user->getPassword()));

        $user->setCreatedAt(new DateTimeImmutable());

        $this->manager->persist($user);
        $this->manager->flush();

        return new JsonResponse(['user' => $user->getUserIdentifier(), 'apiToken' => $user->getApiToken(), 
        'roles' => $user->getRoles()], Response::HTTP_CREATED );
    }
    #[OA\Post(
        path: '/api/login',
        summary: 'Connecter un utilisateur'
    )]
    #[OA\RequestBody(
        required: true,
        description: 'Données de l\'utilisateur pour se connecter',
        content: new OA\JsonContent(
            required: ['email', 'password'],
            properties: [
                new OA\Property(
                    property: 'email',
                    type: 'string',
                    example: 'test3@test3.fr'
                ),
                new OA\Property(
                    property: 'password',
                    type: 'string',
                    example: 'motdepasse123'
                )
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Connexion réussie',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'user',
                    type: 'string',
                    example: 'Nom de l\'utilisateur'
                ),
                new OA\Property(
                    property: 'apiToken',
                    type: 'string',
                    example: '31a023e212f116124a36af14ea0c1c3806eb9378'
                ),
                new OA\Property(
                    property: 'roles',
                    type: 'array',
                    items: new OA\Items(type: 'string', example: 'ROLE_USER')
                )
            ]
        )
    )]
    #[Route('/login', name: 'login', methods: 'POST' )]
     public function login(#[CurrentUser] ?User $user): JsonResponse
    {
        if(null ===$user) {
          return new JsonResponse(['message'  => 'Missing credentials'], Response::HTTP_UNAUTHORIZED);
        }

        return new JsonResponse([
          'user'  => $user->getUserIdentifier(),
          'apiToken' => $user->getApiToken(),
          'roles' => $user->getRoles()
          ]);
    }
}
