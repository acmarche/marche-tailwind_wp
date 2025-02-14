<?php

namespace AcMarche\MarcheTail\Lib;

use AcMarche\Common\Env;
use AcMarche\Issep\Indice\IndiceUtils;
use AcMarche\Issep\Repository\StationRemoteRepository;
use AcMarche\Issep\Repository\StationRepository;
use AcMarche\Issep\Utils\FeuUtils;

class Capteur
{
    private StationRepository $stationRepository;
    private IndiceUtils $indiceUtils;

    public function __construct()
    {
        $this->stationRepository = new StationRepository(new StationRemoteRepository());
        $this->indiceUtils = new IndiceUtils($this->stationRepository);
    }

    public function getCapteurs(): array
    {
        Env::loadEnv();
        try {
            $stations = $this->stationRepository->getStations();
        } catch (\JsonException $e) {
            $stations = [];
        }
        $this->indiceUtils->setIndices($stations);
        foreach ($stations as $station) {
            $station->color = FeuUtils::colorGrey();
            if ($station->last_indice) {
                $station->color = FeuUtils::color($station->last_indice->aqiValue);
            }
        }

        return $stations;
    }
}
