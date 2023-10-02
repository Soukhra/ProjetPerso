<?php

namespace App\Controller;


use DateTime;
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
   

}
