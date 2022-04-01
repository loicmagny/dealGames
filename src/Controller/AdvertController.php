<?php

namespace App\Controller;

use App\Entity\Advert;
use App\Entity\Images;
use App\Entity\User;
use App\Form\AdvertType;
use App\Repository\AdvertRepository;
use App\Repository\CommentsRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/advert')]
class AdvertController extends AbstractController
{
    #[Route('/', name: 'app_advert_index', methods: ['GET'])]
    public function index(AdvertRepository $advertRepository): Response
    {
        return $this->render('advert/index.html.twig', [
            'adverts' => $advertRepository->findAll(),
        ]);
    }

    public function getAll(AdvertRepository $advertRepository)
    {
        return $advertRepository->findAll();
    }

    #[Route('/new', name: 'app_advert_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        AdvertRepository $advertRepository
    ): Response {
        $user = $this->get('security.token_storage')
            ->getToken()
            ->getUser();
        $advert = new Advert();
        $form = $this->createForm(AdvertType::class, $advert);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $images = $form->get('images')->getData();

            foreach ($images as $image) {
                $file = md5(uniqid()) . '.' . $image->guessExtension();
                $image->move(
                    $this->getParameter('images_adverts_directory'),
                    $file
                );
                $img = new Images();
                $img->setName($file);
                $advert->addImages($img);
                $advert->setUser($user);
            }

            $advertRepository->add($advert);
            return $this->redirectToRoute(
                'app_advert_index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->renderForm('advert/new.html.twig', [
            'advert' => $advert,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_advert_show', methods: ['GET'])]
    public function show(
        Advert $advert,
        UserRepository $userRepository
    ): Response {
        return $this->render('advert/show.html.twig', [
            'advert' => $advert,
            'user' => $userRepository->findBy(['id' => $advert->getUser()]),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_advert_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Advert $advert,
        AdvertRepository $advertRepository
    ): Response {
        $form = $this->createForm(AdvertType::class, $advert);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $images = $form->get('images')->getData();

            foreach ($images as $image) {
                $file = md5(uniqid()) . '.' . $image->guessExtension();
                $image->move(
                    $this->getParameter('images_adverts_directory'),
                    $file
                );
                $img = new Images();
                $img->setName($file);
                $advert->addImages($img);
            }

            $advertRepository->add($advert);
            return $this->redirectToRoute(
                'app_advert_index',
                [],
                Response::HTTP_SEE_OTHER
            );

            $advertRepository->add($advert);
            return $this->redirectToRoute(
                'app_advert_index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->renderForm('advert/edit.html.twig', [
            'advert' => $advert,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_advert_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Advert $advert,
        AdvertRepository $advertRepository
    ): Response {
        if (
            $this->isCsrfTokenValid(
                'delete' . $advert->getId(),
                $request->request->get('_token')
            )
        ) {
            $advertRepository->remove($advert);
        }

        return $this->redirectToRoute(
            'app_advert_index',
            [],
            Response::HTTP_SEE_OTHER
        );
    }

    /**
     * @Route("delete/image/{id}", name="advert_delete_image", methods={"DELETE"})
     */
    public function deleteImg(Images $images, Request $request)
    {
        $data = json_decode($request->getContent(), true);
        if (
            $this->isCsrfTokenValid(
                'delete' . $images->getId(),
                $data['_token']
            )
        ) {
            $name = $images->getName();
            unlink($this->getParameter('images_directory') . '/' . $name);
            $em = $this->getDoctrine()->getManager();
            $em->remove($images);
            $em->flush();

            return new JsonResponse(['success' => 1]);
        } else {
            return new JsonResponse(['error' => 'Token Invalide'], 400);
        }
    }
}
