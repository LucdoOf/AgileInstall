<?php

namespace AgileCore\Core\Payment;

use AgileCore\Utils\Dbg;
use Stripe\Card;
use Stripe\Exception\ApiErrorException;
use Stripe\PaymentIntent;
use Stripe\StripeClient;

class StripeSDK {

    /** @var StripeClient|null Client Stripe à utiliser pour les appels */
    public $client = null;

    /** @var StripeSDK Paterne singleton */
    private static $instance = null;

    public function __construct() {
        $this->client = new StripeClient(getenv("STRIPE_SECRET_KEY"));
    }

    /**
     * Récupère l'instance de StripeSDK en cours d'utilisation ou en créé une
     *
     * @return StripeSDK
     */
    public static function getInstance() {
        if(is_null(static::$instance)) static::$instance = new StripeSDK();
        return static::$instance;
    }

    /**
     * Vérifie si une card existe et si elle existe, la retourne
     *
     * @param $cardId string
     * @return \Stripe\PaymentMethod|bool
     */
    public function checkoutCard($cardId) {
        if(empty($cardId)) {
            Dbg::error("An empty stripe card id has been passed to StripeSDK::checkoutCard");
            return false;
        }
        try {
            return $this->client->paymentMethods->retrieve($cardId);
        } catch (ApiErrorException $e) {
            Dbg::error("An error occurred retrieving stripe card " . $cardId . ": " . $e->getMessage());
            return false;
        }
    }

    /**
     * Créé et confirme un payment intent
     *
     * @param $cardId
     * @param $amount
     * @return bool|PaymentIntent
     */
    public function executeCardPayment($cardId, $amount) {
        try {
            return $this->client->paymentIntents->create([
                "amount" => $amount * 100,
                "currency" => "eur",
                "confirm" => true,
                "payment_method" => $cardId
            ]);
        } catch (ApiErrorException $e) {
            Dbg::critical("An error occurred creating payment intent with card " . $cardId . " for an amount of " . $amount . " €: " . $e->getMessage());
            return false;
        }
    }

}
