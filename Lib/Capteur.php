<?php

namespace AcMarche\MarcheTail\Lib;

use AcMarche\Issep\Indice\IndiceUtils;
use AcMarche\Issep\Repository\StationRemoteRepository;
use AcMarche\Issep\Repository\StationRepository;
use AcMarche\Issep\Utils\FeuUtils;

class Capteur
{
    private StationRepository $stationRepository;
    private StationRemoteRepository $stationRemoteRepository;
    private IndiceUtils $indiceUtils;

    public function __construct()
    {
        $this->stationRepository = new StationRepository(new StationRemoteRepository());
        $this->indiceUtils       = new IndiceUtils($this->stationRepository);
    }

    public function getCapteurs(): array
    {
        Env::loadEnv();
        $stations = $this->stationRepository->getStations();
        $indices  = $this->stationRepository->getIndices();
        $this->indiceUtils->setIndices($stations, $indices);
        foreach ($stations as $station) {
            $station->color = FeuUtils::colorGrey();
            if ($station->last_indice) {
                $station->color = FeuUtils::color($station->last_indice->aqi_value);
            }
        }

        return $stations;
    }
}
