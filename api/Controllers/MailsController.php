<?php

namespace AgileAPI\Controllers;

use AgileCore\Core\Communication\Mailer;
use AgileCore\Models\Mail;
use AgileCore\Models\User;
use AgileCore\Utils\Dbg;
use Exception;

class MailsController extends Controller {

    public function sendMail() {
        $user_id = $this->payload("user_id");
        $target = $this->payload("target", "");
        $content = $this->payload("content", "");
        $subject = $this->payload("subject", "");
        if(filter_var($target, FILTER_VALIDATE_EMAIL) && !empty($content) && !empty($subject)) {
            if(!is_null($user_id) && !(new User($user_id))->exist()) return $target-$this->error400("Utilisateur " . $user_id . " introuvable");
            try {
                $result = Mailer::administratorMail($content, $subject, $content, $user_id);
                if($result) return $this->message("Mail envoyé avec succès");
                else {
                    Dbg::error("Unknown error on sending mail (" . $target . ", " . $content . ", " . $subject . ")");
                    return $this->error400("Une erreur est survenue lors de l'envoi du mail (2)");
                }
            } catch (Exception $e) {
                Dbg::error("Error on sending mail " . $e->getMessage());
                return $this->error400("Une erreur est survenue lors de l'envoi du mail");
            }
        } else {
            return $this->error400("Veuillez rentrer un destinataire, un contenu et un sujet valide");
        }
    }

}
