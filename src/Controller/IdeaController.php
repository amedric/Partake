<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Idea;
use App\Entity\Like;
use App\Entity\Project;
use App\Entity\User;
use App\Form\CommentType;
use App\Form\IdeaType;
use App\Form\IdeaEditType;
use App\Repository\CommentRepository;
use App\Repository\IdeaRepository;
use App\Repository\LikeRepository;
use App\Repository\UserRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/idea')]
class IdeaController extends AbstractController
{
    #[Route('/', name: 'app_idea_index', methods: ['GET'])]
    public function index(IdeaRepository $ideaRepository): Response
    {
        return $this->render('idea/index.html.twig', [
            'ideas' => $ideaRepository->findAll(),
        ]);
    }

    #[Route('/new/{id}', name: 'app_idea_new', methods: ['GET', 'POST'])]
    public function new(Project $project, Request $request, IdeaRepository $ideaRepository): Response
    {
        $idea = new Idea();
        $form = $this->createForm(IdeaType::class, $idea);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $idea->setUser($this->getUser());
            $idea->setProject($project);
            $ideaRepository->save($idea, true);

            return $this->redirectToRoute('app_project_show', [
                'id' => $project->getId(),
                'orderBy' => 'show',
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('idea/new.html.twig', [
            'idea' => $idea,
            'form' => $form,
            'project' => $project,
            'edit' => true,
        ]);
    }

    #[Route('/{id}', name: 'app_idea_show', methods: ['GET', 'POST'])]
    public function show(
        Idea $idea,
        IdeaRepository $ideaRepository,
        Request $request,
        CommentRepository $commentRepository
    ): Response {
        $user = $this->getUser();
        $idea->setIdeaViews($idea->getIdeaViews() + 1);
        $ideaRepository->save($idea, true);

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setUser($this->getUser());
            $comment->setIdea($idea);
            $date = new DateTime('now');
            $comment->setCreatedAt($date);
            $commentRepository->save($comment, true);

            return $this->redirectToRoute('app_idea_show', [
                'id' => $idea->getId(),
            ], Response::HTTP_SEE_OTHER);
        }

        $comments = $commentRepository->findAll();

        return $this->renderForm('idea/show.html.twig', [
            'idea' => $idea,
            'user' => $user,
            'form' => $form,
            'edit' => true,
            'comments' => $comments,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_idea_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Idea $idea, IdeaRepository $ideaRepository): Response
    {
        $userId = $this->getUser();
        $ideaCreatedBy = $idea->getUser();

        if ($ideaCreatedBy === $userId) {
            $form = $this->createForm(IdeaType::class, $idea);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $ideaRepository->save($idea, true);

                return $this->redirectToRoute('app_idea_show', [
                    'id' => $idea->getId(),
                ], Response::HTTP_SEE_OTHER);
            }

            return $this->renderForm('idea/edit.html.twig', [
                'idea' => $idea,
                'form' => $form,
                'edit' => true,
            ]);
        } else {
            return $this->redirectToRoute('app_idea_show', [
                'id' => $idea->getId(),
            ], Response::HTTP_SEE_OTHER);
        }
    }

    #[Route('/{id}', name: 'app_idea_delete', methods: ['POST'])]
    public function delete(Request $request, Idea $idea, IdeaRepository $ideaRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $idea->getId(), $request->request->get('_token'))) {
            $ideaRepository->remove($idea, true);
        }

        return $this->redirectToRoute('app_idea_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/like', name: 'app_idea_like', methods: ['GET','POST'])]
    public function like(Idea $idea, LikeRepository $likeRepository): Response
    {
        $like = new Like();
        $like->setIdea($idea);
        $like->setUser($this->getUser());
        $likeRepository->save($like, true);
        return $this->redirectToRoute(
            'app_user_show',
            ['id' => $this->getUser()->getId()],
            Response::HTTP_SEE_OTHER
        );
    }
    #[Route('/{id}/dislike', name: 'app_idea_dislike', methods: ['GET','POST'])]
    public function dislike(LikeRepository $likeRepository): Response
    {
        $ideaUser = $likeRepository->findOneBy(['user' => $this->getUser()]);
        $likeRepository->remove($ideaUser, true);
        return $this->redirectToRoute(
            'app_user_show',
            ['id' => $this->getUser()->getId()],
            Response::HTTP_SEE_OTHER
        );
    }
}
