<?php

namespace App\Controller;

use App\Entity\Idea;
use App\Entity\Project;
use App\Form\SearchContentType;
use App\Repository\CategoryRepository;
use App\Repository\IdeaRepository;
use App\Repository\ProjectRepository;
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

        return $this->render('home/home.html.twig', [
            'projects' => $projects,
            'categories' => $categories,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{orderBy}', name: 'app_home_orderBy')]
    public function orderBy(
        Request $request,
        ProjectRepository $projectRepository,
        CategoryRepository $categoryRepository,
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
        return $this->render('home/home.html.twig', [
            'projects' => $projects,
            'categories' => $categories,
            'form' => $form->createView(),
        ]);
    }
}
