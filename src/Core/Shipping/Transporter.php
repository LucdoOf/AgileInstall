<?php

namespace AgileCore\Core\Shipping;

use AgileInstall\Config;
use DateTime;

/**
 * Class Transporter
 *
 * Un transporteur gère la livraison de colis
 * @package AgileCore\Core\Shipping
 */
abstract class Transporter {

    public const SHIPPING_STATUS_IN_DELIVERY = 'in-delivering';
    public const SHIPPING_STATUS_DELIVERED = 'delivered';

    /**
     * Retourne le nom public du transporteur
     *
     * @return string
     */
    public static abstract function getName() : string;

    /**
     * Retourne l'identifiant du constructeur
     *
     * @return string
     */
    public static abstract function getIdentifier() : string;

    /**
     * Retourne une url de tracking à partir d'une référence de commande
     *
     * @param string $commandReference Référence de la commande à tracker
     * @return string
     */
    public static abstract function getTrackingUrl(string $commandReference) : string;

    /**
     * Retourne un status de livraison à partir d'une référence de commande
     *
     * @param string $commandReference Status de la livraison
     * @return string
     */
    public static abstract function getShippingStatus(string $commandReference) : string;

    /**
     * Estime la date de livraison en fonction d'une date de départ
     *
     * @param DateTime|null $from Date de départ, null pour date actuelle
     * @return DateTime
     */
    public static abstract function estimateDeliveryDate(DateTime $from = null) : DateTime;

    /**
     * Retourne le délai de livraison usuel pour ce type de livraison
     *
     * @return int[]|int Array avec comme première position la tranche basse de l'estimation en secondes, en deuxième position la tranche haute
     * Ou bien simplement un int avec le délai estimé fixe
     */
    public static abstract function getDeliveryUsualDelay();

    /**
     * Retourne un transporteur par son identifier parmi la liste des transporteurs disponibles pour l'install
     *
     * @param $identifier
     * @return Transporter|string|null
     */
    public static function getByIdentifier($identifier) {
        foreach (Config::AVAILABLE_TRANSPORTERS as $transporter) {
            if ($transporter::getIdentifier() === $identifier) return $transporter;
        }
        return null;
    }

    /**
     * Retourne le transporteur sous forme d'une array simple
     *
     * @return array
     */
    public static function toArray() {
       return [
           'name' => static::getName(),
           'identifier' => static::getIdentifier()
       ];
    }

}
