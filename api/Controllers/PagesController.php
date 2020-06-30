<?php

namespace AgileAPI\Controllers;

use AgileAPI\AgileAPI;
use AgileCore\Models\Model;
use AgileCore\Models\Page;
use AgileCore\Utils\Dbg;
use Exception;
use Gettext\Generator\MoGenerator;
use Gettext\Generator\PoGenerator;
use Gettext\Loader\PoLoader;

class PagesController extends Controller {

    public function getPages(){
        return Model::listToArray(Page::getAll());
    }

    public function getTranslations($locale){
        $loader = new PoLoader();
        try {
            $translations = $loader->loadFile(INSTALL_ROOT . '/public/assets/locale/' . $locale . '/LC_MESSAGES/messages.po');
            $toReturn = [];
            foreach ($translations->getTranslations() as $translation) {
                $toReturn[] = ["id" => $translation->getOriginal(), "translation" => $translation->getTranslation()];
            }
            return $toReturn;
        } catch (Exception $e) {
            Dbg::logs('Error reading po: ' . $e->getMessage());
            return $this->error400('Une erreur est survenue lors de la lecture de fichier de traduction');
        }
    }

    public function updateTranslation($locale, $key){
        $string = $this->payload()['value'];
        $loader = new PoLoader();
        try {
            $translations = $loader->loadFile(INSTALL_ROOT . '/public/assets/locale/' . $locale . '/LC_MESSAGES/messages.po');
            $translation = $translations->find(null, $key);
            if ($translation) {
                $translation->translate($string);
                $poGenerator = new PoGenerator();
                $poGenerator->generateFile($translations, INSTALL_ROOT . '/public/assets/locale/' . $locale . '/LC_MESSAGES/messages.po');
                return $this->message('Traduction mise à jour');
            } else {
                return $this->error404('Clé de traduction inconnue');
            }
        } catch (Exception $e) {
            Dbg::logs('Unable to modify po: ' . $e->getMessage());
            return $this->error400('Une erreur est survenue lors de la mise à jour de la traduction');
        }
    }

    public function refreshTranslations($locale){
        $loader = new PoLoader();
        try {
            $translations = $loader->loadFile(INSTALL_ROOT . '/public/assets/locale/' . $locale . '/LC_MESSAGES/messages.po');
            $generator = new MoGenerator();
            $generator->generateFile($translations, INSTALL_ROOT . '/public/assets/locale/' . $locale . '/LC_MESSAGES/messages.mo');
            return $this->message('Traductions mises à jour');
        } catch (Exception $e) {
            Dbg::logs('Unable to save mo: ' . $e->getMessage());
            return $this->error400('Une erreur est survenue lors de la mise à jour de la traduction');
        }
    }

}
