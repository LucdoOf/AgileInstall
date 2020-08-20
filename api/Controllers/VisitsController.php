<?php

namespace AgileAPI\Controllers;

use AgileCore\Models\Model;
use AgileCore\Models\Transaction;
use AgileCore\Models\Visit;

class VisitsController extends Controller {

    public function getVisits($page) {
        return Model::listToArray(Visit::page($page-1, $this->getSortKey() ?? "visit_date DESC", $this->getFilters()));
    }

}
