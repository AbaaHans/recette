<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RecipeController extends AbstractController
{
    /**
     * function display recipe
     */
    #[Route('/recette', name: 'app_recette', methods:['GET'])]
    public function index(RecipeRepository $Repositor, PaginatorInterface $paginator, Request $request ): Response
    {
        $recipes = $paginator->paginate(
            $Repositor->findAll(), 
            $request->query->getInt('page', 1), /*page number*/
            10  /*limit per page*/ 
        );
        return $this->render('pages/recipe/index.html.twig', [
            'recipes' => $recipes
        ]);
    }

    /**
     * created new recipe
     */

    #[Route('/recette/nouveau', name:'new_recette', methods:['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $recipe = $form->getData();
            $em->persist($recipe);
            $em->flush();
            $this->addFlash(
                'success',
                'Votre recette a été créé avec succès !'
               );

            return $this->redirectToRoute('app_recette');

        }
        return $this->render('pages/recipe/new.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**
     * edit recipe
     */
    #[Route('/recette/modifier/{id}', name:'edit_recette', methods:['GET', 'POST'])]
     public function edit(Recipe $recipe, Request $request, EntityManagerInterface $em ): Response
     {
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $recipe = $form->getData();
            $em->persist($recipe);
            $em->flush();
            $this->addFlash(
                'success',
                'Votre recette a été modifiée avec succès !'
               );

            return $this->redirectToRoute('app_recette');

        }
        return $this->render('pages/recipe/edit.html.twig', [
            'form' => $form->createView()
        ]);
     }
     
     /**
      * supprimer recette
      */
      #[Route('/recette/suppression/{id}', name:'delete_recette', methods:['GET'])]
      public function delete(EntityManagerInterface $em, Recipe $recipe): Response
      {
        if (!$recipe) {
            $this->addFlash(
                'warning',
                'La recette en question n\'a pas été trouvé !'
               );
    
            return $this->redirectToRoute('app_recette');
        }

        $em->remove($recipe);
        $em->flush();
        $this->addFlash(
            'success',
            'Votre recette a été supprimé avec succès !'
           );

        return $this->redirectToRoute('app_recette');
      }
    
}
