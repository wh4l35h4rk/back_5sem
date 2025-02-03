<?php

namespace App\Controller;

use App\Entity\Project;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/project')]
final class ProjectController extends AbstractController
{
    #[Route(name: 'app_project_index', methods: ['GET'])]
    public function index(ProjectRepository $projectRepository): JsonResponse
    {
        $projects_all = $projectRepository->findAll();

        $this->render('project/index.html.twig', [
            'projects' => $projects_all,
        ]);

        return new JsonResponse(['data' => $projects_all]);
    }

    #[Route('/new', name: 'app_project_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($project);
            $entityManager->flush();

            return new JsonResponse(['data' => $project]);
        }

        $this->render('project/new.html.twig', [
            'project' => $project,
            'form' => $form,
        ]);

        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $errors[$error->getOrigin()->getName()][] = $error->getMessage();
        }

        return new JsonResponse(['data' => $errors]);
    }

    #[Route('/{id}', name: 'app_project_show', methods: ['GET'])]
    public function show(Project $project): JsonResponse
    {
        $this->render('project/show.html.twig', [
            'project' => $project,
        ]);

        return new JsonResponse(['data' => $project]);
    }

    #[Route('/{id}/edit', name: 'app_project_edit', methods: ['GET', 'POST', 'PATCH'])]
    public function edit(Request $request, Project $project, EntityManagerInterface $entityManager): JsonResponse
    {
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            if ($request->getMethod() === 'PATCH') {
                $entityManager->flush();
                return new JsonResponse(['data' => $project]);
            }
        }

        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $errors[$error->getOrigin()->getName()][] = $error->getMessage();
        }
    
        return new JsonResponse(['data' => $errors]);
    }
    
    

    #[Route('/{id}', name: 'app_project_delete', methods: ['POST', 'DELETE'])]
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
