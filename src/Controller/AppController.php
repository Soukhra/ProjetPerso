<?php

namespace App\Controller;


use App\Entity\Produit;
use App\Service\CartService;
use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class AppController extends AbstractController
{
    #[Route('/accueil', name: 'app_app')]
    public function index( ProduitRepository $repo, CartService $cs): Response
    {
        $produits = $repo->findAll();
        $cart = $cs->cart();
        return $this->render('app/index.html.twig', [
            'produits'=> $produits,
            'cart' => $cart
        ]);
    }

    #[Route('/compte/commandes', name:"app_commande")]
    public function compteCommande()
    {
        return $this->render('app/commande.html.twig');
    }

    #[Route('/boutique/{collection}', name:"app_boutique_collection")]
    #[Route('/boutique', name:"app_boutique")]
    public function boutique( ProduitRepository $repo, $collection = null): Response
    {
        //* je vérifie que ma variable n'est pas null
        if($collection)
        {
            //* critère en tableau dans le findBy = Where .... AND ....
            $produits = $repo->findBy(['collection' => $collection ]);
        }else{
           $produits = $repo->findAll(); 
        }
        
        return $this->render('app/boutique.html.twig', [
            'produits'=> $produits,
            'collection'=> $collection
        ]);
    }

    #[Route('/boutique/show/{id}' , name:"app_boutique_show")]
    public function show(Produit $produit, CartService $cs)
    {
        
        $cart = $cs->cart();
        return $this->render('app/show.html.twig', [
            'produit'=> $produit,
            'cart' => $cart
        ]);
    }
}

