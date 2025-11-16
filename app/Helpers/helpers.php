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
