<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Idea;
use App\Entity\Like;
use App\Form\CommentType;
use App\Form\IdeaType;
use App\Repository\CommentRepository;
use App\Repository\ProjectRepository;
use App\Repository\IdeaRepository;
use App\Repository\LikeRepository;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/idea')]
class IdeaController extends AbstractController
{
//    #[Route('/new/{id}', name: 'app_idea_new', methods: ['GET', 'POST'])]
//    public function new(Project $project, Request $request, IdeaRepository $ideaRepository): Response
//    {
//        $idea = new Idea();
//        $form = $this->createForm(IdeaType::class, $idea);
//        $form->handleRequest($request);
//        if ($form->isSubmitted() && $form->isValid()) {
//            $idea->setUser($this->getUser());
//            $idea->setProject($project);
//            $ideaRepository->save($idea, true);
//            $this->addFlash('success', 'Success:  New idea created');
//            return $this->redirectToRoute('app_project_show', [
//                'id' => $project->getId(),
//                'orderBy' => 'show',
//            ], Response::HTTP_SEE_OTHER);
//        }
//
//        return $this->renderForm('idea/new.html.twig', [
//            'idea' => $idea,
//            'form' => $form,
//            'project' => $project,
//            'edit' => true,
//        ]);
//    }

    /**
     * @throws Exception
     */
    #[Route('/{id}/{orderBy}', name: 'app_idea_show', methods: ['GET', 'POST'])]
    public function show(
        Idea $idea,
        IdeaRepository $ideaRepository,
        Request $request,
        CommentRepository $commentRepository,
        ProjectRepository $projectRepository,
        LikeRepository $likeRepository,
        string $orderBy = 'ASC',
    ): Response {
        $user = $this->getUser();
        $idea->setIdeaViews($idea->getIdeaViews() + 1);
        $ideaRepository->save($idea, true);
        $likedUser = $likeRepository->findLikeByUser($user->getId(), $idea->getId());
        $ideaLikes = count($likeRepository->findBy(['idea' => $idea->getId()]));
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

        $comments = $commentRepository->findBy([], ['createdAt' => $orderBy ]);
        $nbComments = $projectRepository->findIdeasCountComments();
        return $this->render('idea/show.html.twig', [
            'idea' => $idea,
            'user' => $user,
            'formComm' => $form->createView(),
            'edit' => true,
            'comments' => $comments,
            'nbComments' => $nbComments,
            'likedUser' => $likedUser,
            'ideaLikes' => $ideaLikes
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
                $this->addFlash('success', 'Success: Idea modified');
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
            $this->addFlash('notice', 'Notice: Idea deleted');
        }

        return $this->redirectToRoute('app_project_show', [
            'id' => $idea->getProject()->getId(),
            'orderBy' => 'show'
        ], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/{orderBy}/{dataType}/like', name: 'app_idea_like', methods: ['GET','POST'])]
    public function like(
        Idea $idea,
        LikeRepository $likeRepository,
        int $id,
        string $orderBy,
        string $dataType
    ): Response {
        $like = new Like();
        $like->setIdea($idea);
        $like->setUser($this->getUser());
        $likeRepository->save($like, true);
        return $this->redirectToRoute(
            'app_user_show',
            [
                'id' => $this->getUser()->getId(),
                'orderBy' => $orderBy,
                'dataType' => $dataType
            ],
            Response::HTTP_SEE_OTHER
        );
    }
    #[Route('/{id}/{orderBy}/{dataType}/dislike', name: 'app_idea_dislike', methods: ['GET','POST'])]
    public function dislike(
        LikeRepository $likeRepository,
        Idea $idea,
        int $id,
        string $orderBy,
        string $dataType
    ): Response {
        $ideaUser = $likeRepository->findOneBy(['user' => $this->getUser(), 'idea' => $idea->getId()]);
        $likeRepository->remove($ideaUser, true);
        return $this->redirectToRoute(
            'app_user_show',
            [
                'id' => $this->getUser()->getId(),
                'orderBy' => $orderBy,
                'dataType' => $dataType
            ],
            Response::HTTP_SEE_OTHER
        );
    }
}
