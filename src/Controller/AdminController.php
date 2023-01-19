<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Idea;
use App\Repository\ProjectRepository;
use App\Repository\IdeaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/Admin')]
class AdminController extends AbstractController
{
//    ---------------------------------- Project Routes ---------------------------------------------------
    #[Route('/project_list', name: 'app_admin_project_list', methods: ['GET'])]
    public function projectList(
        ProjectRepository $projectRepository,
    ): Response {
        $projects = $projectRepository->findAll();
        return $this->render('admin/admin_project_list.html.twig', [
            'projects' => $projects,
        ]);
    }

    #[Route('/project_view/{id}', name: 'app_admin_project_view', methods: ['GET'])]
    public function projectView(
        Project $project,
    ): Response {

        return $this->render('admin/admin_project_view.html.twig', [
            'project' => $project,
        ]);
    }

    #[Route('/delete_project/{id}', name: 'app_admin_project_delete', methods: ['POST'])]
    public function projectDelete(Request $request, Project $project, ProjectRepository $projectRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $project->getId(), $request->request->get('_token'))) {
            $projectRepository->remove($project, true);
        }

        return $this->redirectToRoute('app_admin_project_list', [], Response::HTTP_SEE_OTHER);
    }

    //    ---------------------------------------- Idea Routes -------------------------------------------------------
    #[Route('/idea_list', name: 'app_admin_idea_list', methods: ['GET'])]
    public function ideaList(
        IdeaRepository $ideaRepository,
    ): Response {
        $ideas = $ideaRepository->findAll();
        return $this->render('admin/admin_idea_list.html.twig', [
            'ideas' => $ideas,
        ]);
    }

    #[Route('/idea_view/{id}', name: 'app_admin_idea_view', methods: ['GET'])]
    public function ideaView(
        Idea $idea,
    ): Response {
        return $this->render('admin/admin_idea_view.html.twig', [
            'idea' => $idea,
        ]);
    }

    #[Route('/delete_idea/{id}', name: 'app_admin_idea_delete', methods: ['POST'])]
    public function ideaDelete(Request $request, Idea $idea, IdeaRepository $ideaRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $idea->getId(), $request->request->get('_token'))) {
            $ideaRepository->remove($idea, true);
        }

        return $this->redirectToRoute('app_admin_idea_list', [], Response::HTTP_SEE_OTHER);
    }
}
