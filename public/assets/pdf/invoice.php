<?php
/** @var $command Command */

$basket = $command->basket();
$entries = $basket->entries();
$user = $basket->user();
$billing_address = $command->billing_address();
$shipping_address = $command->shipping_address();

use AgileConfig\Config;
use AgileCore\Models\Command;

?>
<style lang="css">
    #payment-information {
        padding: 10px;
        width: 300px;
        border: 1px solid #22a8bd;
        background-color: #ebf8fa;
        float: right;
        clear: none;
    }
    #buyer-information {
        float: left;
        width: 300px;
        clear: none;
    }
    #total-container {
        margin-top: 20px;
        float: right;
        width: 50%;
    }
    .address {
        float: left;
        width: 50%;
    }
</style>

<div id="invoice">
    <header class="row">
        <div id="buyer-information">
            <div class="big buyer-field"><?= $user->lastname . " " . $user->firstname ?></div>
            <div class="big buyer-field"><?= $user->mail ?></div>
        </div>
        <div id="payment-information">
            <h2>Payée</h2>
            <div>Vendue par <?= Config::BUSINESS_NAME ?></div>
            <div>Numéro de TVA <?= Config::TVA_NUMBER ?></div>
        </div>
    </header>
    <section id="addresses" class="row">
        <div class="address" style="margin-right: 100px;">
            <h3>Adresse de facturation</h3>
            <div><?= $billing_address->lastname . " " . $billing_address->firstname ?></div>
            <div><?= $billing_address->address ?></div>
            <div><?= $billing_address->city . ", " . $billing_address->zipcode ?></div>
            <div><?= $billing_address->country ?></div>
        </div>
        <div class="address">
            <h3>Adresse de livraison</h3>
            <div><?= $shipping_address->lastname . " " . $shipping_address->firstname ?></div>
            <div><?= $shipping_address->address ?></div>
            <div><?= $shipping_address->city . ", " . $shipping_address->zipcode ?></div>
            <div><?= $shipping_address->country ?></div>
        </div>
    </section>
    <section id="command-information" class="column">
        <h2>Informations sur la commande</h2>
        <div>Date de la commande: <?= parseDate($command->order_date) ?></div>
        <div>Référence de la commande: <?= $command->reference ?></div>
    </section>
    <section id="command-details">
        <h2>Détail de la facture</h2>
        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Quantité</th>
                    <th>Réduction appliquée</th>
                    <th>Prix unitaire HT</th>
                    <th>Prix unitaire TTC</th>
                    <th>Total TTC</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($entries as $entry): ?>
                    <?php $product = $entry->product() ?>
                    <tr>
                        <td><?= $product->name . ", " . $product->description ?></td>
                        <td><?= $entry->quantity ?></td>
                        <td><?= $entry->hasDiscount() ? parsePercentage($entry->entry_discount) : '&mdash;' ?></td>
                        <td><?= parsePrice($entry->getUnitPriceHT()) ?></td>
                        <td><?= parsePrice($entry->getUnitPriceTTC()) ?></td>
                        <td><?= parsePrice($entry->getPriceTTC()) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div id="total-container">
            <h2>Total</h2>
            <table>
                <thead>
                    <tr>
                        <th>Hors taxes</th>
                        <th>Toutes taxes comprises</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?= parsePrice($basket->getTotalHT()) ?></td>
                        <td><?= parsePrice($basket->getTotalTTC()) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
    <footer>
        <div><?= Config::BUSINESS_NAME ?></div>
        <div>Numéro de TVA <?= Config::TVA_NUMBER ?></div>
        <div>Siret <?= Config::SIRET ?></div>
        <div><?= Config::HEAD_OFFICE ?></div>
    </footer>
</div>
