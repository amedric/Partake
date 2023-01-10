<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Idea;
use App\Entity\User;
use App\Form\Project1Type;
use App\Form\SearchContentType;
use App\Form\EditProjectType;
use App\Form\ProjectEditType;
use App\Repository\CategoryRepository;
use App\Repository\IdeaRepository;
use App\Repository\ProjectRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/project')]
class ProjectController extends AbstractController
{
    #[Route('/', name: 'app_project_index', methods: ['GET'])]
    public function index(
        Request $request,
        User $user,
        UserRepository $userRepository,
        ProjectRepository $projectRepository,
        CategoryRepository $categoryRepository,
    ): Response {
        $form = $this->createForm(SearchContentType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $search = $form->getData()['search'];
            $projects = $projectRepository->findLikeProject($search);
            $users = $userRepository->findLikeUser($search);
            $categories = $categoryRepository->findAll();
        } else {
            $projects = $projectRepository->findAll();
            $categories = $categoryRepository->findAll();
            $users = null;
        }
        return $this->render('project/index.html.twig', [
            'user' => $user,
            'users' => $users,
            'projects' => $projects,
            'categories' => $categories,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/new', name: 'app_project_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ProjectRepository $projectRepository): Response
    {
        $project = new Project();
        $form = $this->createForm(Project1Type::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $projectRepository->save($project, true);

            return $this->redirectToRoute('app_project_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('project/new.html.twig', [
            'project' => $project,
            'form' => $form,
            'edit' => true,
        ]);
    }

    #[Route('/{id}', name: 'app_project_show', methods: ['GET'])]
    public function show(
        Project $project,
        IdeaRepository $ideaRepository,
        ProjectRepository $projectRepository
    ): Response {

        $ideas = $ideaRepository->findBy(['project' => $project->getId()]);
        $project->setProjectViews($project->getProjectViews() + 1);
        $projectRepository->save($project, true);

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

    #[Route('/{id}', name: 'app_project_delete', methods: ['POST'])]
    public function delete(Request $request, Project $project, ProjectRepository $projectRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $project->getId(), $request->request->get('_token'))) {
            $projectRepository->remove($project, true);
        }

        return $this->redirectToRoute('app_project_index', [], Response::HTTP_SEE_OTHER);
    }
}
