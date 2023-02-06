<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Idea;
use App\Entity\User;
use App\Form\IdeaType;
use App\Form\ProjectEditType;
use App\Repository\IdeaRepository;
use App\Repository\ProjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/project')]
class ProjectController extends AbstractController
{
//    #[Route('/new', name: 'app_project_new', methods: ['GET', 'POST'])]
//    public function new(Request $request, ProjectRepository $projectRepository, MailerInterface $mailer,): Response
//    {
//        $project = new Project();
//        $form = $this->createForm(Project1Type::class, $project);
//        $form->handleRequest($request);
//
//        /** @var User $user */
//        $user = $this->getUser();
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $project->setUser($user);
//            $today = new DateTime();
//            $project->setCreatedAt($today);
//            $projectRepository->save($project, true);
//            $this->addFlash('success', 'Success: New project created');
//
//            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
//        }
//
//        return $this->renderForm('project/new.html.twig', [
//            'project' => $project,
//            'form' => $form,
//            'edit' => true,
//        ]);
//    }

    #[Route('/{id}/{orderBy}', name: 'app_project_show', methods: ['GET', 'POST'])]
    public function show(
        Request $request,
        Project $project,
        IdeaRepository $ideaRepository,
        ProjectRepository $projectRepository,
        int $id,
        string $orderBy
    ): Response {
        $currentUser = $this->getUser();
        $projectCreateBy = $project->getUser();
        $userAuthorized = $project->getUsersSelectOnProject()->contains($currentUser);

        if ($currentUser === $projectCreateBy || $currentUser == $userAuthorized) {
            switch ($orderBy) {
                case 'show':
                    $ideas = $ideaRepository->findAllIdeasByProjectId($project->getId(), 'createdAt', 'ASC');
                    $project->setProjectViews($project->getProjectViews() + 1);
                    $projectRepository->save($project, true);
                    break;
                case 'newest':
                    $ideas = $ideaRepository->findAllIdeasByProjectId($project->getId(), 'createdAt', 'DESC');
                    break;
                case 'oldest':
                    $ideas = $ideaRepository->findAllIdeasByProjectId($project->getId(), 'createdAt', 'ASC');
                    break;
                case 'likes':
                    $ideas = $ideaRepository->findAllIdeasByProjectId($project->getId(), 'ideaLikes', 'DESC');
                    break;
                case 'comments':
                    $ideas = $ideaRepository->findAllIdeasByProjectId($project->getId(), 'ideaComments', 'DESC');
                    break;
                case 'views':
                    $ideas = $ideaRepository->findAllIdeasByProjectId($project->getId(), 'ideaViews', 'DESC');
                    break;
            }

            //------------------ add new idea form ----------------------------
            $idea = new Idea();
            $formNew = $this->createForm(IdeaType::class, $idea);
            $formNew->handleRequest($request);
            //------------------ edit project form ----------------------------
            $formEdit = $this->createForm(ProjectEditType::class, $project);
            $formEdit->handleRequest($request);

            //--------------- if new idea form is submitted --------------------
            if ($formNew->isSubmitted() && $formNew->isValid()) {
                $idea->setUser($this->getUser());
                $idea->setProject($project);
                $ideaRepository->save($idea, true);
                $this->addFlash('success', 'Success:  New idea created');
                return $this->redirectToRoute('app_project_show', [
                    'id' => $project->getId(),
                    'orderBy' => 'show',
                    'project' => $project,
                    'edit' => true,
                ], Response::HTTP_SEE_OTHER);
            }

//            //--------------- if edit project form is submitted --------------------
            if ($formEdit->isSubmitted() && $formEdit->isValid()) {
                $projectRepository->save($project, true);
                $this->addFlash('success', 'Success: Project modified');
                return $this->redirectToRoute('app_project_show', [
                    'id' => $project->getId(),
                    'orderBy' => 'show',
                    'project' => $project,
                    'edit' => true,
                ], Response::HTTP_SEE_OTHER);
            }

            if (isset($_POST["deleteProject"])) {
                if ($this->isCsrfTokenValid('delete' . $project->getId(), $request->request->get('_token'))) {
                    $projectRepository->remove($project, true);
                    $this->addFlash('notice', 'Project deleted');
                }
                return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
            }

            return $this->render('project/show.html.twig', [
                'project' => $project,
                'ideas' => $ideas,
                'formNew' => $formNew->createView(),
                'formEdit' => $formEdit->createView(),
                'edit' => true,
            ]);
        } else {
            $this->addFlash(
                'notice',
                'You do not have permission to access this project, please contact your administrator'
            );
            return $this->redirectToRoute('app_home');
        }
    }

//    #[Route('/{id}/edit', name: 'app_project_edit', methods: ['GET', 'POST'])]
//    public function edit(Request $request, Project $project, ProjectRepository $projectRepository): Response
//    {
//        $form = $this->createForm(ProjectEditType::class, $project);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $projectRepository->save($project, true);
//            $this->addFlash('success', 'Success: Project modified');
//            return $this->redirectToRoute('app_project_show', [
//                'id' => $project->getId(),
//            ], Response::HTTP_SEE_OTHER);
//        }
//
//        return $this->renderForm('project/edit.html.twig', [
//            'project' => $project,
//            'form' => $form,
//            'edit' => true,
//        ]);
//    }

    #[Route('/{id}/delete', name: 'app_project_delete', methods: ['POST'])]
    public function projectDelete(
        Request $request,
        User $user,
        Project $project,
        ProjectRepository $projectRepository
    ): Response {
        if ($this->isGranted('ROLE_ADMIN')) {
            if ($this->isCsrfTokenValid('delete' . $project->getId(), $request->request->get('_token'))) {
                $projectRepository->remove($project, true);
                $this->addFlash('notice', 'Project deleted');
            }
            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        } else {
            $this->addFlash('danger', 'You do not have access rights, please contact your administrator');
            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }
    }

    #[Route('/{id}/show/archived', name: 'app_project_archived', methods: ['GET', 'POST'])]
    public function archive(Request $request, Project $project, ProjectRepository $projectRepository): Response
    {
        if ($this->isCsrfTokenValid('archive' . $project->getId(), $request->request->get('_token'))) {
            $project->setIsArchived(true);
            $projectRepository->save($project, true);
            $this->addFlash('success', 'Success: Project archived');
        }

        return $this->redirectToRoute('app_project_show', [
            'id' => $project->getId(),
            'orderBy' => 'show'
        ], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/show/unarchived', name: 'app_project_unarchived', methods: ['GET', 'POST'])]
    public function unarchive(Request $request, Project $project, ProjectRepository $projectRepository): Response
    {
        if ($this->isCsrfTokenValid('unarchive' . $project->getId(), $request->request->get('_token'))) {
            $project->setIsArchived(false);
            $projectRepository->save($project, true);
            $this->addFlash('notice', 'Success: Project unarchived');
        }

        return $this->redirectToRoute('app_project_show', [
            'id' => $project->getId(),
            'orderBy' => 'show'
        ], Response::HTTP_SEE_OTHER);
    }
}
