<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\SearchContentType;
use App\Repository\CategoryRepository;
use App\Repository\IdeaRepository;
use App\Repository\ProjectRepository;
use App\Repository\UserRepository;
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
        UserRepository $userRepository,
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
            $ideas = $ideaRepository->findLikeIdea($search);
            $users = $userRepository->findLikeUser($search);

            return $this->render('/searchResult.html.twig', [
                'users' => $users,
                'projects' => $projects,
                'ideas' => $ideas,
                'form' => $form->createView(),
            ]);
        } else {
            $projects = $projectRepository->findAllProjects('createdAt', 'ASC');
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
                    $projects = $projectRepository->findAllProjects('createdAt', 'DESC');
                    $categories = $categoryRepository->findAll();
                    break;
                case 'oldest':
                    $projects = $projectRepository->findAllProjects('createdAt', 'ASC');
                    $categories = $categoryRepository->findAll();
                    break;
                case 'views':
                    $projects = $projectRepository->findAllProjects('views', 'DESC');
                    $categories = $categoryRepository->findAll();
                    break;
                case 'ideas':
                    $projects = $projectRepository->findAllProjects('ideaCount', 'DESC');
                    $categories = $categoryRepository->findAll();
                    break;
            }
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
}
