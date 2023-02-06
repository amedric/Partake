<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\User;
use App\Form\Project1Type;
use App\Form\SearchContentType;
use App\Repository\CategoryRepository;
use App\Repository\IdeaRepository;
use App\Repository\ProjectRepository;
use App\Service\ChartStats;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;

#[Route('/home', name: '')]
class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        Request $request,
        ProjectRepository $projectRepository,
        CategoryRepository $categoryRepository,
        IdeaRepository $ideaRepository,
        ChartStats $chartStats,
        MailerInterface $mailer
    ): Response {
        //----------------------- search bar form -----------------
        $form = $this->createForm(SearchContentType::class);
        $form->handleRequest($request);

        //----------------------- new project form -----------------
        /** @var User $user */
        $user = $this->getUser();
        $project = new Project();
        $formNew = $this->createForm(Project1Type::class, $project);
        $formNew->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $search = $form->getData()['search'];
            $projects = $projectRepository->findLikeProject($search);
            $categories = $categoryRepository->findAll();
        } else {
            //----------------------- if project form is submitted -----------------
            if ($formNew->isSubmitted() && $formNew->isValid()) {
                $project->setUser($user);
                $today = new DateTime();
                $project->setCreatedAt($today);
                $projectRepository->save($project, true);
                $usersAuth = $project->getUsersSelectOnProject();
                //Email
                foreach ($usersAuth as $userAuth) {
                    $email = (new Email())
                        ->from('partake@partake.com')
                        ->to($userAuth->getEmail())
                        ->subject('New Project created !')
                        ->text('You have been invited by ' . $user->getFullName() . ' to join the Project : '
                            . $project->getTitle() . ' !');
                    $mailer->send($email);
                }
                $this->addFlash('success', 'Success: New project created');

                return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
            }
            $projects = $projectRepository->findAllProjects('createdAt', 'ASC');
            $categories = $categoryRepository->findAll();
        }
        $projectChart1 = $chartStats->getMobileProjectChart1();
        $projectChart2 = $chartStats->getMobileProjectChart2();
        $ideaChart1 = $chartStats->getMobileIdeaChart1();
        $ideaChart2 = $chartStats->getMobileIdeaChart2();
        $authorizedProjects = $projectRepository->findProjectAuthorizedForUser($this->getUser()->getId());

        return $this->render('home/home.html.twig', [
            'projects' => $projects,
            'categories' => $categories,
            'form' => $form->createView(),
            'formNew' => $formNew->createView(),
            'projectChart1' => $projectChart1,
            'ideaChart1' => $ideaChart1,
            'projectChart2' => $projectChart2,
            'ideaChart2' => $ideaChart2,
            "authorizedProjects" => $authorizedProjects,
            'edit' => true
        ]);
    }

    #[Route('/{orderBy}', name: 'app_home_orderBy')]
    public function orderBy(
        Request $request,
        ProjectRepository $projectRepository,
        CategoryRepository $categoryRepository,
        IdeaRepository $ideaRepository,
        ChartStats $chartStats,
        MailerInterface $mailer,
        string $orderBy
    ): Response {
        //----------------------- search bar form -----------------
        $form = $this->createForm(SearchContentType::class);
        $form->handleRequest($request);

        //----------------------- new project form -----------------
        /** @var User $user */
        $user = $this->getUser();
        $project = new Project();
        $formNew = $this->createForm(Project1Type::class, $project);
        $formNew->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $search = $form->getData()['search'];
            $projects = $projectRepository->findLikeProject($search);
            $categories = $categoryRepository->findAll();
        } else {
            switch ($orderBy) {
                case 'newest':
                    $projects = $projectRepository->findAllProjects('createdAt', 'DESC');
                    $categories = $categoryRepository->findAll();
                    break;
                case 'oldest':
                    $projects = $projectRepository->findAllProjects('createdAt', 'ASC');
                    $categories = $categoryRepository->findAll();
                    break;
                case 'views':
                    $projects = $projectRepository->findAllProjects('views', 'DESC');
                    $categories = $categoryRepository->findAll();
                    break;
                case 'ideas':
                    $projects = $projectRepository->findAllProjects('ideaCount', 'DESC');
                    $categories = $categoryRepository->findAll();
                    break;
            }
        }
        $projectChart1 = $chartStats->getMobileProjectChart1();
        $projectChart2 = $chartStats->getMobileProjectChart2();
        $ideaChart1 = $chartStats->getMobileIdeaChart1();
        $ideaChart2 = $chartStats->getMobileIdeaChart2();
        $authorizedProjects = $projectRepository->findProjectAuthorizedForUser($this->getUser()->getId());

        return $this->render('home/home.html.twig', [
            'projects' => $projects,
            'categories' => $categories,
            'form' => $form->createView(),
            'formNew' => $formNew->createView(),
            'projectChart1' => $projectChart1,
            'ideaChart1' => $ideaChart1,
            'projectChart2' => $projectChart2,
            'ideaChart2' => $ideaChart2,
            "authorizedProjects" => $authorizedProjects,
            'edit' => true
        ]);
    }
}
