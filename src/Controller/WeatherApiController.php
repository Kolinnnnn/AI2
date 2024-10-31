<?php

namespace App\Controller;

use App\Entity\Measurement;
use App\Service\WeatherUtil;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

class WeatherApiController extends AbstractController
{
    #[Route('/api/v1/weather', name: 'app_weather_api')]
    public function index(
        #[MapQueryParameter] string $city,
        #[MapQueryParameter] string $country,
        WeatherUtil $weatherUtil,
        #[MapQueryParameter] string $format = 'json',
        #[MapQueryParameter('twig')] bool $twig = false
    ): Response {

        $measurements = $weatherUtil->getWeatherForCountryAndCity($country, $city);

        if ($format === 'csv') {
            if ($twig) {
                return $this->render('weather_api/index.csv.twig', [
                    'city' => $city,
                    'country' => $country,
                    'measurements' => $measurements,
                ]);

            }

            $csvData = ["city,country,date,temperature,humidity,pressure"];
            foreach ($measurements as $measurement) {
                $csvData[] = sprintf(
                    '%s,%s,%s,%s,%s,%s,%s',
                    $city,
                    $country,
                    $measurement->getDate()->format('Y-m-d'),
                    $measurement->getTemperature(),
                    $measurement->getFahrenheit(),
                    $measurement->getHumidity(),
                    $measurement->getPressure()
                );
            }
            $csvContent = implode("\n", $csvData) . "\n";

            return new Response($csvContent, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="weather.csv"',
            ]);
        }

        $measurementData = array_map(fn(Measurement $m) => [
            'date' => $m->getDate()->format('Y-m-d'),
            'temperature' => $m->getTemperature(),
            'fahrenheit' => $m->getFahrenheit(),
            'humidity' => $m->getHumidity(),
            'pressure' => $m->getPressure(),
        ], $measurements);

        if ($twig) {
            return $this->render('weather_api/index.json.twig', [
                'city' => $city,
                'country' => $country,
                'measurements' => $measurements,
            ]);
        }

        return $this->json([
            'city' => $city,
            'country' => $country,
            'measurements' => $measurementData,
        ]);
    }
}
