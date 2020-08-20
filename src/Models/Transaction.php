<?php

namespace AgileCore\Models;

use AgileCore\Core\Payment\PaymentMethod;
use AgileCore\Utils\Dbg;

class Transaction extends Model {

    use Referenceable;

    public const STORAGE = "transactions";
    public const REFERENCE_PREFIX = "TRS";
    public const NAME = "transaction";

    public const COLUMNS = [
      "id",
      "reference",
      "amount",
      "payment_mode_id",
      "payment_mode_method",
      "created_at",
      "status",
      "command_id",
      "partner_response",
      "transaction_type"
    ];

    public const CONDITIONS = [
        "amount" => "filterStrictPositiveFloat",
        "payment_mode_id" => "filterStrictPositiveInt nullable",
        "payment_mode_method" => "filterString nullable",
        "created_at" => "filterDate",
        "status" => "filterTransactionStatus",
        "command_id" => "filterStrictPositiveInt",
        "partner_response" => "filterString nullable",
        "transaction_type" => "filterTransactionType"
    ];

    public const STATUS_WAITING_PAYMENT = "waiting";
    public const STATUS_SUCCESS = "success";
    public const STATUS_ERROR = "error";
    public const STATUS_CANCELLED = "cancelled";

    public const TYPE_PAYMENT = "payment";
    public const TYPE_REFUND = "refund";

    public const STATUS = [
        self::STATUS_WAITING_PAYMENT,
        self::STATUS_SUCCESS,
        self::STATUS_ERROR,
        self::STATUS_CANCELLED
    ];

    var $amount = 0.0;
    var $payment_mode_id = null;
    var $payment_mode_method = null;
    var $created_at = null;
    var $status = self::STATUS_WAITING_PAYMENT;
    var $command_id = 0;
    var $partner_response = "";
    var $transaction_type = self::TYPE_PAYMENT;
    private $_command = null;

    /**
     * Créé une transaction à partir d'une commande
     *
     * @param Command $command
     * @param PaymentMethod $paymentMethod
     * @return Transaction
     */
    public static function createFromCommand(Command $command, ?PaymentMethod $paymentMethod) {
       $transaction = new Transaction();
       $transaction->amount = $command->basket()->getTotalTTC();
       $transaction->payment_mode_id = $paymentMethod ? $paymentMethod->id : null;
       $transaction->payment_mode_method = $paymentMethod ? $paymentMethod->getMethod() : null;
       $transaction->status = self::STATUS_WAITING_PAYMENT;
       $transaction->command_id = $command->id;
       $transaction->partner_response = "";
       $transaction->transaction_type = self::TYPE_PAYMENT;
       $transaction->save();
       return $transaction;
    }

    /**
     * Retourne la commande associée à la transaction
     *
     * @return Command
     */
    public function command() {
        if(is_null($this->_command)) $this->_command = new Command($this->command_id);
        return $this->_command;
    }

    /**
     * Met à jour le status de la transaction
     *
     * @param string $status
     */
    public function setStatus(string $status) {
        $this->status = $status;
        $this->save();
        $command = $this->command();
        if($status == Transaction::STATUS_SUCCESS) {
            $command->setStatus(Command::STATUS_PAYED);
        } else if($status == Transaction::STATUS_ERROR) {
            $command->setStatus(Command::STATUS_ERROR);
        } else if($status == Transaction::STATUS_CANCELLED) {
            $command->setStatus(Command::STATUS_CANCELED_BY_PLATFORM);
        } else if($status == Transaction::STATUS_WAITING_PAYMENT) {
            $command->setStatus(Command::STATUS_PAYMENT_WAITING);
        }
    }

    public function toArray($excludedKeys = []) {
        $parentArray = parent::toArray($excludedKeys);
        if(!in_array('command', $excludedKeys) && !is_null($this->command())) $parentArray["command"] = $this->command()->toArray();
        return $parentArray;
    }

}
