<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class UserController extends AbstractController
{
    public function list(UserRepository $userRepository): JsonResponse
    {
        $userList = $userRepository->findAll();
        return $this->json($userList);
    }

    public function read(UserRepository $userRepository,$id): JsonResponse
    {
        try
        {
            $user = $userRepository->findOneById($id);
        }
        catch (NoResultException)
        {
            throw new NotFoundHttpException('User not found');
        }

        return $this->json($user);
    }

    public function create(UserRepository $userRepository,Request $request, SerializerInterface $serializer,ValidatorInterface $validator): JsonResponse
    {
        if ('json' !== $request->getContentTypeFormat())
        {
            throw new BadRequestException('The format of the request is not supported , this api only supports json');
        }

        $user = $serializer->deserialize($request->getContent(), User::class, 'json');

        $user->setCreationDate(New DateTime());
        $errors = $validator->validate($user);

        if (count($errors) > 0)
        {
            throw new BadRequestException((string) $errors);
        }

        $userCreated = $userRepository->create($user);

        return $this->json($userCreated);
    }

    public function update(UserRepository $userRepository,Request $request,ValidatorInterface $validator,$id): JsonResponse
    {
        if ('json' !== $request->getContentTypeFormat())
        {
            throw new BadRequestException('The format of the request is not supported , this api only supports json');
        }

        $data = json_decode($request->getContent(),true);

        try
        {
            $user = $userRepository->findOneById($id);
        }
        catch (NoResultException)
        {
            throw new NotFoundHttpException('User not found');
        }

        foreach($data as $key => $value)
        {
            switch ($key)
            {
                case 'firstName':
                    $user->setFirstName($value);
                    break;
                case 'lastName':
                    $user->setLastName($value);
                    break;
                case 'email':
                    $user->setEmail($value);
                    break;
            }
        }

        $user->setLastUpdateDate(New DateTime());
        $errors = $validator->validate($user);

        if (count($errors) > 0)
        {
            throw new BadRequestException((string) $errors);
        }

        $userUpdated = $userRepository->update($user);

        return $this->json($userUpdated);
    }

    public function delete(UserRepository $userRepository,$id): JsonResponse
    {
        try
        {
            $user = $userRepository->findOneById($id);
        }
        catch (NoResultException)
        {
            throw new NotFoundHttpException('User not found');
        }

        $userRepository->delete($user);

        return $this->json("",204);
    }
}
