<?php

namespace App\Command;

use App\Repository\LocationRepository;
use App\Service\WeatherUtil;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'weather:location',
    description: 'Pobiera prognozę pogody dla określonej lokalizacji na podstawie jej ID',
)]
class WeatherLocationCommand extends Command
{
    private WeatherUtil $weatherUtil;
    private LocationRepository $locationRepository;
    public function __construct(WeatherUtil $weatherUtil, LocationRepository $locationRepository)
    {
        parent::__construct();
        $this->weatherUtil = $weatherUtil;
        $this->locationRepository = $locationRepository;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('id', InputArgument::REQUIRED, 'ID lokalizacji');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $locationId = $input->getArgument('id');
        $location = $this->locationRepository->find($locationId);

        if (!$location) {
            $io->error(sprintf('Lokalizacja z ID %d nie została znaleziona.', $locationId));
            return Command::FAILURE;
        }

        $measurements = $this->weatherUtil->getWeatherForLocation($location);

        $io->writeln(sprintf('Lokalizacja: %s, %s', $location->getCity(), $location->getCountry()));


        if (empty($measurements)) {
            $io->writeln("Brak dostępnych pomiarów dla tej lokalizacji.");
        } else {
            foreach ($measurements as $measurement) {
                $io->writeln(sprintf(
                    "\tData: %s, Temperatura: %s°C, Wilgotność: %s%%, Ciśnienie: %s hPa",
                    $measurement->getDate()->format('Y-m-d'),
                    $measurement->getTemperature(),
                    $measurement->getHumidity(),
                    $measurement->getPressure()
                ));
            }
        }

        return Command::SUCCESS;
    }
}
