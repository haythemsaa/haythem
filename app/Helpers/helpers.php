<?php

if (!function_exists('setting')) {
    /**
     * Get a setting value
     */
    function setting($key, $default = null)
    {
        return \App\Models\Setting::get($key, $default);
    }
}

if (!function_exists('set_setting')) {
    /**
     * Set a setting value
     */
    function set_setting($key, $value, $type = 'string', $group = 'general')
    {
        return \App\Models\Setting::set($key, $value, $type, $group);
    }
}

if (!function_exists('translate')) {
    /**
     * Get translated value based on current locale
     */
    function translate($key, $params = [])
    {
        return __($key, $params);
    }
}

if (!function_exists('format_currency')) {
    /**
     * Format currency with symbol
     */
    function format_currency($amount, $currency = null)
    {
        $currency = $currency ?? setting('app.currency', 'DH');
        return number_format($amount, 2, ',', ' ') . ' ' . $currency;
    }
}

if (!function_exists('format_date')) {
    /**
     * Format date according to app settings
     */
    function format_date($date, $format = null)
    {
        if (!$date) return '';

        $format = $format ?? setting('app.date_format', 'd/m/Y');

        if (is_string($date)) {
            $date = \Carbon\Carbon::parse($date);
        }

        return $date->format($format);
    }
}

if (!function_exists('format_datetime')) {
    /**
     * Format datetime according to app settings
     */
    function format_datetime($datetime, $format = null)
    {
        if (!$datetime) return '';

        $format = $format ?? setting('app.datetime_format', 'd/m/Y H:i');

        if (is_string($datetime)) {
            $datetime = \Carbon\Carbon::parse($datetime);
        }

        return $datetime->format($format);
    }
}

if (!function_exists('notify')) {
    /**
     * Create a notification
     */
    function notify($notifiable, $title, $message, $type = 'email', $data = [])
    {
        return \App\Models\Notification::create([
            'notifiable_type' => get_class($notifiable),
            'notifiable_id' => $notifiable->id,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'data' => $data,
            'status' => 'pending',
        ]);
    }
}

if (!function_exists('flash_success')) {
    /**
     * Add success flash message
     */
    function flash_success($message)
    {
        session()->flash('success', $message);
    }
}

if (!function_exists('flash_error')) {
    /**
     * Add error flash message
     */
    function flash_error($message)
    {
        session()->flash('error', $message);
    }
}

if (!function_exists('flash_warning')) {
    /**
     * Add warning flash message
     */
    function flash_warning($message)
    {
        session()->flash('warning', $message);
    }
}

if (!function_exists('flash_info')) {
    /**
     * Add info flash message
     */
    function flash_info($message)
    {
        session()->flash('info', $message);
    }
}

if (!function_exists('active_menu')) {
    /**
     * Check if current route matches menu item
     */
    function active_menu($route, $active_class = 'active')
    {
        return request()->routeIs($route) ? $active_class : '';
    }
}

if (!function_exists('user_can')) {
    /**
     * Check if current user has permission
     */
    function user_can($permission)
    {
        return auth()->check() && auth()->user()->can($permission);
    }
}

if (!function_exists('user_has_role')) {
    /**
     * Check if current user has role
     */
    function user_has_role($role)
    {
        return auth()->check() && auth()->user()->hasRole($role);
    }
}

if (!function_exists('days_until')) {
    /**
     * Calculate days until a date
     */
    function days_until($date)
    {
        if (!$date) return null;

        if (is_string($date)) {
            $date = \Carbon\Carbon::parse($date);
        }

        return now()->diffInDays($date, false);
    }
}

if (!function_exists('is_expired')) {
    /**
     * Check if date is expired
     */
    function is_expired($date)
    {
        if (!$date) return false;

        if (is_string($date)) {
            $date = \Carbon\Carbon::parse($date);
        }

        return $date->isPast();
    }
}

if (!function_exists('status_badge')) {
    /**
     * Generate Bootstrap badge for status
     */
    function status_badge($status, $labels = [])
    {
        $colors = [
            'active' => 'success',
            'inactive' => 'secondary',
            'pending' => 'warning',
            'completed' => 'success',
            'cancelled' => 'danger',
            'approved' => 'success',
            'rejected' => 'danger',
            'draft' => 'secondary',
        ];

        $color = $colors[$status] ?? 'primary';
        $label = $labels[$status] ?? ucfirst($status);

        return '<span class="badge bg-' . $color . '">' . $label . '</span>';
    }
}

if (!function_exists('vehicle_status_badge')) {
    /**
     * Get Bootstrap badge class for vehicle status
     */
    function vehicle_status_badge($status)
    {
        return match ($status) {
            'disponible' => 'success',
            'en_mission' => 'primary',
            'en_maintenance' => 'warning',
            'en_reparation' => 'danger',
            'hors_service' => 'dark',
            'vendu' => 'secondary',
            default => 'secondary',
        };
    }
}

if (!function_exists('vehicle_status_label')) {
    /**
     * Get human-readable label for vehicle status
     */
    function vehicle_status_label($status)
    {
        return match ($status) {
            'disponible' => 'Disponible',
            'en_mission' => 'En mission',
            'en_maintenance' => 'En maintenance',
            'en_reparation' => 'En réparation',
            'hors_service' => 'Hors service',
            'vendu' => 'Vendu',
            default => ucfirst($status),
        };
    }
}

if (!function_exists('intervention_status_badge')) {
    /**
     * Get Bootstrap badge class for intervention status
     */
    function intervention_status_badge($status)
    {
        return match ($status) {
            'en_attente' => 'warning',
            'en_cours' => 'info',
            'terminee' => 'success',
            'annulee' => 'danger',
            default => 'secondary',
        };
    }
}

if (!function_exists('intervention_priority_badge')) {
    /**
     * Get Bootstrap badge class for intervention priority
     */
    function intervention_priority_badge($priority)
    {
        return match ($priority) {
            'urgente' => 'danger',
            'haute' => 'warning',
            'moyenne' => 'info',
            'faible' => 'secondary',
            default => 'secondary',
        };
    }
}

if (!function_exists('format_distance')) {
    /**
     * Format distance in kilometers
     */
    function format_distance($kilometers)
    {
        return number_format($kilometers, 0, ',', ' ') . ' km';
    }
}

if (!function_exists('format_consumption')) {
    /**
     * Format fuel consumption
     */
    function format_consumption($consumption)
    {
        return number_format($consumption, 2, ',', '.') . ' L/100km';
    }
}

if (!function_exists('expiry_badge')) {
    /**
     * Get badge class based on days until expiry
     */
    function expiry_badge($expiryDate)
    {
        $days = days_until($expiryDate);

        if (!$days) return 'secondary';

        if ($days < 0) {
            return 'danger'; // Expired
        } elseif ($days <= 7) {
            return 'danger'; // Expires very soon
        } elseif ($days <= 30) {
            return 'warning'; // Expires soon
        } else {
            return 'success'; // Valid
        }
    }
}

if (!function_exists('notification_icon')) {
    /**
     * Get icon class for notification type
     */
    function notification_icon($type)
    {
        return match ($type) {
            'critical' => 'fas fa-exclamation-triangle text-danger',
            'warning' => 'fas fa-exclamation-circle text-warning',
            'info' => 'fas fa-info-circle text-info',
            'success' => 'fas fa-check-circle text-success',
            default => 'fas fa-bell text-secondary',
        };
    }
}

if (!function_exists('user_avatar')) {
    /**
     * Get user avatar URL or initials
     */
    function user_avatar($user, $size = '40')
    {
        if ($user->avatar ?? null) {
            return asset('storage/' . $user->avatar);
        }

        // Generate initials-based avatar URL
        $name = $user->name ?? 'User';
        $initials = substr($name, 0, 1);
        if (str_contains($name, ' ')) {
            $parts = explode(' ', $name);
            $initials = substr($parts[0], 0, 1) . substr($parts[1], 0, 1);
        }

        return "https://ui-avatars.com/api/?name={$initials}&size={$size}&background=random";
    }
}

if (!function_exists('file_size_format')) {
    /**
     * Format file size in human-readable format
     */
    function file_size_format($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $power = $bytes > 0 ? floor(log($bytes, 1024)) : 0;

        return number_format($bytes / pow(1024, $power), 2, ',', ' ') . ' ' . $units[$power];
    }
}

if (!function_exists('format_phone')) {
    /**
     * Format phone number
     */
    function format_phone($phone)
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Format as +212 6XX XX XX XX for Morocco
        if (strlen($phone) === 10 && str_starts_with($phone, '0')) {
            $phone = '212' . substr($phone, 1);
        }

        if (strlen($phone) === 12 && str_starts_with($phone, '212')) {
            return '+' . substr($phone, 0, 3) . ' ' . substr($phone, 3, 1) . ' '
                . substr($phone, 4, 2) . ' ' . substr($phone, 6, 2) . ' '
                . substr($phone, 8, 2) . ' ' . substr($phone, 10, 2);
        }

        return $phone;
    }
}
