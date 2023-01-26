<?php

namespace App\Controller;

use App\Form\SearchContentType;
use App\Repository\CategoryRepository;
use App\Repository\IdeaRepository;
use App\Repository\ProjectRepository;
use App\Service\ChartStats;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/home', name: '')]
class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        Request $request,
        ProjectRepository $projectRepository,
        CategoryRepository $categoryRepository,
        IdeaRepository $ideaRepository,
        ChartStats $chartStats
    ): Response {
        $form = $this->createForm(SearchContentType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $search = $form->getData()['search'];
            $projects = $projectRepository->findLikeProject($search);
            $categories = $categoryRepository->findAll();
        } else {
            $projects = $ideaRepository->findIdeasCount();
            $categories = $categoryRepository->findAll();
        }
        $projectChart1 = $chartStats->getMobileProjectChart1();
        $projectChart2 = $chartStats->getMobileProjectChart2();
        $ideaChart1 = $chartStats->getMobileIdeaChart1();
        $ideaChart2 = $chartStats->getMobileIdeaChart2();
        $authorizedProjects = $projectRepository->findProjectAuthorizedForUser($this->getUser()->getId());

        return $this->render('home/home.html.twig', [
            'projects' => $projects,
            'categories' => $categories,
            'form' => $form->createView(),
            'projectChart1' => $projectChart1,
            'ideaChart1' => $ideaChart1,
            'projectChart2' => $projectChart2,
            'ideaChart2' => $ideaChart2,
            "authorizedProjects" => $authorizedProjects,
        ]);
    }

    #[Route('/{orderBy}', name: 'app_home_orderBy')]
    public function orderBy(
        Request $request,
        ProjectRepository $projectRepository,
        CategoryRepository $categoryRepository,
        ChartStats $chartStats,
        string $orderBy,
    ): Response {
        $form = $this->createForm(SearchContentType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $search = $form->getData()['search'];
            $projects = $projectRepository->findLikeProject($search);
            $categories = $categoryRepository->findAll();
        } else {
            switch ($orderBy) {
                case 'newest':
                    $projects = $projectRepository->findProjectDesc();
                    $categories = $categoryRepository->findAll();
                    break;
                case 'oldest':
                    $projects = $projectRepository->findProjectAsc();
                    $categories = $categoryRepository->findAll();
                    break;
                case 'views':
                    $projects = $projectRepository->findProjectViewsDesc();
                    $categories = $categoryRepository->findAll();
                    break;
            }
        }
        $projectChart1 = $chartStats->getMobileProjectChart1();
        $projectChart2 = $chartStats->getMobileProjectChart2();
        $ideaChart1 = $chartStats->getMobileIdeaChart1();
        $ideaChart2 = $chartStats->getMobileIdeaChart2();

        return $this->render('home/home.html.twig', [
            'projects' => $projects,
            'categories' => $categories,
            'form' => $form->createView(),
            'projectChart1' => $projectChart1,
            'ideaChart1' => $ideaChart1,
            'projectChart2' => $projectChart2,
            'ideaChart2' => $ideaChart2,
        ]);
    }
}
