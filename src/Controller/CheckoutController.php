<?php

namespace App\Controller;

use DateTime;
use App\Entity\Commande;
use App\Form\CheckoutType;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CheckoutController extends AbstractController
{
    #[Route('/checkout', name: 'app_checkout')]
    public function index(CartService $cs, RequestStack $rs): Response
    {
        $user = $this->getUser();
        $cartWithData = $cs->cartWithData();
        $total = $cs->total();


        if (isset($cart['produit'])) {
            return $this->redirectToRoute("app_checkout");
        }
        if (!$user->getAddresses()->getValues()) {
            $this->addFlash('checkout_message', 'Merci de renseigner une adresse de livraison avant de continuer !');
            return $this->redirectToRoute("app_address_new");
        }
        // if($session()->get('checkout_data')){
        //     return $this->redirectToRoute('checkoutConfirm');
        // }
        $form = $this->createForm(CheckoutType::class, null, ['user' => $user]);
        //$form->handleRequest($request);
        //traitement du formulaire

        return $this->render('checkout/index.html.twig', [
            'checkout' => $form->createView(),
            'cart' => $cartWithData,
            'total' => $total
        ]);
    }

   
}
