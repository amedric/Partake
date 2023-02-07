<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Idea;
use App\Form\CommentType;
use App\Form\IdeaType;
use App\Repository\CommentRepository;
use App\Repository\IdeaRepository;
use App\Repository\LikeRepository;
use App\Repository\ProjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/comment')]
class CommentController extends AbstractController
{

    #[Route('/new', name: 'app_comment_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CommentRepository $commentRepository): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commentRepository->save($comment, true);

            return $this->redirectToRoute('app_comment_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('comment/new.html.twig', [
            'comment' => $comment,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/{orderBy}/{commentId}/edit', name: 'app_comment_edit', methods: ['GET', 'POST'])]
    public function commentEdit(
        Idea $idea,
        IdeaRepository $ideaRepository,
        Request $request,
        CommentRepository $commentRepository,
        ProjectRepository $projectRepository,
        LikeRepository $likeRepository,
        string $commentId,
        string $orderBy = 'ASC',
    ): Response {

        $user = $this->getUser();
        $idea->setIdeaViews($idea->getIdeaViews() + 1);
        $ideaRepository->save($idea, true);
        $likedUser = $likeRepository->findLikeByUser($user->getId(), $idea->getId());
        $ideaLikes = count($likeRepository->findBy(['idea' => $idea->getId()]));
        $comment = $commentRepository->findOneBy(['id' => $commentId]);

        //------------ edit comment form --------------------------------
        $formEditComm = $this->createForm(CommentType::class, $comment);
        $formEditComm->handleRequest($request);
        if ($formEditComm->isSubmitted() && $formEditComm->isValid()) {
            $commentRepository->save($comment, true);
            $this->addFlash('success', 'Success: Comment modified');
            return $this->redirectToRoute('app_idea_show', [
                'id' => $idea->getId(),
            ], Response::HTTP_SEE_OTHER);
        }

        //---------------- edit idea form ------------------------------------------------
        //---------------- if edit form form is submitted --------------------------------
        $userId = $this->getUser();
        $ideaCreatedBy = $idea->getUser();
        $formEditIdea = $this->createForm(IdeaType::class, $idea);
        $formEditIdea->handleRequest($request);
        if ($ideaCreatedBy === $userId && $formEditIdea->isSubmitted() && $formEditIdea->isValid()) {
            $ideaRepository->save($idea, true);
            $this->addFlash('success', 'Success: Idea modified');
            return $this->redirectToRoute('app_idea_show', [
                'id' => $idea->getId(),
            ], Response::HTTP_SEE_OTHER);
        }

        $comments = $commentRepository->findBy([], ['createdAt' => $orderBy ]);
        $nbComments = $projectRepository->findIdeasCountComments();
        return $this->render('comment/edit.html.twig', [
            'idea' => $idea,
            'user' => $user,
            'formEditIdea' => $formEditIdea->createView(),
            'formEditComm' => $formEditComm->createView(),
            'edit' => true,
            'comment' => $comment,
            'comments' => $comments,
            'nbComments' => $nbComments,
            'likedUser' => $likedUser,
            'ideaLikes' => $ideaLikes
        ]);
    }

    #[Route('/{id}', name: 'app_comment_delete', methods: ['POST'])]
    public function deleteIdea(Request $request, Comment $comment, CommentRepository $commentRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $comment->getId(), $request->request->get('_token'))) {
            $commentRepository->remove($comment, true);
        }

        return $this->redirectToRoute('app_idea_show', [
            'id' => $comment->getIdea()->getId(),
        ], Response::HTTP_SEE_OTHER);
    }
}
