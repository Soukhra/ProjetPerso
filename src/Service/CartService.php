<?php

namespace App\Service;

use DateTime;
use App\Entity\Commande;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;


class CartService
{
    private $repo;

    private $rs;

   

    //injection de dépendances hors d'un controller : constructeur
    public function __construct(ProduitRepository $repo, RequestStack $rs)
    {
        $this->rs = $rs;
        $this->repo = $repo;
       
    }
    public function cart()
    {
        $session = $this->rs->getSession();

        $cart = $session->get('cart', []);
        return $cart;
    }
    public function add($id, $qtAdd = 1)
    {
        //Nous récupérons ou créons une session gâce à la classe RequsStack (service)
        $session = $this->rs->getSession();

        $cart = $session->get('cart', []);
        $qt = $session->get('qt', 0);
        $produit = $this->repo->find($id);
        // je récupère l'attribut de session 'cart' s'il existe ou un tableau vide
        
            if(!empty($cart[$id]))
             {   
                if($produit->getStock() - $cart[$id] - $qtAdd >= 0 )
                {
                    $cart[$id] += $qtAdd;
                    $qt += $qtAdd;
                    $this->rs->getSession()->getFlashBag()->add('success', 'le produit a été ajouté au panier, connectez vous pour voir votre panier si vous n\'etes pas deja connecté');
                }
                else
                {
                    // $this->addFlash('danger', 'le produit a un stock limité');
                    $this->rs->getSession()->getFlashBag()->add('danger', 'le produit a un stock limité');
                }
            }else
            {
            $cart[$id] = $qtAdd; 
            $qt += $qtAdd;
            }
       
        //dans mon tableau $cart, à la case $id, je donne la valeur 1
        $session->set('qt', $qt);
        $session->set('cart', $cart);
        // je sauvegarde l'état de mon panier à l'attribut de session 'cart'

    }
    public function less($id)
    {
        //Nous récupérons ou créons une session gâce à la classe RequsStack (service)
        $session = $this->rs->getSession();

        $cart = $session->get('cart', []);
        $qt = $session->get('qt', 0);
        if($cart[$id] != 1)
        {
            $cart[$id]--;
            $qt--;
        }else
        {
            unset($cart[$id]);
            $qt--;
        }

        $session->set('qt', $qt);
        $session->set('cart', $cart);
    }
    public function modifQuantite($id, $newQt)
    {   
        $session = $this->rs->getSession();

        $cart = $session->get('cart', []);
        $qt = $session->get('qt', 0);

        $diff = $cart[$id] - $newQt ;
        $qt -= $diff;

        $cart[$id] = $newQt;

        $session->set('qt', $qt);
        $session->set('cart', $cart);
    }

    public function remove($id)
    {
        $session = $this->rs->getSession();
        $cart= $session->get('cart', []);
        $qt = $session->get('qt', 0);

        if(!empty($cart[$id]))
        {
            $qt -= $cart[$id];
            unset($cart[$id]);
        }
        if($qt < 0){
            $qt = 0;
        }
        $session->set('qt', $qt);
        $session->set('cart', $cart);
    }

    public function deleteCart()
    {
        $this->rs->getSession()->remove('cart');
        $this->rs->getSession()->remove('qt');
    }
    public function cartWithData()
    {
        $session = $this->rs->getSession();
        $cart = $session->get('cart', []);

        //je vais créer un nouveau tableau qui contiendra des objets Produit et les quantités de chaque objet
        $cartWithData = [];

        //Pour chaque $id qui se trouve dans mon tableau $cart, j'ajoute une case au tableau $cartWithData
        //dans chacune des ses cases il y aura un tableau associatif contenant 2 case: une pour Produit et une pour quantity
        foreach($cart as $id => $quantity)
        {
            $produit = $this->repo->find($id);
            $cartWithData[] = [
                'produit' => $produit,
                'quantity' => $quantity
            ];
        }

        return $cartWithData;

    }

    public function total()
    {
        $cartWithData = $this->cartWithData();
        $total = 0 ;

        foreach($cartWithData as $item)
        {
            $totalItem = $item['produit']->getPrix()/100 * $item['quantity'];
            $total += $totalItem;
        }

        return $total;

    }
    public function saveCommande($data, $user)
    {
        
        // dd($data);
        $commande = new Commande();//remplissage de la table commande
        $reference = $this->generateUuid();
        $address = $data['checkout']['address'];
        $transport = $data['checkout']['transport'];
        $informations = $data['checkout']['informations'];
        

        $commande->setReference($reference)
             ->setNomTransporteur($transport->getNomTransporteur())
             ->setPrixTransporteur($transport->getPrice())
             ->setNom($address->getNom())
             ->setPrenom($address->getPrenom())
             ->setAdresseLivraison($address)
             ->setMoreInformations($informations)
             ->setQuantitePanier($data['data']['quantite'])//voir dans CartServices.php
             ->setTotal($data['data']['subTotalHT'])
             ->setUser($user)
             ->setCreatedAt(new DateTime());
      

        return $reference;
    }
    public function generateUuid()
    {
        // Initialise le générateur de nombres aléatoires Mersenne Twister
        mt_srand((double)microtime()*100000);

        //strtoupper : Renvoie une chaîne en majuscules
        //uniqid : Génère un identifiant unique
        $charid = strtoupper(md5(uniqid(rand(), true)));

        //Générer une chaîne d'un octet à partir d'un nombre
        $hyphen = chr(45);

        //substr : Retourne un segment de chaîne
        $uuid = ""
        .substr($charid, 0, 8).$hyphen
        .substr($charid, 8, 4).$hyphen
        .substr($charid, 12, 4).$hyphen
        .substr($charid, 16, 4).$hyphen
        .substr($charid, 20, 12);
        
        return $uuid;
    }
    
   
}