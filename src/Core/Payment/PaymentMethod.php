<?php

namespace AgileCore\Core\Payment;

use AgileCore\Models\Command;
use AgileCore\Models\Model;
use AgileCore\Models\Transaction;

abstract class PaymentMethod extends Model {

    public const PAYMENT_MODE_STRIPE_CARD = "stripe-card";

    var $holder_name = "";

    /**
     * Déclenche le processus de paiement (création de la transaction et paiement)
     *
     * @param Command $command
     * @return bool
     */
    public function payCommand(Command $command) {
        $transaction = Transaction::createFromCommand($command, $this);
        $result =  $this->executePayment($transaction);
        if($result) $transaction->setStatus(Transaction::STATUS_SUCCESS);
        else $transaction->setStatus(Transaction::STATUS_ERROR);
        return $result;
    }

    /**
     * Créé un mode de paiement depuis un paramètre option dépendant du mode de paiement
     *
     * @param $holder_name
     * @param $options
     * @return mixed Retourne le mode de paiement créé ou false si une erreur est survenue
     */
    public abstract static function create(string $holder_name, $options);

    /**
     * Effectue une demande de paiement depuis le prestataire
     *
     * @param Transaction $transaction
     * @return bool
     */
    public abstract function executePayment(Transaction $transaction): bool;

    /**
     * Renvoie le nom du mode de paiement
     *
     * @return string
     */
    public abstract static function getMethod(): string;

    /**
     * Retourne les informations connues sur le moyen de paiement
     *
     * @param bool $public
     * @return string
     */
    public abstract function getInformation(bool $public = true) : string;

}
