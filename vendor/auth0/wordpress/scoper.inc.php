<?php

declare(strict_types=1);

use Isolated\Symfony\Component\Finder\Finder;

return [
    'prefix' => 'Auth0\\WordPress\\Vendor',

    'finders' => [
        Finder::create()
            ->files()
            ->ignoreVCS(true)
            ->notName('/.*\\.dist|Makefile|scoper.inc.php|rector.php|opslevel.yml|build.sh|public-signing-key.pub|composer.json|composer.lock/')
            ->exclude([
                'doc',
                'test',
                'test_old',
                'tests',
                'Tests',
                'vendor-bin',
            ])
			->in(['vendor', '.']),

        Finder::create()->append([
            'composer.json',
        ]),
    ],

    'exclude-namespaces' => [
        '/^Auth0\\\\WordPress\\\\/',
        '/^Auth0\\\\WordPress/',
        '/^Psr\\\/',
    ],

	'expose-global-constants' => false,
	'expose-global-classes'   => false,
	'expose-global-functions' => false,

    'patchers' => [],
];
