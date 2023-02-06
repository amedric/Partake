<?php

namespace App\Controller;

use App\Form\SearchContentType;
use App\Repository\IdeaRepository;
use App\Repository\ProjectRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    #[Route('/search', name: 'app_search_index')]
    public function index(
        Request $request,
        UserRepository $userRepository,
        ProjectRepository $projectRepository,
        IdeaRepository $ideaRepository,
    ): Response {

        //----------------------- search bar form -----------------
        $form = $this->createForm(SearchContentType::class);
        $form->handleRequest($request);

        if (isset($_GET["searchBar"])) {
            $search = $_GET["searchBar"];
            $projects = $projectRepository->findLikeProject($search);
            $ideas = $ideaRepository->findLikeIdea($search);
            $users = $userRepository->findLikeUser($search);
        }
        return $this->render('/searchResult.html.twig', [
            'users' => $users,
            'projects' => $projects,
            'ideas' => $ideas,
            'form' => $form->createView(),
        ]);
    }
}
