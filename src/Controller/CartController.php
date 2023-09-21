<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart')]
    public function index(CartService $cs): Response
    {
        $cartWithData = $cs->cartWithData();
        $total = $cs->total();
        
        return $this->render('cart/index.html.twig', [
            'items' => $cartWithData,
            'total' => $total
        ]);
    }
    #[Route('/cart/add/{id}/{route}', name:'cart_add_route')]
    #[Route('/cart/add/{id}', name:'cart_add')]
    public function add($id, CartService $cs, Request $request, $route = null )
    {
        //*ici s'il y a une route en url en plus (boutique, homme, femme) pour la bonne redirection
        if($route)
        {
            $cs->add($id);
            switch ($route)
            {
                case 'boutique':
                return $this->redirectToRoute('app_boutique');
                break;
                case 'homme':
                return $this->redirectToRoute('app_boutique_collection', ['collection' => 'homme']);
                break;
                case 'femme':
                return $this->redirectToRoute('app_boutique_collection', ['collection' => 'femme']);
                break;
            }
            
        }
        //* s'il y a une requete en post avec un name = qtAdd donc s'il y a dans le tableau $_POST un indice qtAdd
        elseif($request->request->get('qtAdd'))
        {   
            $qtAdd = $request->request->get('qtAdd');
            $cs->add($id, $qtAdd);
            return $this->redirectToRoute('app_app');
        }
        //* sinon toujours rediriger vert le panier
        else{
            $cs->add($id);
            return $this->redirectToRoute('app_cart');
        }
        
        // dd($session->get('cart'));
        
        
    }
    
    #[Route('/cart/drop/{id}', name:'cart_drop')]
    public function drop($id, CartService $cs)
    {
        
            $cs->less($id);
            return $this->redirectToRoute('app_cart');
        
    }

    #[Route('/cart/remove/{id}', name:'cart_remove')]
    public function remove($id, CartService $cs) : Response
    {
        $cs->remove($id);
        return $this->redirectToRoute('app_cart');
         
    }

    // #[Route('/cart/commande', name:'cart_commande')]
    // public function commander(CartService $cs, EntityManagerInterface $manager)
    // {
    //     // ! Pour le produit relier en ManyToMany a la commande
    //     //* recupérer les information de mon panier
    //     $cartWithData = $cs->cartWithData();
    //     //* initialisation quantité total
    //     $qt = 0;
    //     //* récupérer montant total de la commande
    //     $total = $cs->total();

    //     $commande = new Commande;
    //     //*set se qui ne change pas
    //     $commande->setMembre($this->getUser())
    //             ->setMontant($total)
    //             ->setEtat('en cours de traitement')
    //             ->setDateEnregistrement(new \DateTime);
    //     foreach($cartWithData as $data):
    //         //* récupération du stock
    //         $stock= $data['produit']->getStock();

    //         //* si la différence entre mon stock et ma quantité de produit achété est positive
    //         if($stock - $data['quantity'] >= 0 )
    //         {   
    //             //* add mon produit a la commande
    //             $commande->addProduit($data['produit']);
    //             //*j'ajoute la quantité de se produit a ma quantité total
    //             $qt += $data['quantity'];
    //             //du coup on modifie les stock avec le nouveau stock
    //             $data['produit']->setStock($stock - $data['quantity']);
    //         }else
    //         {
    //             $this->addFlash('danger',"vous avez dépassez le nombre de t-shirt $data[produit] disponible nous avons modifier votre quantité de ce produit");

    //             $cs->modifQuantite($data['produit']->getId(), $data['produit']->getStock());
                
    //             return $this->redirectToRoute('app_cart');
    //         }
    //     endforeach;
    //     $commande->setQuantite($qt);
    //     // ! pour le produit relié en ManyToOne a la commande
    //         /**
    //          * * $data = [ 
    //          * *    'produit' => produit(objet),
    //          * *    'quantity' => quantité du produit dans mon panier
    //          * * ]
    //          * 
    //          */
    //     //* recupérer les information de mon panier
    //     /**$cartWithData = $cs->cartWithData();
    //     foreach($cartWithData as $data):    
    //         $stock= $data['produit']->getStock();
             
    //         $commande = new Commande;
    //         //*set se qui ne change pas
    //         $commande->setMembre($this->getUser())
    //                 ->setEtat('en cours de traitement')
    //                 ->setDateEnregistrement(new \DateTime)
    //                 ->setProduit($data['produit'])
    //                 ->setQuantite($data['quantity'])
    //                 ->setMontant($data['produit']->getPrix() * $data['quantity']);
    //         $manager->persist($commande);
    //         $data['produit']->setStock($stock - $data['quantity']);
    //         endforeach;
    //     */

    //     $manager->persist($commande);
    //     $manager->flush();
    //     $cs->deleteCart();
    //     $this->addFlash('success',"Votre commande est bien en cour de traitement, retrouvez le détail sur votre profil");
    //     return $this->redirectToRoute('app_app');
            
    // }
}
