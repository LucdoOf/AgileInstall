<?php

namespace AgileAPI\Controllers;

use AgileCore\Models\Model;
use AgileCore\Models\Transaction;

class TransactionsController extends Controller {

    public function getTransactions($page){
        return Model::listToArray(Transaction::page($page-1, $this->getSortKey() ?? "created_at DESC", $this->getFilters()));
    }

    public function getTransaction($id) {
        $transaction = new Transaction($id);
        if($transaction->exist()) {
            return $transaction->toArray();
        } else {
            return $this->error404("Transaction introuvable");
        }
    }

}
