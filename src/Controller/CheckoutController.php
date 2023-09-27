<?php

namespace App\Controller;

use App\Form\CheckoutType;
use App\Service\CartService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CheckoutController extends AbstractController
{
    #[Route('/checkout', name: 'app_checkout')]
    public function index(CartService $cs): Response
    {
        $user = $this->getUser();
        $cartWithData = $cs->cartWithData();
        $total = $cs->total();    
       
        if(isset($cart['produit'])){
            return $this->redirectToRoute("app_checkout");
        }
        if(!$user->getAddresses()->getValues()){
            $this->addFlash('checkout_message', 'Merci de renseigner une adresse de livraison avant de continuer !');
            return $this->redirectToRoute("app_address_new");
        }
        $form = $this->createForm(CheckoutType::class, null, ['user'=>$user]);
        //$form->handleRequest($request);
        //traitement du formulaire
        return $this->render('checkout/index.html.twig', [
            'checkout'=>$form->createView(),
            'cart' => $cartWithData,
            'total' => $total
        ]);
    }
}
