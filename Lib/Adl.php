<?php


namespace AcMarche\MarcheTail\Lib;

use AcMarche\MarcheTail\Lib\Mailer;
use Symfony\Component\HttpFoundation\Request;
use AcMarche\MarcheTail\Lib\Twig;

class Adl
{
    public function generateForm(): string
    {
        $twig     = Twig::LoadTwig();
        $inscrits = $this->handleInscription();

        return $twig->render(
            'eco/_inscription_newsletter.html.twig',
            [
                'inscrits' => $inscrits,
            ]
        );
    }

    public function handleInscription(): bool
    {
        $request = Request::createFromGlobals();
        $nom     = $request->get('nom');
        $prenom  = $request->get('prenom');
        $email   = $request->get('email');
        $rgpd    = $request->get('rgpd', false);

        if ($nom && $prenom && $email) {
            Mailer::sendInscription($nom, $prenom, $email, $rgpd);

            return true;
        }

        return false;
    }
}
