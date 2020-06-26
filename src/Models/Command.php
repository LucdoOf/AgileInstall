<?php

namespace AgileCore\Models;

use AgileCore\Utils\Dbg;
use DateTime;
use Mpdf\Mpdf;
use Mpdf\MpdfException;
use Mpdf\Output\Destination;

class Command extends Model {

    use Referenceable;
    use Versionable;

    public const STORAGE = "commands";
    public const REFERENCE_PREFIX = "CMD";
    public const SQL_JOINS = [
      [Basket::class => "id", Command::class => "basket_id"],
      [User::class => "id", Basket::class => "user_id"],
      [BasketEntry::class => "basket_id", Basket::class => "id"]
    ];

    public const COLUMNS = [
        "id",
        "reference",
        "basket_id",
        "order_date",
        "status",
        "billing_address_id",
        "shipping_address_id",
        "invoice_pdf"
    ];

    public const CONDITIONS = [
        "basket_id" => [FILTER_VALIDATE_INT, ['min_range' => 1]],
        "order_date" => "filterDate",
        "status" => "filterCommandStatus",
        "billing_address_id" => [FILTER_VALIDATE_INT, ['min_range' => 1]],
        "shipping_address_id" => [FILTER_VALIDATE_INT, ['min_range' => 1]],
        "invoice_pdf" => "filterUrl nullable"
    ];

    public const STATUS_DRAFT = "draft";
    public const STATUS_PAYMENT_WAITING = "payment-waiting";
    public const STATUS_PAYED = "payed";
    public const STATUS_EXPEDITED = "expedited";
    public const STATUS_IN_DELIVERING = "in-delivering";
    public const STATUS_RECEIVED = "received";
    public const STATUS_CANCELED_BY_BUYER = "canceled-by-buyer";
    public const STATUS_CANCELED_BY_PLATFORM = "canceled-by-platform";
    public const STATUS_REFUNDED = "refunded";
    public const STATUS_ERROR = "error";

    public const STATUS = [
      self::STATUS_DRAFT,
      self::STATUS_PAYMENT_WAITING,
      self::STATUS_PAYED,
      self::STATUS_EXPEDITED,
      self::STATUS_IN_DELIVERING,
      self::STATUS_RECEIVED,
      self::STATUS_CANCELED_BY_BUYER,
      self::STATUS_CANCELED_BY_PLATFORM,
      self::STATUS_REFUNDED,
      self::STATUS_ERROR
    ];

    var $basket_id = -1;
    var $order_date = null;
    var $status = self::STATUS_DRAFT;
    var $basket = null;
    var $billing_address_id = -1;
    var $billing_address = null;
    var $shipping_address_id = -1;
    var $shipping_address = null;
    var $invoice_pdf = null;

    /**
     * Sauvegarde l'objet et en créé une version alternative
     */
    public function save() {
        if (empty($this->invoice_pdf)) try {
            $this->generateInvoicePDF();
        } catch (MpdfException $e) {
            Dbg::logs('Error creating invoice pdf ' . $e->getMessage());
        }

        $this->versionableSave();
        parent::save();
    }

    /**
     * Retourne le panier associé à la commande
     *
     * @return Basket
     */
    public function basket() {
        if(is_null($this->basket)){
            $this->basket = new Basket($this->basket_id);
        }
        return $this->basket;
    }

    /**
     * @see Model
     */
    public function toArray() {
        $parentArray = parent::toArray();
        if(!is_null($this->basket())) $parentArray["basket"] = $this->basket()->toArray();
        if(!is_null($this->billing_address())) $parentArray["billing_address"] = $this->billing_address()->toArray();
        if(!is_null($this->shipping_address())) $parentArray["shipping_address"] = $this->shipping_address()->toArray();
        return $parentArray;
    }

    /**
     * @see Model
     */
    public function delete() {
        if ($this->basket()->exist()) $this->basket()->delete();
        parent::delete();
    }

    /**
     * Retourne l'adresse de facturation
     *
     * @return Address
     */
    public function billing_address() {
        if(is_null($this->billing_address)){
            $this->billing_address = new Address($this->billing_address_id);
        }
        return $this->billing_address;
    }

    /**
     * Retourne l'adresse de livraison
     *
     * @return Address
     */
    public function shipping_address() {
        if(is_null($this->shipping_address)){
            $this->shipping_address = new Address($this->shipping_address_id);
        }
        return $this->shipping_address;
    }

    /**
     * Génère le pdf de facture
     *
     * @throws MpdfException
     */
    public function generateInvoicePDF() {
        $command = $this;
        ob_start();
        require ROOT . "/public/assets/pdf/invoice.php";
        $content = ob_get_clean();
        $mpdf = new Mpdf([
            'tempDir'             => '/tmp/mpdf/',
            'setAutoBottomMargin' => 'pad',
            'margin-bottom'       => 0,
            'default_font'        => 'sans-serif',
        ]);

        $stylesheet = file_get_contents(ROOT . '/public/assets/pdf/style.css');
        $mpdf->WriteHTML($stylesheet, \Mpdf\HTMLParserMode::HEADER_CSS);
        $mpdf->WriteHTML($content);

        $mpdf->Output(ROOT . "/public/documents/commands/invoices/" . $this->reference . ".pdf", Destination::FILE);
        //echo $content;
        $this->invoice_pdf = public_url() . "documents/commands/invoices/" . $this->reference . ".pdf";
    }

}
