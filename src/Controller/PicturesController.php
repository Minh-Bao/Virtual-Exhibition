<?php

namespace App\Controller;

use App\Entity\Picture;
use App\Form\PictureType;
use Doctrine\ORM\EntityManager;
use App\Repository\PictureRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PicturesController extends AbstractController
{

    private $em;

    public function __construct(EntityManagerInterface $em) 
    {
        $this->em = $em;
    }

    /**
     * @Route("/", name="app_home", methods="GET")
     //* @Route("/", name= "app_pictures_index")
     */
    public function index(PictureRepository $pictureRepository): Response
    {
        $pictures = $pictureRepository->findBy([], ['createdAt' => 'DESC']);

        return $this->render('pictures/index.html.twig', compact('pictures'));
    }

    /**
     * @Route("/pictures/create", name="app_pictures_create", methods="GET|POST")
     */
    public function create(Request $request, UserRepository $userRepo): Response
    {
        $picture = new Picture;

        $form = $this->createForm(PictureType::class, $picture);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $janeDoe = $userRepo->findOneBy(['email' => 'janedoe@example.com']);
            $picture->setUser($janeDoe);
            
            $this->em->persist($picture);
            $this->em->flush();

            $this->addFlash('success', 'ArtWork successfully created!');

            return $this->redirectToRoute('app_home');

        }

        return $this->render('pictures/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/pictures/{id<[0-9]+>}", name="app_pictures_show", methods="GET")
     */
    public function show(Picture $picture): Response 
    {
        return $this->render('pictures/show.html.twig', compact('picture'));
    }

    /**
     * @Route("/pictures/{id<[0-9]+>}/edit", name="app_pictures_edit", methods="GET|PUT")
     */
    public function edit(Request $request, Picture $picture): Response 
    {
        $form = $this->createForm(PictureType::class, $picture, [
            'method' => 'PUT',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            $this->addFlash('success', 'ArtWork successfully updated!');


            return $this->redirectToRoute('app_home');

        }


        return $this->render('pictures/edit.html.twig', [
            'picture' => $picture,
            'form' => $form->createView()
        ]);
    }


        /**
     * @Route("/pictures/{id<[0-9]+>}", name="app_pictures_delete", methods="DELETE")
     */
    public function delete(Request $request, Picture $picture, EntityManagerInterface $em): Response 
    {
        if ($this->isCsrfTokenValid('picture_deletion_' . $picture->getId(), $request->request->get('csrf'))) {
            $em->remove($picture);
            $em->flush();

            $this->addFlash('info', 'ArtWork successfully deleted!');

        }

        return $this->redirectToRoute('app_home');
    }



}

