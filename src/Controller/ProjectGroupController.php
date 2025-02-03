<?php

namespace App\Controller;

use App\Entity\ProjectGroup;
use App\Form\ProjectGroupType;
use App\Repository\ProjectGroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/project/group')]
final class ProjectGroupController extends AbstractController
{
    #[Route(name: 'app_project_group_index', methods: ['GET'])]
    public function index(ProjectGroupRepository $projectGroupRepository): JsonResponse
    {
        $project_groups = $projectGroupRepository->findAll();

        $this->render('project_groups/index.html.twig', [
            'projects' => $project_groups,
        ]);

        return new JsonResponse(['data' => $project_groups]);
    }

    #[Route('/new', name: 'app_project_group_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $projectGroup = new ProjectGroup();
        $form = $this->createForm(ProjectGroupType::class, $projectGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($projectGroup);
            $entityManager->flush();

            return new JsonResponse(['data' => $projectGroup]);
        }

        $this->render('project/new.html.twig', [
            'project' => $projectGroup,
            'form' => $form,
        ]);

        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $errors[$error->getOrigin()->getName()][] = $error->getMessage();
        }

        return new JsonResponse(['errors' => $errors]);
    }

    #[Route('/{id}', name: 'app_project_group_show', methods: ['GET'])]
    public function show(ProjectGroup $projectGroup): JsonResponse
    {
        $this->render('project/show.html.twig', [
            'project' => $projectGroup,
        ]);

        return new JsonResponse(['data' => $projectGroup]);
    }

    #[Route('/{id}/edit', name: 'app_project_group_edit', methods: ['GET', 'POST', 'PATCH'])]
    public function edit(Request $request, ProjectGroup $projectGroup, EntityManagerInterface $entityManager): JsonResponse
    {
        $form = $this->createForm(ProjectGroupType::class, $projectGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $request->getMethod() === 'PATCH') {
            $entityManager->flush();

            return new JsonResponse(['data' => $projectGroup]);
        }

        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $errors[$error->getOrigin()->getName()][] = $error->getMessage();
        }

        return new JsonResponse(['errors' => $errors]);
    }

    #[Route('/{id}', name: 'app_project_group_delete', methods: ['POST', 'DELETE'])]
    public function delete(Request $request, ProjectGroup $projectGroup, EntityManagerInterface $entityManager): JsonResponse
    {
        if ($request->getMethod() == 'DELETE' && $this->isCsrfTokenValid('delete'.$projectGroup->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($projectGroup);
            $entityManager->flush();

            return new JsonResponse(['deleted successfully']);
        }

        return new JsonResponse(['something went wrong!']);
    }
}
