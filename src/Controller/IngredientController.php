<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IngredientController extends AbstractController
{
#[Route('/ingredient', name: 'app_ingredient', methods:['GET'])]
    public function index(IngredientRepository $Repository, PaginatorInterface $paginator, Request $request ): Response
    {
        $ingredients = $paginator->paginate(
            $Repository->findAll(), 
            $request->query->getInt('page', 1), /*page number*/
            10  /*limit per page*/ 
        );
        return $this->render('pages/ingredient/index.html.twig',[
            'ingredients'  => $ingredients 
        ]);
    }

    /**
     * creer des news ingredients
     */

     #[Route('/ingredient/nouveau', name:'app_nouveau', methods:['GET', 'POST'])]
     public function new(Request $request, EntityManagerInterface $em): Response
     {
        $ingredient = new Ingredient();
        $form = $this->createForm(IngredientType::class, $ingredient);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $ingredient = $form->getData();
            $em->persist($ingredient);
            $em->flush();
           $this->addFlash(
            'success',
            'Votre ingredient a été créé avec succès !'
           );
           return $this->redirectToRoute('app_ingredient');
        }
        return $this->render('pages/ingredient/new.html.twig', [
        'form' => $form->createView()
       ]);  
     }

     /**
      * modifier l'ingredient
      */

    #[Route('/ingredient/modifier/{id}', name:'app_modifier', methods:['GET', 'POST'])]
     public function edit(Ingredient $ingredient, Request $request, EntityManagerInterface $em): Response
     {
        $form = $this->createForm(IngredientType::class, $ingredient);  
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $ingredient = $form->getData();
            $em->persist($ingredient);
            $em->flush();
           $this->addFlash(
            'success',
            'Votre ingredient a été modifié avec succès !'
           );
           return $this->redirectToRoute('app_ingredient');
        }
        return $this->render('pages/ingredient/edit.html.twig', [
        'form' => $form->createView()  
        ]);
     }

     /**
      * supprimer l'ingrdient
      */
     #[Route('/ingredient/suppression/{id}', name:'app_supprimer', methods:['GET'])]
      public function delete(EntityManagerInterface $em, Ingredient $ingredient): Response
      {
        if (!$ingredient) {
            $this->addFlash(
                'warning',
                'L\'ingredient en question n\'a pas été trouvé !'
               );
    
            return $this->redirectToRoute('app_ingredient');
        }

        $em->remove($ingredient);
        $em->flush();
        $this->addFlash(
            'success',
            'Votre ingredient a été supprimé avec succès !'
           );

        return $this->redirectToRoute('app_ingredient');
      }
}
