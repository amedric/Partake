<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\SearchContentType;
use App\Form\UserType;
use App\Repository\IdeaRepository;
use App\Repository\LikeRepository;
use App\Repository\ProjectRepository;
use App\Repository\UserRepository;
use Doctrine\DBAL\Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user')]
class UserController extends AbstractController
{
    /**
     * @throws Exception
     */
    #[Route('/{id}/{orderBy}', name: 'app_user_show', methods: ['GET', 'POST'])]
    public function show(
        Request $request,
        User $user,
        UserRepository $userRepository,
        ProjectRepository $projectRepository,
        IdeaRepository $ideaRepository,
        LikeRepository $likeRepository,
        string $orderBy
    ): Response {
        $form = $this->createForm(SearchContentType::class);
        $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $search = $form->getData()['search'];
                $projectsIdeas = $userRepository->findProjectsIdeasForUser($user->getId(), 'createdAt', 'ASC');
            } else {
    //            $ideas = $ideaRepository->findBy(['user' => $user->getId()]);
    //            $projectsIdeas = $userRepository->findProjectsIdeasForUser($user->getId());
                switch ($orderBy) {
                    case 'newest':
                        $projectsIdeas = $userRepository->findProjectsIdeasForUser($user->getId(), 'createdAt', 'DESC');
                        break;
                    case 'oldest':
                        $projectsIdeas = $userRepository->findProjectsIdeasForUser($user->getId(), 'createdAt', 'ASC');
                        break;
                }
            }
        return $this->render('user/show.html.twig', [
            'user' => $user,
            'projectsIdeas' => $projectsIdeas,
            'form' => $form->createView()
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, UserRepository $userRepository): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $passwordUser = $user->getPassword();
            $user->setPassword(password_hash($passwordUser, PASSWORD_DEFAULT));
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
            'edit' => true,
        ]);
    }
}
