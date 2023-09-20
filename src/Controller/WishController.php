<?php

namespace App\Controller;

use App\Entity\Wish;
use App\Repository\CategoryRepository;
use App\Repository\WishRepository;
use App\Services\CensuratorServices;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\CreateWishType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

#[IsGranted('ROLE_USER')]
class WishController extends AbstractController
{
    #[Route('/wish/list', name: 'app_wish_list')]
    public function list(WishRepository $wishRepository): Response
    {
        // get all wish and send them to the twig

        $wishList = $wishRepository->getAllWish();
        return $this->render('wish/wishlist.html.twig', [
            'controller_name' => 'WishController',
            'wishList' => $wishList
        ]);
    }


    #[Route('/wish/details/{id}', name: 'app_wish_details', requirements: ['id' => '\d+'])]
    public function details(int $id,WishRepository $wishRepository): Response
    {
        $wish = $wishRepository->findWishById($id);
        return $this->render('wish/wishdetails.html.twig', [
            'controller_name' => 'WishController',
            'wish'=> $wish
        ]);
    }

    #[Route('/wish/deleteBackdrop/{id}', name: 'app_wish_deleteBackdrop')]
    public function deleteBackdrop(Wish $wish, EntityManagerInterface $em): Response
    {
        //$wish = $wishRepository->findWishById($id);
        if ($wish->getBackdrop() != null)
        {
            unlink($this->getParameter('uploads_dir'). '/backdrops/'.$wish->getBackdrop());
            $wish->setBackdrop(null);
            $em->persist($wish);
            $em->flush();
        }

        $this->addFlash("success", "L'image à bien été supprimée");

        return $this->redirectToRoute('app_wish_details', ['id' => $wish->getId()]);
    }


    #[Route('/wish/create', name: 'app_wish_create')]
    #[IsGranted('ROLE_USER')]
    public function create(Request $request, CategoryRepository $categRepo, EntityManagerInterface $em, SluggerInterface $slugger, CensuratorServices $censuratorService): Response
    {
        $categories = $categRepo->findAll();
        $wish = new Wish();
        $wishForm = $this->createForm(CreateWishType::class, $wish, [
            'categories' => $categories
        ]);

        $wishForm->handleRequest($request);

        if ($wishForm->isSubmitted() && $wishForm->isValid()) {
            $wish->setDescription($censuratorService->purify($wish->getDescription()));

            $backdropFile = $wishForm->get('backdrop_file')->getData();

            if (!empty($backdropFile) && $backdropFile instanceof UploadedFile) {
                $newFileName = $slugger->slug($wish->getTitle()) . '_' . uniqid() . '.' . $backdropFile->guessExtension();
                if ($backdropFile->move($this->getParameter('uploads_dir'). '/backdrops', $newFileName)) {
                    if ($wish->getBackdrop() != null)
                    {
                        unlink($this->getParameter('uploads_dir'). '/backdrops/'.$wish->getBackdrop());
                    }
                    $wish->setBackdrop($newFileName);
                }
            }

            $em->persist($wish);
            $em->flush();

            $this->addFlash("success", "Le souhait est bien enregistrée");

            return $this->redirectToRoute('app_wish_details', ['id' => $wish->getId()]);
        }

        return $this->render('wish/edit.html.twig', [
            'form' => $wishForm
        ]);
    }
}
