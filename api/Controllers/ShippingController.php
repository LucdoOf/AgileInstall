<?php

namespace AgileAPI\Controllers;

use AgileCore\Core\Shipping\Transporter;
use AgileInstall\Config;

class ShippingController {

    public function getAvailableTransporters() {
        $toReturn = [];
        foreach (Config::AVAILABLE_TRANSPORTERS as $transporter) {
            $toReturn[] = $transporter::toArray();
        }
        return $toReturn;
    }

}
