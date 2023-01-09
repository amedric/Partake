<?php

namespace App\Controller;

use App\Entity\Idea;
use App\Entity\Project;
use App\Form\IdeaType;
use App\Form\IdeaEditType;
use App\Repository\IdeaRepository;
use App\Repository\UserRepository;
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
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('idea/new.html.twig', [
            'idea' => $idea,
            'form' => $form,
            'edit' => true,
        ]);
    }

    #[Route('/{id}', name: 'app_idea_show', methods: ['GET'])]
    public function show(Idea $idea): Response
    {
        $user = $this->getUser();
        return $this->render('idea/show.html.twig', [
            'idea' => $idea,
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_idea_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Idea $idea, IdeaRepository $ideaRepository): Response
    {
        $ideaUserId = $idea->getUser()->getId();
        $user = $this->getUser();

        if ($ideaUserId == $user) {
            $form = $this->createForm(IdeaEditType::class, $idea);
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
}
