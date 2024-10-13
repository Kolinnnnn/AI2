<?php

namespace App\Controller;

use App\Entity\Location;
use App\Repository\LocationRepository;
use App\Repository\MeasurementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class WeatherController extends AbstractController
{
    #[Route('/weather/{city}/{country?}', name: 'app_weather_city')]
    public function city(string $city, ?string $country, LocationRepository $locationRepository, MeasurementRepository $measurementRepository): Response
    {
        $location = $locationRepository->findOneByCityAndCountry($city, $country);
        $measurements = $measurementRepository->findByLocation($location);
        return $this->render('weather/city.html.twig', [
            'location' => $location,
            'measurements' => $measurements,
        ]);
    }
}
