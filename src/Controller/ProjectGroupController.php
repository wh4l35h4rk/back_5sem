<?php

namespace App\Controller;

use App\Entity\ProjectGroup;
use App\Form\ProjectGroupType;
use App\Repository\ProjectGroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/project_group')]
final class ProjectGroupController extends AbstractController
{
    #[Route(name: 'app_project_group_index', methods: ['GET'])]
    public function index(ProjectGroupRepository $projectGroupRepository): Response
    {
        $projectGroups = $projectGroupRepository->findAll();

        return $this->render('project_group/index.html.twig', [
            'project_groups' => $projectGroups,
        ]);
    }

    #[Route('/new', name: 'app_project_group_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $projectGroup = new ProjectGroup();
        $projectGroup->setCreatedAt(new \DateTime());
        $projectGroup->setUpdatedAt(new \DateTime());

        $form = $this->createForm(ProjectGroupType::class, $projectGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($projectGroup);
            $entityManager->flush();

            return $this->redirectToRoute('app_project_group_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('project_group/new.html.twig', [
            'project_group' => $projectGroup,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_project_group_show', methods: ['GET'])]
    public function show(ProjectGroup $projectGroup): Response
    {
        return $this->render('project_group/show.html.twig', [
            'project_group' => $projectGroup,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_project_group_edit', methods: ['GET', 'POST', 'PATCH'])]
    public function edit(Request $request, ProjectGroup $projectGroup, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProjectGroupType::class, $projectGroup);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $request->getMethod() === 'PATCH'){
            $projectGroup->setUpdatedAt(new \DateTime());

            if ($form->isValid()) {
                $entityManager->flush();

                return $this->redirectToRoute('app_project_group_index', [], Response::HTTP_SEE_OTHER);
        }}

        return $this->render('project_group/edit.html.twig', [
            'project_group' => $projectGroup,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_project_group_delete', methods: ['POST', 'DELETE'])]
    public function delete(Request $request, ProjectGroup $projectGroup, EntityManagerInterface $entityManager): Response
    {
        if ($request->getMethod() == 'DELETE' && $this->isCsrfTokenValid('delete'.$projectGroup->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($projectGroup);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_project_group_index', [], Response::HTTP_SEE_OTHER);
    }
}
