<?php

namespace App\Controller;

use App\Entity\Idea;
use App\Entity\Like;
use App\Repository\LikeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LikeController extends AbstractController
{
    #[Route('//idea/{id}/like', name: 'app_like_like', methods: ['GET', 'POST'])]
    public function like(Idea $idea, LikeRepository $likeRepository): Response
    {
        $like = new Like();
        $like->setIdea($idea);
        $like->setUser($this->getUser());
        $ideaViews = $idea->getIdeaViews();
        $idea->setIdeaViews($ideaViews - 1);
        $likeRepository->save($like, true);
        return $this->redirectToRoute('app_idea_show', [
            'id' => $idea->getId(),
        ], Response::HTTP_SEE_OTHER);
    }

    #[Route('/idea/{id}/dislike', name: 'app_like_dislike', methods: ['GET', 'POST'])]
    public function dislike(Idea $idea, LikeRepository $likeRepository): Response
    {
        $ideaUser = $likeRepository->findOneBy(['user' => $this->getUser(), 'idea' => $idea->getId()]);
        $ideaViews = $idea->getIdeaViews();
        $idea->setIdeaViews($ideaViews - 1);
        $likeRepository->remove($ideaUser, true);
        return $this->redirectToRoute('app_idea_show', [
            'id' => $idea->getId(),
        ], Response::HTTP_SEE_OTHER);
    }

    #[Route('///idea/{id}/{orderBy}/like', name: 'app_like_like_comment', methods: ['GET', 'POST'])]
    public function likeFromComment(Idea $idea, LikeRepository $likeRepository): Response
    {
        $like = new Like();
        $like->setIdea($idea);
        $like->setUser($this->getUser());
        $ideaViews = $idea->getIdeaViews();
        $idea->setIdeaViews($ideaViews - 1);
        $likeRepository->save($like, true);
        return $this->redirectToRoute('app_idea_show', [
            'id' => $idea->getId(),
        ], Response::HTTP_SEE_OTHER);
    }

    #[Route('//idea/{id}/{orderBy}/dislike', name: 'app_like_dislike_comment', methods: ['GET', 'POST'])]
    public function dislikeFromComment(Idea $idea, LikeRepository $likeRepository): Response
    {
        $ideaUser = $likeRepository->findOneBy(['user' => $this->getUser(), 'idea' => $idea->getId()]);
        $ideaViews = $idea->getIdeaViews();
        $idea->setIdeaViews($ideaViews - 1);
        $likeRepository->remove($ideaUser, true);
        return $this->redirectToRoute('app_idea_show', [
            'id' => $idea->getId(),
        ], Response::HTTP_SEE_OTHER);
    }
}
