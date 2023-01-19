<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\User;
use App\Form\SearchContentType;
use App\Repository\CategoryRepository;
use App\Repository\ProjectRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/Admin')]
class AdminController extends AbstractController
{

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
}
