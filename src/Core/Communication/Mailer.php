<?php

namespace AgileCore\Core\Communication;

use AgileCore\Models\Command;
use AgileCore\Models\Mail;
use AgileCore\Utils\Dbg;
use AgileInstall\Config;
use Cake\Core\App;
use DateTime;
use Exception;
use PHPMailer\PHPMailer\PHPMailer;

class Mailer {

    private $phpMailer;
    private $usingTemplate = false;
    private $user_id;
    private $command_id;
    private $basket_id;

    const SESSION_NO_SPAM = 'no_spam';
    const TEMPLATE_PATH = CONFIG_ROOT . '/mails/';

    public function __construct() {
        $this->phpMailer = new PHPMailer(isDev());
        $this->phpMailer->isSMTP();
        $this->phpMailer->isHTML(true);
        $this->phpMailer->CharSet = 'UTF-8';
        $this->phpMailer->Host = getenv("SMTP_HOST");
        $this->phpMailer->Port = getenv("SMTP_PORT");
        $this->phpMailer->Password = getenv("SMTP_PASSWORD");
        $this->phpMailer->Username = getenv("SMTP_USERNAME");
        $this->phpMailer->SMTPAuth = !empty($this->phpMailer->Password);
    }

    /**
     * Mail depuis le back-office
     *
     * @param $target
     * @param $subject
     * @param $content
     * @param null $userId
     * @return bool
     * @throws Exception
     */
    public static function administratorMail($target, $subject, $content, $userId = null) {
        return (new Mailer())
            ->to($target)
            ->setUserId($userId)
            ->useTemplate("administrator-mail", ["message" => $content, "headline" => $subject])
            ->putInQueue($subject);
    }

    /**
     * @param Command $command
     * @return bool
     * @throws Exception
     */
    public static function orderSuccessMail(Command $command) {
        return (new Mailer())
            ->to($command->basket()->user()->mail)
            ->setUserId($command->basket()->user_id)
            ->useTemplate("order-success", ["command-amount" => $command->basket()->getTotalTTC(), "command-number" => $command->reference, "dashboard-route" => "/"])
            ->putInQueue("Votre commande " . Config::PUBLIC_NAME);
    }

    /**
     * Utilise un template pour le corps du mail
     *
     * @param $template
     * @param array $data
     * @param string $container
     * @param string $locale
     * @return $this
     * @throws Exception
     */
    public function useTemplate($template, $data = [], $container = 'default', $locale = 'fr_FR') {
        $this->usingTemplate = true;

        $content = $this->getTemplateData($template);

        if ($content) {
            if ($container !== null) {
                $templateContainer = $this->getTemplateData('templates/' . $container);
                if ($templateContainer) {
                    $content = self::replaceTemplateData($templateContainer, 'content', $content);
                }
            }

            $generalData = [
                'logo-route'    => '', // TODO
                'home-route'    => '', // TODO
                'contact-route' => '', // TODO
                'facebook-route' => '', // TODO
                'facebook-logo-route' => '', //TODO
                'twitter-route' => '', // TODO
                'twitter-logo-route' => '', // TODO
                'legals'        => Config::BUSINESS_NAME,
                'business-name' => Config::PUBLIC_NAME,
            ];

            foreach ($generalData as $k => $v) {
                if (!key_exists($k, $data)) {
                    self::replaceTemplateData($content, $k, $v);
                }
            }
            foreach ($data as $k => $v) {
                self::replaceTemplateData($content, $k, $v);
            }

            $this->phpMailer->Body = $content;
        } else {
            throw new Exception('Invalid mail template ' . $template);
        }

        return $this;
    }

    /**
     * Retourne le contenu html du template.
     *
     * @param $templateName
     * @return string|false
     */
    private function getTemplateData($templateName) {
        $filename = self::TEMPLATE_PATH . $templateName . '.html';

        if (file_exists($filename)) {
            return file_get_contents($filename);
        }

        Dbg::warning($filename . ' not found');

        return false;
    }

    /**
     * Remplace les valeurs du template entre {{ }}
     *
     * @param string $target
     * @param string $key
     * @param string $value
     * @return string
     */
    private static function replaceTemplateData(string &$target, string $key, string $value) {
        return $target = str_replace("{{" . $key . "}}", $value, $target);
    }

    /**
     * @param $to
     * @param bool $verifySpam
     * @return Mailer
     * @throws Exception
     */
    public function to($to, $verifySpam = false) {
        if (isDev()) {
            $this->addAddress('garbage@agile-web.net', false);
            return $this;
        }

        if (is_array($to)) {
            foreach ($to as $adr) {
                $this->addAddress($adr, $verifySpam);
            }
        } else {
            $this->addAddress($to, $verifySpam);
        }

        return $this;
    }

    /**
     * @param int $command_id
     * @return $this
     */
    public function setCommandId(?int $command_id) {
        $this->command_id = $command_id;
        return $this;
    }

    /**
     * @param int $user_id
     * @return $this
     */
    public function setUserId(?int $user_id) {
        $this->user_id = $user_id;
        return $this;
    }

    /**
     * @param int $basket_id
     * @return $this
     */
    public function setBasketId(?int $basket_id) {
        $this->basket_id = $basket_id;
        return $this;
    }

    /**
     * @param string $address
     * @param bool $verifySpam
     * @return bool
     * @throws Exception
     */
    public function addAddress(string $address, $verifySpam = true) {
        if ($verifySpam && self::isAlreadySentTo($address)) {
            throw new Exception('Mail déjà envoyé à l\'adresse ' . $address);
        }
        if (filter_var($address, FILTER_VALIDATE_EMAIL)) {
            $_SESSION[self::SESSION_NO_SPAM][$address] = time() + 30;
            $this->phpMailer->addAddress($address);
            return true;
        }
        throw new Exception('Incorrect mail address ' . $address);
    }

    /**
     * Si un mail a déjà été envoyé récemment (- 30min) à la même adresse
     *
     * @param $email
     * @return bool
     */
    private static function isAlreadySentTo($email) {
        return isset($_SESSION[self::SESSION_NO_SPAM]) && key_exists($email,
                $_SESSION[self::SESSION_NO_SPAM]) && $_SESSION[self::SESSION_NO_SPAM][$email] > time();
    }

    /**
     * @param $from
     * @param $fromName
     * @param $replyTo
     * @return Mailer
     */
    public function from($from, $fromName = null, $replyTo = null) {
        try {
            if (!is_null($from)) {
                $this->phpMailer->setFrom($from);
            }

            if (!is_null($fromName)) {
                $this->phpMailer->FromName = $fromName;
            }

            $this->phpMailer->addReplyTo(!is_null($replyTo) ? $replyTo : Config::MAIL_ADDRESS);
        } catch (Exception $e) {
            Dbg::error($e);
        }
        return $this;
    }

    /**
     * @param $subject
     * @param $body
     * @return bool
     */
    public function send(string $subject, string $body = null) {
        try {
            if (!empty($this->phpMailer->getToAddresses())) {
                $this->phpMailer->Subject = $subject;
                if (!$this->usingTemplate && !is_null($body)) {
                    $this->phpMailer->Body = $body;
                }
                $this->phpMailer->AltBody = strip_tags($this->phpMailer->Body);

                Dbg::info('Sending a mail to ' . $this->phpMailer->getToAddresses()[0][0]);
                return $this->phpMailer->send();
            }
        } catch (Exception $e) {
            Dbg::critical('An error occurred sending mail ' . $e->getMessage());
        }
        return false;
    }

    /**
     * @param string $subject
     * @param string $body
     * @param int $not_before Timestamp pour l'envoi du mail
     * @return bool
     */
    public function putInQueue(string $subject, string $body = null, int $not_before = 0) {
        if (is_null($body)) {
            $body = $this->phpMailer->Body;
        }
        foreach ($this->phpMailer->getToAddresses() as $to) {
            $mail = new Mail();
            $mail->user_id = $this->user_id;
            $mail->basket_id = $this->basket_id;
            $mail->command_id = $this->command_id;
            $mail->subject = $subject;
            $mail->content = $body;
            $mail->created_at = new DateTime();
            $mail->from_mail = $this->phpMailer->From;
            $mail->from_name = $this->phpMailer->FromName;
            $mail->target = $to[0];
            $mail->not_before = $not_before > 0 ? (new DateTime())->setTimestamp($not_before) : null;
            $mail->try_counter = 0;

            $mail->save();
        }
        return true;
    }

}
