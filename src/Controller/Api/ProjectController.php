<?php

namespace App\Controller\Api;

use App\Entity\Project;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/project')]
final class ProjectController extends AbstractController
{
    #[Route(name: 'api_project_index', methods: ['GET'])]
    public function index(ProjectRepository $projectRepository, SerializerInterface $serializer): JsonResponse
    {
        $projects = $projectRepository->findAll();

        foreach ($projects as $project) {
            if ($project->getProjectGroup()) {
                $project->getProjectGroup()->getName();
            }
        }

        $jsonData = $serializer->serialize($projects, 'json', ['groups' => 'project:read']);

        return new JsonResponse($jsonData, Response::HTTP_OK, [], true);
    }

    #[Route('/new', name: 'api_project_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        $project = new Project();
        $project->setCreatedAt(new \DateTime());
        $project->setUpdatedAt(new \DateTime());

        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($project);
            $entityManager->flush();

            $jsonData = $serializer->serialize($project, 'json', ['groups' => 'project:read']);

            return new JsonResponse($jsonData, Response::HTTP_OK, [], true);
        }

        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $errors[$error->getOrigin()->getName()][] = $error->getMessage();
        }

        return new JsonResponse(['data' => $errors]);
    }

    #[Route('/{id}', name: 'api_project_show', methods: ['GET'])]
    public function show(Project $project, SerializerInterface $serializer): JsonResponse
    {
        if ($project->getProjectGroup()) {
            $project->getProjectGroup()->getName();
        }

        $jsonData = $serializer->serialize($project, 'json', ['groups' => 'project:read']);
    
        return new JsonResponse($jsonData, Response::HTTP_OK, [], true);
    }

    #[Route('/{id}/edit', name: 'api_project_edit', methods: ['GET', 'POST', 'PATCH'])]
    public function edit(Request $request, Project $project, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $request->isMethod('PATCH')) {
            $project->setUpdatedAt(new \DateTime());
            
            if ($form->isValid()) {
                $entityManager->flush();
                $jsonData = $serializer->serialize($project, 'json', ['groups' => 'project:read']);

                return new JsonResponse($jsonData, Response::HTTP_OK, [], true);
            }
        }

        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $errors[$error->getOrigin()->getName()][] = $error->getMessage();
        }
    
        return new JsonResponse(['data' => $errors]);
    }
    
    

    #[Route('/{id}/delete', name: 'api_project_delete', methods: ['POST', 'DELETE'])]
    public function delete(Request $request, Project $project, EntityManagerInterface $entityManager): JsonResponse
    {
        if ($request->getMethod() == 'DELETE' && $this->isCsrfTokenValid('delete'.$project->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($project);
            $entityManager->flush();
            
            return new JsonResponse(['deleted successfully']);
        }

        return new JsonResponse(['something went wrong!']);
    }
}