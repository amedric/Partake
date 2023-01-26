<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/category')]
class CategoryController extends AbstractController
{
//    #[Route('/', name: 'app_category_index', methods: ['GET', 'POST'])]
//    public function index(Request $request, CategoryRepository $categoryRepository): Response
//    {
//
//        $category = new Category();
//        $form = $this->createForm(CategoryType::class, $category);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $categoryRepository->save($category, true);
//
//            return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
//        }
//        return $this->renderform('category/index.html.twig', [
//            'categories' => $categoryRepository->findAll(),
//            'form' => $form,
//            'edit' => true,
//        ]);
//    }

//    #[Route('/new', name: 'app_category_new', methods: ['GET', 'POST'])]
//    public function new(Request $request, CategoryRepository $categoryRepository): Response
//    {
//        $category = new Category();
//        $form = $this->createForm(CategoryType::class, $category);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $categoryRepository->save($category, true);
//
//            return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
//        }
//
//        return $this->renderForm('admin_category_new.html.twig', [
//            'category' => $category,
//            'form' => $form,
//            'edit' => true,
//        ]);
//    }

//    #[Route('/{id}', name: 'app_category_show', methods: ['GET'])]
//    public function show(Category $category): Response
//    {
//        return $this->render('category/show.html.twig', [
//            'category' => $category,
//        ]);
//    }

//    #[Route('/{id}/edit', name: 'app_category_edit', methods: ['GET', 'POST'])]
//    public function edit(Request $request, Category $category, CategoryRepository $categoryRepository): Response
//    {
//        $form = $this->createForm(CategoryType::class, $category);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $categoryRepository->save($category, true);
//
//            return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
//        }
//
//        return $this->renderForm('category/edit.html.twig', [
//            'categories' => $categoryRepository->findAll(),
//            'categoryById' => $categoryRepository->findOneBy(['id' => $category->getId()]),
//            'form' => $form,
//            'edit' => true,
//        ]);
//    }
//
//    #[Route('/{id}', name: 'app_category_delete', methods: ['POST'])]
//    public function delete(Request $request, Category $category, CategoryRepository $categoryRepository): Response
//    {
//        if ($this->isCsrfTokenValid('delete' . $category->getId(), $request->request->get('_token'))) {
//            $categoryRepository->remove($category, true);
//        }
//
//        return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
//    }
}
