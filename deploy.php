<?php
namespace Deployer;

require 'recipe/laravel.php';
require 'vendor/deployer/recipes/rsync.php';

// Fix date message
date_default_timezone_set('Europe/Rome');

// Configuration

set('ssh_type', 'native');
set('ssh_multiplexing', true);
set('writable_mode', 'chmod');
set('default_stage', 'test');

set('rsync_src', __DIR__);
set('rsync', [ // FIXME: Try switching to add() and avoid repeating everything
    'exclude' => [
        '.git',
        '.sass-cache',
        '.env',
        '*.log*',
        '._*',
        '.DS_Store',
        'deploy.php',
        'resources/assets',
        'node_modules', // built locally
        //'vendor', // Installed server side
        'public/storage',
        'public/hot',
        'storage/*.key'
    ],
    'exclude-file' => false,
    'include' => [],
    'include-file' => false,
    'filter' => [],
    'filter-file' => false,
    'filter-perdir' => false,
    'flags' => 'rz',
    'options' => ['delete'],
    'timeout' => 300,
]);

add('shared_files', []);
add('shared_dirs', []);
add('writable_dirs', []);

// Servers

server('test', 'prontogioco.it')
    ->stage('test')
    ->user('tegola')
    ->identityFile()
    ->set('deploy_path', '/home/tegola/prontogioco.it/test');

server('production', 'prontogioco.it')
    ->stage('production')
    ->user('tegola')
    ->identityFile()
    ->set('deploy_path', '/home/tegola/prontogioco.it/production');

// Tasks

desc('Building CSS and JS locally');
task('asset:build', function() {
    runLocally('npm run production');
});

desc('Running migrations -> artisan:migrate');
task('db:migrate', 'artisan:migrate')->onlyOn('production');

desc('Resetting database');
task('db:refresh', function () {
    run('{{bin/php}} {{release_path}}/artisan migrate:refresh --seed --force');
})->onlyOn('test');

desc('Rolling back the database -> artisan:migrate:rollback');
task('db:rollback', 'artisan:migrate:rollback');

task('deploy', [
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    // 'npm:local:install',
    'asset:build',
    'rsync',
    'deploy:shared',
    // 'deploy:vendors',
    'deploy:writable',
    'db:refresh', // only test
    'db:migrate', // only production
    'artisan:view:clear',
    'artisan:cache:clear',
    'artisan:config:cache',
    'artisan:optimize',
    'deploy:symlink',
    'deploy:unlock',
    'cleanup',
]);

/*
desc('Restart PHP-FPM service');
task('php-fpm:restart', function () {
    // The user must have rights for restart service
    // /etc/sudoers: username ALL=NOPASSWD:/bin/systemctl restart php-fpm.service
    run('sudo systemctl restart php-fpm.service');
});
after('deploy:symlink', 'php-fpm:restart');
*/

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');
//after('deploy:failed', 'db:rollback');
