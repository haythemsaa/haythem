<?php

namespace App\Listeners;

use App\Events\VehicleCreated;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendVehicleCreatedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(VehicleCreated $event): void
    {
        $vehicle = $event->vehicle;

        Log::info("Vehicle created: {$vehicle->registration_number}");

        // Notify fleet managers and admins
        $users = User::role(['Fleet Manager', 'Super Admin'])->get();

        foreach ($users as $user) {
            Notification::create([
                'user_id' => $user->id,
                'type' => 'info',
                'title' => 'Nouveau véhicule ajouté',
                'message' => "Le véhicule {$vehicle->registration_number} ({$vehicle->brand->name} {$vehicle->vehicleModel->name}) a été ajouté à la flotte.",
                'action_url' => '/vehicles/' . $vehicle->id,
            ]);
        }

        // Log to audit trail
        activity()
            ->performedOn($vehicle)
            ->causedBy(auth()->user())
            ->log('vehicle_created');
    }

    /**
     * Handle a job failure.
     */
    public function failed(VehicleCreated $event, \Throwable $exception): void
    {
        Log::error('Failed to send vehicle created notification', [
            'vehicle_id' => $event->vehicle->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
