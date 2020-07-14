<?php

namespace AgileCore\Core\Shipping\Transporters;

use AgileCore\Core\Shipping\Transporter;
use DateTime;

class DHLTransporter extends Transporter {

    /**
     * @inheritDoc
     */
    public static function getName(): string { return 'DHL'; }

    /**
     * @inheritDoc
     */
    public static function getIdentifier(): string { return 'dhl'; }

    /**
     * @inheritDoc
     */
    public static function getTrackingUrl(string $commandReference): string { return "https://dhl.com/$commandReference"; }

    /**
     * @inheritDoc
     */
    public static function getShippingStatus(string $commandReference): string { return self::SHIPPING_STATUS_DELIVERED; }

    /**
     * @inheritDoc
     */
    public static function estimateDeliveryDate(DateTime $from = null): DateTime { return new DateTime(); }

    /**
     * @inheritDoc
     */
    public static function getDeliveryUsualDelay() { return 60*60*24; }

}
