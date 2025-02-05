<?php

namespace App\Controller\Api;

use App\Entity\ProjectGroup;
use App\Form\ProjectGroupType;
use App\Repository\ProjectGroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/project_group')]
final class ProjectGroupController extends AbstractController
{
    #[Route(name: 'api_project_group_index', methods: ['GET'])]
    public function index(ProjectGroupRepository $projectGroupRepository, SerializerInterface $serializer): JsonResponse
    {
        $projectGroups = $projectGroupRepository->findAll();
        $jsonData = $serializer->serialize($projectGroups, 'json', ['groups' => 'project_group:read']);

        return new JsonResponse($jsonData, Response::HTTP_OK, [], true);
    }

    #[Route('/new', name: 'api_project_group_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        $projectGroup = new ProjectGroup();
        $projectGroup->setCreatedAt(new \DateTime());
        $projectGroup->setUpdatedAt(new \DateTime());
        
        $form = $this->createForm(ProjectGroupType::class, $projectGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($projectGroup);
            $entityManager->flush();

            $jsonData = $serializer->serialize($projectGroup, 'json', ['groups' => 'project_group:read']);

            return new JsonResponse($jsonData, Response::HTTP_OK, [], json: true);
        }

        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $errors[$error->getOrigin()->getName()][] = $error->getMessage();
        }

        return new JsonResponse(['errors' => $errors]);
    }

    #[Route('/{id}', name: 'api_project_group_show', methods: ['GET'])]
    public function show(ProjectGroup $projectGroup, SerializerInterface $serializer): JsonResponse
    {
        $jsonData = $serializer->serialize($projectGroup, 'json', ['groups' => 'project_group:read']);

        return new JsonResponse($jsonData, Response::HTTP_OK, [], true);
    }

    #[Route('/{id}/edit', name: 'api_project_group_edit', methods: ['GET', 'POST', 'PATCH'])]
    public function edit(Request $request, ProjectGroup $projectGroup, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        $form = $this->createForm(ProjectGroupType::class, $projectGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $request->getMethod() === 'PATCH'){
            $projectGroup->setUpdatedAt(new \DateTime());

            if ($form->isValid()) {
                $entityManager->flush();
                $jsonData = $serializer->serialize($projectGroup, 'json', ['groups' => 'project_group:read']);

                return new JsonResponse($jsonData, Response::HTTP_OK, [], true);
        }}

        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $errors[$error->getOrigin()->getName()][] = $error->getMessage();
        }

        return new JsonResponse(['errors' => $errors]);
    }

    #[Route('/{id}/delete', name: 'api_project_group_delete', methods: ['POST', 'DELETE'])]
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