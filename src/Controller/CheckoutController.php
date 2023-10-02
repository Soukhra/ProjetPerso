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
    public function index(CartService $cs, RequestStack $rs,Request $request): Response
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
        if($rs->getSession()->get('checkout_data')){
            return $this->redirectToRoute('checkoutConfirm');
        }
        $form = $this->createForm(CheckoutType::class, null, ['user' => $user]);
        $form->handleRequest($request);
        //traitement du formulaire

        return $this->render('checkout/index.html.twig', [
            'checkout' => $form->createView(),
            'cart' => $cartWithData,
            'total' => $total
        ]);
    }

    #[Route('/checkout/confirm', name: 'checkoutConfirm')]
    public function commander(CartService $cs, Request $request, EntityManagerInterface $manager)
    {
        // ! Pour le produit relier en ManyToMany a la commande
        //* recupérer les information de mon panier
        $cartWithData = $cs->cartWithData();
        //* initialisation quantité total
        $qt = 0;
        //* récupérer montant total de la commande
        $total = $cs->total();
        $user = $this->getUser();

        $commande = new Commande;
        if (isset($cartWithData['produit'])) {
            return $this->redirectToRoute("app_accueil");
        }
        if (!$user->getAddresses()->getValues()) {
            $this->addFlash('checkout_message', 'Merci de renseigner une adresse de livraison avant de continuer !');
            return $this->redirectToRoute("app_address_new");
        }
        $form = $this->createForm(CheckoutType::class, null, ['user' => $user]);
        $form->handleRequest($request);
        $reference = $cs->generateUuid();
        $address = $form['address']->getData();
        $transport = $form['transport']->getData();
        $informations = $form['informations']->getData();
       
        if ($form->isSubmitted() && $form->isValid()) {
            $commande->setReference($reference)
                ->setAdresseLivraison($address)
                ->setMoreInformations($informations)
                ->setQuantitePanier($cs['data']['quantite']) //voir dans CartServices.php
                ->setUser($user)
                ->setTransporteur($transport)
                ->setTotal($total)
                ->setEtat('en cours de traitement')
                ->setCreatedAt(new DateTime());
        }
        foreach ($cartWithData as $data) :
            //* récupération du stock
            $stock = $data['produit']->getStock();

            //* si la différence entre mon stock et ma quantité de produit achété est positive
            if ($stock - $data['quantity'] >= 0) {
                $qt += $data['quantity'];
                //du coup on modifie les stock avec le nouveau stock
                $data['produit']->setStock($stock - $data['quantity']);
            } else {
                $this->addFlash('danger', "vous avez dépassez le nombre de t-shirt $data[produit] disponible nous avons modifier votre quantité de ce produit");

                $cs->modifQuantite($data['produit']->getId(), $data['produit']->getStock());

                return $this->redirectToRoute('app_cart');
            }
        endforeach;
        $commande->setQuantitePanier($qt);
        $manager->persist($commande);
        $manager->flush();
        $cs->deleteCart();
        $this->addFlash('success', "Votre commande est bien en cour de traitement, retrouvez le détail sur votre profil");
        return $this->render('checkout/confirm.html.twig', [
            'commande' => $commande,
            'cart' => $cart,
            'address' => $address,
            'transport' => $transport,
            'informations' => $informations,
            'reference' => $reference,
            'checkout' => $form->createView()
        ]);
    }
}
