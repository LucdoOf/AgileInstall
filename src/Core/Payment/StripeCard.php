<?php

namespace AgileCore\Core\Payment;

use AgileCore\Models\Transaction;
use AgileCore\Utils\Dbg;
use AgileCore\Utils\Str;
use Stripe\PaymentIntent;

class StripeCard extends PaymentMethod {

    public const STORAGE = "stripe_card_creditors";
    public const NAME = "stripe_card_creditor";

    public const COLUMNS = [
      "id",
      "card_id",
      "holder_name",
      "last_4",
      "exp_year",
      "exp_month",
      "network"
    ];

    var $card_id = 0;
    var $holder_name = "";
    var $last_4 = "";
    var $exp_year = "";
    var $exp_month = "";
    var $network = "";

    /**
     * @inheritDoc
     */
    public function executePayment(Transaction $transaction): bool {
        $card = $this->checkoutCard();
        if($card) {
            $result = StripeSDK::getInstance()->executeCardPayment($this->card_id, $transaction->amount);
            return $result ? true : false;
        } else {
            Dbg::critical("An unknown stripe card have been used for executePayment: " . $this->card_id . " " . $this->id);
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public static function getMethod(): string {
        return static::PAYMENT_MODE_STRIPE_CARD;
    }

    /**
     * @inheritDoc
     */
    public static function create(string $holder_name, $options) {
        $card = StripeSDK::getInstance()->checkoutCard($options);
        if($card) {
            $stripeCard = new StripeCard();
            $stripeCard->holder_name = $holder_name;
            $stripeCard->card_id = $card->id;
            $stripeCard->hydrate($card->card);
            $result = $stripeCard->isValid();
            if($result !== false) {
                return $stripeCard;
            } else {
                Dbg::error("An error occurred creating a stripe intent: " . $result);
                return false;
            }
        }
        return false;
    }

    /**
     * Retourne le PaymentIntent Stripe associé
     *
     * @return bool|PaymentMethod
     */
    public function checkoutCard() {
        return StripeSDK::getInstance()->checkoutCard($this->card_id);
    }

    /**
     * @inheritDoc
     */
    public function getInformation(bool $public = true): string {
        return "**** **** **** " . $this->last_4;
    }

}
