<?php

namespace AgileCore\Models;

use AgileCore\Core\Communication\Mailer;
use AgileCore\Database\SQL;
use AgileCore\Utils\Dbg;
use DateTime;
use Exception;

class Mail extends Model{

    const STORAGE = "mails";

    public const COLUMNS = [
        "id",
        "user_id",
        "content",
        "from_mail",
        "from_name",
        "basket_id",
        "command_id",
        "try_counter",
        "target",
        "sent_at",
        "created_at",
        "subject",
        "not_before"
    ];

    public const CONDITIONS = [
        "user_id" => "filterStrictPositiveInt nullable",
        "content" => "filterString",
        "target" => FILTER_VALIDATE_EMAIL,
        "sent_at" => "filterDate nullable",
        "created_at" => "filterDate",
        "subject" => "filterString",
        "from_mail" => FILTER_VALIDATE_EMAIL,
        "from_name" => "filterString",
        "basket_id" => "filterStrictPositiveInt nullable",
        "command_id" => "filterStrictPositiveInt nullable",
        "try_counter" => "filterPositiveInt",
        "not_before" => "filterDate nullable"
    ];

    var $user_id = null;
    var $content = "";
    var $target = "";
    var $sent_at = null;
    var $created_at = null;
    var $subject = "";
    var $basket_id = null;
    var $command_id = null;
    var $from_mail = "";
    var $from_name = "";
    var $try_counter = 0;
    var $not_before = null;

    /**
     * Retourne la liste des mails en attente d'être envoyés
     *
     * @param int $limit
     * @return Mail[]
     */
    public static function getPendingMails(int $limit = 5) {
        $query = "SELECT * FROM " . self::STORAGE . " WHERE sent_at IS NULL AND (not_before IS NULL OR not_before >= CURRENT_TIMESTAMP)";
        return SQL::instantiateAll(SQL::db()->query($query), self::class);
    }

    /**
     * Envoi le mail depuis la liste des mails en attente
     */
    public function sendFromQueue() {
        $this->try_counter++;
        try {
            $result = (new Mailer())
                ->from($this->from_mail, $this->from_name)
                ->to($this->target)
                ->send($this->subject, $this->content);
            if ($result == true) {
                $this->sent_at = new DateTime();
            }
        } catch (Exception $e) {
            Dbg::error("An error occurred sending mail from queue: " . $e->getMessage());
        }
        $this->save();
    }

}
