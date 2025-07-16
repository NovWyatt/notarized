<?php

// app/Console/Commands/UpdateSessionLocationsCommand.php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserSession;
use App\Services\LocationService;

class UpdateSessionLocationsCommand extends Command
{
    protected $signature = 'sessions:update-locations';
    protected $description = 'Update location for sessions with Unknown location';

    public function handle(LocationService $locationService)
    {
        $unknownSessions = UserSession::where('location', 'Unknown')
            ->orWhere('location', 'like', '%Unknown%')
            ->get();

        $this->info("Found {$unknownSessions->count()} sessions with unknown location");

        $bar = $this->output->createProgressBar($unknownSessions->count());
        $bar->start();

        $updated = 0;

        foreach ($unknownSessions as $session) {
            $newLocation = $locationService->getLocationFromIP($session->ip_address);

            if ($newLocation !== 'Unknown') {
                $session->update(['location' => $newLocation]);
                $updated++;
            }

            $bar->advance();

            // Tránh rate limit
            usleep(100000); // 0.1 second
        }

        $bar->finish();
        $this->line('');
        $this->info("Updated {$updated} sessions with new location data");
    }
}
