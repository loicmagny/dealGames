<?php

namespace App\Controller\Admin;

use App\Entity\Subcategories;
use App\Form\SubcategoriesType;
use App\Repository\SubcategoriesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/subcategories')]
class SubcategoriesController extends AbstractController
{
    #[Route('/', name: 'app_subcategories_index', methods: ['GET'])]
    public function index(SubcategoriesRepository $subcategoriesRepository): Response
    {
        return $this->render('admin/subcategories/index.html.twig', [
            'subcategories' => $subcategoriesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_subcategories_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SubcategoriesRepository $subcategoriesRepository): Response
    {
        $subcategory = new Subcategories();
        $form = $this->createForm(SubcategoriesType::class, $subcategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $subcategoriesRepository->add($subcategory);
            return $this->redirectToRoute('app_subcategories_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/subcategories/new.html.twig', [
            'subcategory' => $subcategory,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_subcategories_show', methods: ['GET'])]
    public function show(Subcategories $subcategory): Response
    {
        return $this->render('admin/subcategories/show.html.twig', [
            'subcategory' => $subcategory,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_subcategories_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Subcategories $subcategory, SubcategoriesRepository $subcategoriesRepository): Response
    {
        $form = $this->createForm(SubcategoriesType::class, $subcategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $subcategoriesRepository->add($subcategory);
            return $this->redirectToRoute('app_subcategories_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/subcategories/edit.html.twig', [
            'subcategory' => $subcategory,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_subcategories_delete', methods: ['POST'])]
    public function delete(Request $request, Subcategories $subcategory, SubcategoriesRepository $subcategoriesRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$subcategory->getId(), $request->request->get('_token'))) {
            $subcategoriesRepository->remove($subcategory);
        }

        return $this->redirectToRoute('app_subcategories_index', [], Response::HTTP_SEE_OTHER);
    }
}
