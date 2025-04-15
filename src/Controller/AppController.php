<?php

namespace App\Controller;

use PHPUnit\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AppController extends AbstractController
{
    #[Route('/', name: 'app_home_page')]
    public function index(): Response
    {
        $client = HttpClient::create();
        $users = $client->request('GET', $_ENV['API_URL']."/api/users/list");
        return $this->render('home_page/index.html.twig', [
            'controller_name' => 'AppController',
            'users' => json_decode($users->getContent(), true),
        ]);
    }

    #[Route('/create', name: 'create_user', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $form = $this->createFormBuilder()
            ->add('lastName', TextType::class)
            ->add('firstName', TextType::class)
            ->add('email', EmailType::class)
        ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            try
            {
                $client = HttpClient::create();
                $client->request('POST', $_ENV['API_URL']."/api/users", ['json' => $form->getData()]);
                return $this->redirectToRoute('app_home_page');
            }
            catch (\Exception $e)
            {
                Throw New BadRequestException("email already used");
            }
        }

        return $this->render('edit_user/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/edit/{id}', name: 'edit_user', methods: ['GET', 'POST'])]
    public function edit(Request $request,$id): Response
    {
        $client = HttpClient::create();
        $user = $client->request('GET', $_ENV['API_URL']."/api/users/".$id);

        $form = $this->createFormBuilder(json_decode($user->getContent(), true))
            ->add('lastName', TextType::class)
            ->add('firstName', TextType::class)
            ->add('email', EmailType::class)
        ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            try
            {
                $client = HttpClient::create();
                $client->request('PUT', $_ENV['API_URL']."/api/users/".$id, ['json' => $form->getData()]);
                return $this->redirectToRoute('app_home_page');
            }
            catch (\Exception $e)
            {
                Throw New BadRequestException("email already used");
            }
        }

        return $this->render('edit_user/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/delete/{id}', name: 'delete_user', methods: ['POST'])]
    public function delete($id): Response
    {
        $client = HttpClient::create();
        $client->request('DELETE', $_ENV['API_URL']."/api/users/".$id);
        return $this->redirectToRoute('app_home_page');
    }
}
