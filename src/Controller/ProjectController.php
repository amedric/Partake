<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Idea;
use App\Entity\User;
use App\Form\Project1Type;
use App\Form\ProjectEditType;
use App\Repository\IdeaRepository;
use App\Repository\ProjectRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/project')]
class ProjectController extends AbstractController
{

    #[Route('/new', name: 'app_project_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ProjectRepository $projectRepository): Response
    {
        $project = new Project();
        $form = $this->createForm(Project1Type::class, $project);
        $form->handleRequest($request);

        /** @var User $user */
        $user = $this->getUser();


        if ($form->isSubmitted() && $form->isValid()) {
            $project->setUser($user);
            $today = new DateTime();
            $project->setCreatedAt($today);
            $projectRepository->save($project, true);

            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('project/new.html.twig', [
            'project' => $project,
            'form' => $form,
            'edit' => true,
        ]);
    }

    #[Route('/{id}/{orderBy}', name: 'app_project_show', methods: ['GET', 'POST'])]
    public function show(
        Project $project,
        IdeaRepository $ideaRepository,
        ProjectRepository $projectRepository,
        string $orderBy
    ): Response {
        $ideas = $ideaRepository->findBy(['project' => $project->getId()]);

        switch ($orderBy) {
            case 'show':
                $ideas = $ideaRepository->findBy(['project' => $project->getId()]);
                $project->setProjectViews($project->getProjectViews() + 1);
                $projectRepository->save($project, true);
                break;
            case 'newest':
                $ideas = $ideaRepository->findBy(['project' => $project->getId()], ['createdAt' => 'DESC']);
                break;
            case 'oldest':
                $ideas = $ideaRepository->findBy(['project' => $project->getId()], ['createdAt' => 'ASC']);
                break;
            case 'likes':
                $ideas = $projectRepository->findIdeasCountLikes(
                    $project->getId()
                );
                break;
            case 'comments':
                $ideas = $projectRepository->findIdeasCountComments(
                    $project->getId()
                );
                break;
            case 'views':
                $ideas = $ideaRepository->findBy(['project' => $project->getId()], ['ideaViews' => 'Desc']);
                break;
        }

        return $this->render('project/show.html.twig', [
            'project' => $project,
            'ideas' => $ideas,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_project_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Project $project, ProjectRepository $projectRepository): Response
    {
        $form = $this->createForm(ProjectEditType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $projectRepository->save($project, true);

            return $this->redirectToRoute('app_project_show', [
                'id' => $project->getId(),
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('project/edit.html.twig', [
            'project' => $project,
            'form' => $form,
            'edit' => true,
        ]);
    }

    #[Route('/{id}/show/archived', name: 'app_project_archived', methods: ['GET', 'POST'])]
    public function archive(Request $request, Project $project, ProjectRepository $projectRepository): Response
    {
        if ($this->isCsrfTokenValid('archive' . $project->getId(), $request->request->get('_token'))) {
            $project->setIsArchived(true);
            $projectRepository->save($project, true);
        }

        return $this->redirectToRoute('app_project_show', [
            'id' => $project->getId(),
            'orderBy' => 'show'
        ], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/show/unarchived', name: 'app_project_unarchived', methods: ['GET', 'POST'])]
    public function unarchive(Request $request, Project $project, ProjectRepository $projectRepository): Response
    {
        if ($this->isCsrfTokenValid('unarchive' . $project->getId(), $request->request->get('_token'))) {
            $project->setIsArchived(false);
            $projectRepository->save($project, true);
        }

        return $this->redirectToRoute('app_project_show', [
            'id' => $project->getId(),
            'orderBy' => 'show'
        ], Response::HTTP_SEE_OTHER);
    }
}
