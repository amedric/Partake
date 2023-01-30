<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Project;
use App\Entity\Idea;
use App\Entity\User;
use App\Form\CategoryType;
use App\Form\UserType;
use App\Repository\CategoryRepository;
use App\Repository\ProjectRepository;
use App\Repository\IdeaRepository;
use App\Repository\CommentRepository;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;

#[Route('/Admin')]
class AdminController extends AbstractController
{
//    ---------------------------------- Project Routes ---------------------------------------------------

    /**
     * @param ProjectRepository $projectRepository
     * @return Response
     * lists all projects in admin
     */
    #[Route('/project_list', name: 'app_admin_project_list', methods: ['GET'])]
    public function projectList(
        ProjectRepository $projectRepository,
    ): Response {
        if ($this->isGranted('ROLE_ADMIN')) {
            $projects = $projectRepository->findAll();
            return $this->render('admin/admin_project_list.html.twig', [
            'projects' => $projects,
            ]);
        } else {
            $this->addFlash('danger', 'You do not have access rights, please contact your administrator');
            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }
    }

    /**
     * @param Project $project
     * @return Response
     * project view page for admin
     */
    #[Route('/project_view/{id}', name: 'app_admin_project_view', methods: ['GET'])]
    public function projectView(
        Project $project,
    ): Response {
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->render('admin/admin_project_view.html.twig', [
                'project' => $project,
            ]);
        } else {
            $this->addFlash('danger', 'You do not have access rights, please contact your administrator');
            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }
    }

    /**
     * @param Request $request
     * @param Project $project
     * @param ProjectRepository $projectRepository
     * @return Response
     * deletes project
     */
    #[Route('/delete_project/{id}', name: 'app_admin_project_delete', methods: ['POST'])]
    public function projectDelete(Request $request, Project $project, ProjectRepository $projectRepository): Response
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            if ($this->isCsrfTokenValid('delete' . $project->getId(), $request->request->get('_token'))) {
                $projectRepository->remove($project, true);
                $this->addFlash('notice', 'Project deleted');
            }
            return $this->redirectToRoute('app_admin_project_list', [], Response::HTTP_SEE_OTHER);
        } else {
            $this->addFlash('danger', 'You do not have access rights, please contact your administrator');
            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }
    }

    //    ---------------------------------------- Idea Routes -------------------------------------------------------

    /**
     * @param IdeaRepository $ideaRepository
     * @return Response
     * lists all ideas in admin page
     */
    #[Route('/idea_list', name: 'app_admin_idea_list', methods: ['GET'])]
    public function ideaList(
        IdeaRepository $ideaRepository,
    ): Response {
        if ($this->isGranted('ROLE_ADMIN')) {
            $ideas = $ideaRepository->findAll();
            return $this->render('admin/admin_idea_list.html.twig', [
                'ideas' => $ideas,
            ]);
        } else {
            $this->addFlash('danger', 'You do not have access rights, please contact your administrator');
            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }
    }

    /**
     * @param Idea $idea
     * @return Response
     * idea view page for admin
     */
    #[Route('/idea_view/{id}', name: 'app_admin_idea_view', methods: ['GET'])]
    public function ideaView(
        Idea $idea,
    ): Response {
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->render('admin/admin_idea_view.html.twig', [
                'idea' => $idea,
            ]);
        } else {
            $this->addFlash('danger', 'You do not have access rights, please contact your administrator');
            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }
    }

    /**
     * @param Request $request
     * @param Idea $idea
     * @param IdeaRepository $ideaRepository
     * @return Response
     * deletes idea
     */
    #[Route('/delete_idea/{id}', name: 'app_admin_idea_delete', methods: ['POST'])]
    public function ideaDelete(Request $request, Idea $idea, IdeaRepository $ideaRepository): Response
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            if ($this->isCsrfTokenValid('delete' . $idea->getId(), $request->request->get('_token'))) {
                $ideaRepository->remove($idea, true);
                $this->addFlash('notice', 'Idea deleted');
            }
            return $this->redirectToRoute('app_admin_idea_list', [], Response::HTTP_SEE_OTHER);
        } else {
            $this->addFlash('danger', 'You do not have access rights, please contact your administrator');
            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }
    }

    //---------------------------------------- Comment Routes -------------------------------------------------------

    /**
     * @param CommentRepository $commentRepository
     * @return Response
     * lists all comments
     */
    #[Route('/comment_list', name: 'app_admin_comment_list', methods: ['GET'])]
    public function commentList(
        CommentRepository $commentRepository,
    ): Response {
        if ($this->isGranted('ROLE_ADMIN')) {
            $comments = $commentRepository->findAll();
            return $this->render('admin/admin_comment_list.html.twig', [
                'comments' => $comments,
            ]);
        } else {
            $this->addFlash('danger', 'You do not have access rights, please contact your administrator');
            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }
    }

    /**
     * @param Comment $comment
     * @param IdeaRepository $ideaRepository
     * @return Response
     * comment view page for admin
     */
    #[Route('/comment_view/{id}', name: 'app_admin_comment_view', methods: ['GET'])]
    public function commentView(
        Comment $comment,
        IdeaRepository $ideaRepository,
    ): Response {
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->render('admin/admin_comment_view.html.twig', [
                'comment' => $comment,
            ]);
        } else {
            $this->addFlash('danger', 'You do not have access rights, please contact your administrator');
            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }
    }

    /**
     * @param Request $request
     * @param Comment $comment
     * @param CommentRepository $commentRepository
     * @return Response
     * deletes comment
     */
    #[Route('/delete_comment/{id}', name: 'app_admin_comment_delete', methods: ['POST'])]
    public function deleteComment(Request $request, Comment $comment, CommentRepository $commentRepository): Response
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            if ($this->isCsrfTokenValid('delete' . $comment->getId(), $request->request->get('_token'))) {
                $commentRepository->remove($comment, true);
                $this->addFlash('notice', 'Comment deleted');
            }
            return $this->redirectToRoute('app_admin_comment_list', [], Response::HTTP_SEE_OTHER);
        } else {
            $this->addFlash('danger', 'You do not have access rights, please contact your administrator');
            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }
    }

    // ---------------------------------------- User Routes -------------------------------------------------------

    /**
     * @param UserRepository $userRepository
     * @return Response
     * lists all users
     */
    #[Route('/users', name: 'app_admin_user_list', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->render('admin/admin_user_list.html.twig', [
                'users' => $userRepository->findAll(),
            ]);
        } else {
            $this->addFlash('danger', 'You do not have access rights, please contact your administrator');
            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }
    }

    /**
     * @param Request $request
     * @param UserRepository $userRepository
     * @return Response
     * adds new user
     * @throws TransportExceptionInterface
     */
    #[Route('/new_user', name: 'app_admin_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserRepository $userRepository, MailerInterface $mailer): Response
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            $user = new User();
            $form = $this->createForm(UserType::class, $user);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $passwordUser = '1234';
                $user->setPassword(password_hash($passwordUser, PASSWORD_DEFAULT));
                $userRepository->save($user, true);
                //Email
                $email = (new Email())
                    ->from('partake@partake.com')
                    ->to($user->getEmail())
                    ->subject('You have been invited to join Partake !')
                    ->text('Hello ' . $user->getFullName() .
                        ' ! You have been invited to join Partake.
                        Please change your password (1234) !');
                $mailer->send($email);
                $this->addFlash('success', 'Success: User added - email sent');
                return $this->redirectToRoute('app_admin_user_list', [], Response::HTTP_SEE_OTHER);
            }
            return $this->renderForm('admin/admin_user_new.html.twig', [
                'user' => $user,
                'form' => $form,
                'edit' => true,
            ]);
        } else {
            $this->addFlash('danger', 'You do not have access rights, please contact your administrator');
            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }
    }

    /**
     * @param Request $request
     * @param User $user
     * @param UserRepository $userRepository
     * @return Response
     * deletes user
     */
    #[Route('/delete_user/{id}', name: 'app_admin_user_delete', methods: ['POST'])]
    public function deleteUser(Request $request, User $user, UserRepository $userRepository): Response
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
                $userRepository->remove($user, true);
                $this->addFlash('notice', 'User - ' . $user->getFullName() . ' deleted');
            }
            return $this->redirectToRoute('app_admin_user_list', [], Response::HTTP_SEE_OTHER);
        } else {
            $this->addFlash('danger', 'You do not have access rights, please contact your administrator');
            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }
    }

    // ---------------------------------------- Category Routes -------------------------------------------------------

    /**
     * @param Request $request
     * @param CategoryRepository $categoryRepository
     * @return Response
     * department admin page, renders form on same page to add departments
     */
    #[Route('/departments', name: 'app_admin_category_index', methods: ['GET', 'POST'])]
    public function indexCategory(Request $request, CategoryRepository $categoryRepository): Response
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            $category = new Category();
            $form = $this->createForm(CategoryType::class, $category);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $categoryRepository->save($category, true);
                $this->addFlash('success', 'Success: Department created');
                return $this->redirectToRoute('app_admin_category_index', [], Response::HTTP_SEE_OTHER);
            }
            return $this->renderform('admin/admin_category_list.html.twig', [
                'categories' => $categoryRepository->findAll(),
                'form' => $form,
                'edit' => true,
            ]);
        } else {
            $this->addFlash('danger', 'You do not have access rights, please contact your administrator');
            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }
    }

    /**
     * @param Request $request
     * @param Category $category
     * @param CategoryRepository $categoryRepository
     * @return Response
     * lists all departments and renders form on same page to edit department
     */
    #[Route('/edit_department/{id}', name: 'app_admin_category_edit', methods: ['GET', 'POST'])]
    public function editCategory(Request $request, Category $category, CategoryRepository $categoryRepository): Response
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            $form = $this->createForm(CategoryType::class, $category);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $categoryRepository->save($category, true);
                $this->addFlash('success', "Success: Category " . "'" . $category->getTitle() . "' modified");
                return $this->redirectToRoute('app_admin_category_index', [], Response::HTTP_SEE_OTHER);
            }
            return $this->renderForm('admin/admin_category_edit.html.twig', [
                'categories' => $categoryRepository->findAll(),
                'categoryById' => $categoryRepository->findOneBy(['id' => $category->getId()]),
                'form' => $form,
                'edit' => true,
            ]);
        } else {
            $this->addFlash('danger', 'You do not have access rights, please contact your administrator');
            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }
    }

    /**
     * @param Request $request
     * @param Category $category
     * @param CategoryRepository $categoryRepository
     * @return Response
     * deletes department
     */
    #[Route('/delete_department/{id}', name: 'app_admin_category_delete', methods: ['POST'])]
    public function deleteCategory(
        Request $request,
        Category $category,
        CategoryRepository $categoryRepository
    ): Response {
        if ($this->isGranted('ROLE_ADMIN')) {
            if ($this->isCsrfTokenValid('delete' . $category->getId(), $request->request->get('_token'))) {
                $categoryRepository->remove($category, true);
                $this->addFlash('notice', "Department '" . $category->getTitle() . ' deleted');
            }
            return $this->redirectToRoute('app_admin_category_index', [], Response::HTTP_SEE_OTHER);
        } else {
            $this->addFlash('danger', 'You do not have access rights, please contact your administrator');
            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }
    }
}
