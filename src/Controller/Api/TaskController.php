<?php

namespace App\Controller\Api;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/task')]
final class TaskController extends AbstractController
{
    #[Route(name: 'app_task_index', methods: ['GET'])]
    public function index(TaskRepository $taskRepository, SerializerInterface $serializer): JsonResponse
    {
        $tasks = $taskRepository->findAll();
        $jsonData = $serializer->serialize($tasks, 'json', ['groups' => 'task:read']);

        return new JsonResponse($jsonData, Response::HTTP_OK, [], true);
    }

    #[Route('/new', name: 'app_task_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($task);
            $entityManager->flush();

            $jsonData = $serializer->serialize($task, 'json', ['groups' => 'task:read']);

            return new JsonResponse($jsonData, Response::HTTP_OK, [], true);
        }

        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $errors[$error->getOrigin()->getName()][] = $error->getMessage();
        }

        return new JsonResponse(['errors' => $errors]);
    }

    #[Route('/{id}', name: 'app_task_show', methods: ['GET'])]
    public function show(Task $task, SerializerInterface $serializer): JsonResponse
    {
        $jsonData = $serializer->serialize($task, 'json', ['groups' => 'task:read']);

        return new JsonResponse($jsonData, Response::HTTP_OK, [], true);
    }

    #[Route('/{id}/edit', name: 'app_task_edit', methods: ['GET', 'POST', 'PATCH'])]
    public function edit(Request $request, Task $task, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $request->getMethod() === 'PATCH') {
            $entityManager->flush();

            $jsonData = $serializer->serialize($task, 'json', ['groups' => 'task:read']);

            return new JsonResponse($jsonData, Response::HTTP_OK, [], true);
        }

        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $errors[$error->getOrigin()->getName()][] = $error->getMessage();
        }

        return new JsonResponse(['errors' => $errors]);
    }

    #[Route('/{id}', name: 'app_task_delete', methods: ['POST', 'DELETE'])]
    public function delete(Request $request, Task $task, EntityManagerInterface $entityManager): JsonResponse
    {
        if ($request->getMethod() == 'DELETE' && $this->isCsrfTokenValid('delete'.$task->getId(), $request->get('_token'))) {
            $entityManager->remove($task);
            $entityManager->flush();

            return new JsonResponse(['deleted successfully']);
        }

        return new JsonResponse(['something went wrong!']);
    }
}