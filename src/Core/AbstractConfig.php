<?php

namespace AgileCore\Core;

use AgileCore\Core\Shipping\Transporter;
use AgileCore\Core\Shipping\Transporters\ChronopostTransporter;
use AgileCore\Core\Shipping\Transporters\ColissimoTransporter;
use AgileCore\Core\Shipping\Transporters\DHLTransporter;
use AgileCore\Core\Shipping\Transporters\DPDTransporter;
use AgileCore\Core\Shipping\Transporters\MondialRelayTransporter;
use AgileCore\Core\Shipping\Transporters\UPSTransporter;

abstract class AbstractConfig {

    /** @var string Nom public du site */
    public const PUBLIC_NAME = "Agile-Web.net";

    /** @var string Nom de business complet, sarl, micro entreprise... */
    public const BUSINESS_NAME = "Agile-Web, micro entreprise gérée par Lucas Garofalo";

    /** @var string Numéro de TVA pour les factures */
    public const TVA_NUMBER = "FR10 882084890";

    /** @var string Siret de l'entreprise */
    public const SIRET = "88208489000012";

    /** @var string Siège social de l'entreprise */
    public const HEAD_OFFICE = "64 rue daniel Mayer, Tours, 37100, France";

    /** @var Transporter[] Liste des transporteurs disponibles */
    public const AVAILABLE_TRANSPORTERS = [
        ChronopostTransporter::class,
        ColissimoTransporter::class,
        DHLTransporter::class,
        DPDTransporter::class,
        MondialRelayTransporter::class,
        UPSTransporter::class
    ];

    /** @var string Adresse mail utilisée dans l'envoi des mails */
    public const MAIL_ADDRESS = "no-reply@agile-web.net";

}
