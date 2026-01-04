<?php

namespace Deployer;

require 'recipe/laravel.php';

// Config

set('repository', 'git@github.com:aizensoosuke/calatrava.fr');

add('shared_files', []);
add('shared_dirs', []);
add('writable_dirs', []);

// Tasks
task(
    'npm:build',
    function () {
        runLocally('./vendor/bin/sail npm run build');
    }
);
task(
    'npm:upload',
    function () {
        run('mkdir -p {{release_path}}/public/build');
        upload('public/build/', '{{release_path}}/public/build/');
    }
);
task(
    'horizon:restart',
    function () {
        run('sudo systemctl restart horizon'.get('labels')['horizon-suffix']);
    }
);
task(
    'artisan:app:generate-permissions',
    artisan('app:generate-permissions')
);
task(
    'lunar:hub:install',
    artisan('lunar:hub:install')
);
task('artisan:migrate', artisan('migrate --force --database=migrate', ['skipIfNoEnv']));
/* task('artisan:deploy:email-templates', artisan('deploy:email-templates')); */
/* task('artisan:deploy:scout', artisan('deploy:scout')); */
/* task('artisan:l5-swagger:generate', artisan('l5-swagger:generate')); */

// Hosts

host('staging')
    ->setLabels(
        [
            'horizon-suffix' => '-staging',
        ]
    )
    ->set('hostname', 'calatrava')
    ->set('branch', 'staging')
    ->set('php_version', '8.4')
    ->set('deploy_path', '/srv/staging.calatrava.fr');

host('production')
    ->setLabels(
        [
            'horizon-suffix' => '-prod',
        ]
    )
    ->set('hostname', 'calatrava')
    ->set('branch', 'production')
    ->set('php_version', '8.4')
    ->set('deploy_path', '/srv/calatrava.fr');

// Hooks

task('deploy:info')
    ->verbose();

task('artisan:migrate');
    //->addAfter('lunar:hub:install');
/* ->addAfter('artisan:app:generate-permissions'); */
/* ->addAfter('artisan:deploy:email-templates') */
/* ->addAfter('artisan:deploy:scout') */
/* ->addAfter('artisan:l5-swagger:generate'); */

task('deploy:prepare')
    ->addAfter('npm:build')
    ->addAfter('npm:upload');

after('deploy:success', 'horizon:restart');
after('deploy:failed', 'deploy:unlock');
