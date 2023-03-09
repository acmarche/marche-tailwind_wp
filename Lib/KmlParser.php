<?php


namespace AcMarche\MarcheTail\Lib;

use DOMDocument;
use DOMElement;
use DOMNode;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class KmlParser
{
    public DOMElement|DOMDocument|null $offre = null;

    public PropertyAccessor $propertyAccessor;

    public function __construct()
    {
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    public function parse(string $xmlString): ?DOMDocument
    {
        $this->offre = null;
        try {
            libxml_use_internal_errors(true);
            $domdoc = new DOMDocument();
            $domdoc->loadXML($xmlString);
            $errors = libxml_get_errors();

            libxml_clear_errors();
            if (count($errors) > 0) {
                Mailer::sendError('kml error', 'contenu: '.$xmlString);

                return null;
            }

            $this->offre = $domdoc;

            return $domdoc;
        } catch (\Exception $exception) {
            Mailer::sendError('Erreur avec le kml', $exception->getMessage());

            return null;
        }
    }

    public function getAttributs(string $name): ?string
    {
        $domList = $this->offre->getElementsByTagName($name);
        if ($domList instanceof \DOMNodeList) {
            $domElement = $domList->item(0);
            if ($domElement instanceof DOMElement) {
                return $domElement->nodeValue;
            }
        }

        return null;
    }

    public function getPlacesMark(): \DOMNodeList
    {
        return $this->offre->getElementsByTagName('Placemark');
    }

    public function getValueByTagName(DOMNode $doc, string $name): ?string
    {
        $description = $doc->getElementsByTagName($name);
        if ($description->item(0) !== null) {
            return $description->item(0)->nodeValue;
        }

        return null;
    }

    public function getElementsByTagName(DOMElement $doc, string $name): ?\DOMNode
    {
        $description = $doc->getElementsByTagName($name);
        if ($description->item(0) !== null) {
            return $description->item(0);
        }

        return null;
    }

}
