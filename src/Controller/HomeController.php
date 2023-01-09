<?php

namespace App\Controller;

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
    ): Response {
        $form = $this->createForm(SearchContentType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $search = $form->getData()['search'];
            $project = $projectRepository->findLikeProject($search);
            $category = $categoryRepository->findAll();
        } else {
            $project = $projectRepository->findAll();
            $category = $categoryRepository->findAll();
        }
        return $this->render('home/home.html.twig', [
            'project' => $project,
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }


    #[Route('/filter_by_popularity', name: 'filter_by_popularity')]
    public function filterByPopularity(IdeaRepository $ideaRepository): Response
    {
        $idea = $ideaRepository->findBy(['id' => 4]);
        return $this->render('home/filter_by_popularity.html.twig', [
            'idea' => $idea,
        ]);
    }

    #[Route('/filter_by_categories', name: 'filter_by_categories')]
    public function filterByCategories(CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->findAll();
        return $this->render('home/filter_by_categories.html.twig', [
            'category' => $category,
        ]);
    }

    #[Route('/filter_by_asc', name: 'filter_by_asc')]
    public function filterByAsc(ProjectRepository $projectRepository, CategoryRepository $categoryRepository): Response
    {
        $project = $projectRepository->findBy([], ['createdAt' => 'ASC']);
        $category = $categoryRepository->findAll();
        return $this->render('home/filter_by_asc.html.twig', [
            'project' => $project,
            'category' => $category,
        ]);
    }

    #[Route('/filter_by_desc', name: 'filter_by_desc')]
    public function filterByDesc(ProjectRepository $projectRepository, CategoryRepository $categoryRepository): Response
    {
        $project = $projectRepository->findBy([], ['createdAt' => 'DESC']);
        $category = $categoryRepository->findAll();
        return $this->render('home/filter_by_desc.html.twig', [
            'project' => $project,
            'category' => $category,
        ]);
    }
}
