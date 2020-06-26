<?php

namespace AgileAPI\Controllers;

use AgileAPI\AgileAPI;
use AgileCore\Models\Address;
use AgileCore\Models\Basket;
use AgileCore\Models\BasketEntry;
use AgileCore\Models\Command;
use AgileCore\Models\Model;
use AgileCore\Models\User;
use AgileCore\Utils\Dbg;
use Cake\Core\App;
use DateTime;
use http\Env\Request;
use Mpdf\MpdfException;

class CommandsController extends Controller {

    public function getCommands(){
        return Model::listToArray(Command::getAll([], "order_date"));
    }

    public function getCommandHistory($id){
        $command = new Command($id);
        if($command->exist()){
            return Model::listToArray($command->getVersions());
        } else {
            return $this->error404("Commande introuvable");
        }
    }

    public function getCommandsPage($page){
        return Model::listToArray(Command::page($page-1, $this->getSortKey() ?? "order_date DESC", $this->getFilters()));
    }

    public function getCommandBasket($id){
        $command = new Command($id);
        if($command->exist()){
            return Basket::select(['id' => $command->basket_id])->toArray();
        } else {
            return $this->error404("Commande introuvable");
        }
    }

    public function updateCommandStatus($id){
        $command = new Command($id);
        if($command->exist()){
            $command->status = AgileAPI::getInstance()->getPayload()["status"];
            $valid = $command->isValid();
            if($valid === true){
                if($command->status !== Command::STATUS_DRAFT) {
                    $command->save();
                    return $this->message("Status mis à jour");
                } else {
                    return $this->error400("Impossible de passer une commande en brouillon");
                }
            } else {
                return $this->error400("Champ invalide (" . $valid . ")");
            }
        } else {
            return $this->error404("Commande introuvable");
        }
    }

    public function createCommand(){
        if(isset($this->payload()['user']) && isset($this->payload()['entries'])) {
            $userArray = $this->payload()['user'];
            $entryArray = $this->payload()['entries'];
            $shippingAddressArray = $this->payload()['shipping_address'];
            $billingAddressArray = $this->payload()['billing_address'];
            if(!empty($entryArray)) {
                $user = isset($userArray["id"]) ? new User($userArray["id"]) : null;
                if (is_null($user)) {
                    $user = new User();
                    $user->hydrate($userArray);
                    $user->inscription_date = new DateTime();
                    $valid = $user->isValid();
                    if ($valid === true) {
                        $user->save();
                    } else {
                        return $this->error400("Champ utilisateur invalide (" . $valid . ')');
                    }
                } else if (!$user->exist()) {
                    return $this->error400("Utilisateur introuvable");
                }
                $shippingAddress = isset($shippingAddressArray['id']) ? new Address($shippingAddressArray["id"]) : null;
                if (is_null($shippingAddress)) {
                    $shippingAddress = new Address();
                    $shippingAddress->hydrate($shippingAddressArray);
                    $shippingAddress->user_id = $user->id;
                    $valid = $shippingAddress->isValid();
                    if ($valid === true) {
                        $shippingAddress->save();
                    } else {
                        return $this->error400("Champ adresse de livraison invalide (" . $valid . ')');
                    }
                } else if(!$shippingAddress->exist()) {
                    return $this->error400("Adresse de livraison introuvable");
                }
                $billingAddress = isset($billingAddressArray['id']) ? new Address($billingAddressArray["id"]) : null;
                if (is_null($billingAddress)) {
                    $billingAddress = new Address();
                    $billingAddress->hydrate($billingAddressArray);
                    $billingAddress->user_id = $user->id;
                    $valid = $billingAddress->isValid();
                    if ($valid === true) {
                        $billingAddress->save();
                    } else {
                        return $this->error400("Champ adresse de facturation invalide (" . $valid . ')');
                    }
                } else if(!$billingAddress->exist()) {
                    return $this->error400("Adresse de facturation introuvable");
                }

                $command = new Command();
                $command->shipping_address_id = $shippingAddress->id;
                $command->billing_address_id = $billingAddress->id;
                $command->order_date = new DateTime();
                $basket = new Basket();
                $basket->user_id = $user->id;
                $basket->save();
                $command->basket_id = $basket->id;
                $command->save();
                foreach ($entryArray as $entry) {
                    $basketEntry = new BasketEntry();
                    $basketEntry->basket_id = $basket->id;
                    $basketEntry->hydrate($entry);
                    $valid = $basketEntry->isValid();
                    if($valid !== true) {
                        $command->delete();
                        return $this->error400("Champ entrée panier invalide (" . $valid . ')');
                    } else {
                        $basketEntry->save();
                    }
                }
                $command->status = Command::STATUS_PAYMENT_WAITING;
                $valid = $command->isValid();
                if($valid === true) {
                    try {
                        $command->generateInvoicePDF();
                        $command->save();
                        return $this->success('Commande ' . $command->reference . ' créée', ['command' => $command->toArray()]);
                    } catch (MpdfException $e) {
                        Dbg::logs('Error generating PDF ' . $e->getMessage());
                        return $this->error400("Une erreur est survenue lors de la génération de la facture");
                    }
                } else {
                    return $this->error400("Champ commande invalide (" . $valid . ")");
                }
            } else {
                return $this->error400("Panier vide");
            }
        } else {
            if(!isset($this->payload()['user'])) return $this->error400('Utilisateur non renseigné');
            if(!isset($this->payload()['entries'])) return $this->error400('Panier non renseigné');
        }
    }

    public function deleteCommand($id){
        $command = new Command($id);
        if($command->exist()){
            if($command->status === Command::STATUS_DRAFT) {
                $command->delete();
                return $this->message('Commande supprimée');
            } else {
                return $this->error400('Impossible de supprimer une commande qui n\'est pas un brouillon, changez son statut à la place');
            }
        } else {
            return $this->error404('Commande introuvable');
        }
    }

}
