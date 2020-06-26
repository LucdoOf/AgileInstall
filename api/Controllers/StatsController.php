<?php

namespace AgileAPI\Controllers;

use AgileCore\Models\Command;
use AgileCore\Models\Model;
use DateTime;

class StatsController extends Controller {

    public function todayCommands(){
        $todayMidnight = strtotime('today midnight');
        $tomorrowMidnight = $todayMidnight + 60*60*24;
        $todayCommands = Command::getAll("UNIX_TIMESTAMP(order_date) >= $todayMidnight AND UNIX_TIMESTAMP(order_date) <= $tomorrowMidnight");
        return Model::listToArray($todayCommands);
    }

    public function monthCommands(){
        $firstDayOfMonthMidnight = strtotime('first day of this month midnight');
        $lastDayOfMonthMidnight = strtotime('last day of this month midnight');
        $todayCommands = Command::getAll("UNIX_TIMESTAMP(order_date) >= $firstDayOfMonthMidnight AND UNIX_TIMESTAMP(order_date) <= $lastDayOfMonthMidnight");
        return Model::listToArray($todayCommands);
    }

}
