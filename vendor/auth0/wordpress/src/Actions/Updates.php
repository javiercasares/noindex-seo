<?php

declare(strict_types=1);

namespace Auth0\WordPress\Actions;

use function is_array;
use function is_object;

final class Updates extends Base
{
    protected array $registry = [
        'site_transient_update_plugins' => 'doUpdateCheck',
        'transient_update_plugins' => 'doUpdateCheck',
    ];

    public function doUpdateCheck($plugins)
    {
        // trap($plugins);

        if (! is_object($plugins)) {
            return $plugins;
        }

        if (! isset($plugins->response) || ! is_array($plugins->response)) {
            $plugins->response = [];
        }

        // $plugins->response['auth0/wpAuth0.php'] = (object) [
        //     'slug' => 'auth0',
        //     'new_version' => '5.9',
        //     'url' => 'https://github.com/auth0/wordpress',
        //     'package' => 'https://github.com/auth0/wirdoress/archive/0.2.zip',
        // ];

        return $plugins;
    }
}
