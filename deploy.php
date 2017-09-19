<?php
namespace Deployer;

require 'recipe/laravel.php';
require 'vendor/deployer/recipes/local.php';
require 'vendor/deployer/recipes/rsync.php';
require 'vendor/deployer/recipes/npm.php';

// Fix date message
date_default_timezone_set('Europe/Rome');

// Configuration
set('bin/php', function() {
    return run('which php-7.0')->toString();
});
set('ssh_type', 'native');
set('ssh_multiplexing', true);
set('writable_mode', 'chmod');
set('default_stage', 'test');
set('local_deploy_path', '/tmp/deployer');
set('repository', 'git@bitbucket.org:tegola/prontogioco.git');

// RSYNC files from /tmp/deployer
set('rsync_src', function() {
    $local_src = get('local_release_path');
    if (is_callable($local_src)){
        $local_src = $local_src();
    }
    return $local_src;
});
add('rsync', [
    'exclude' => [
        '.git',
        '.sass-cache',
        '.env',
        '*.log*',
        '._*',
        '*.todo',
        '.DS_Store',
        'deploy.php',
        'resources/assets',
        'node_modules', // built locally
        'public/storage',
        'public/hot',
        'storage/*.key'
    ]
]);
/*
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
*/

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

task('npm:local:build', function() {
    runLocally("cd {{local_release_path}} && {{local/bin/npm}} run production");
})->desc('Build CSS and JS locally');

task('artisan:migrate')->desc('Run migrations');

task('artisan:migrate:refresh', function() {
    run('{{bin/php}} {{release_path}}/artisan migrate:refresh --seed --force');
})->desc('Reset database')->onlyOn('test');

task('artisan:config:clear', function () {
    run('{{bin/php}} {{release_path}}/artisan config:clear');
})->desc('Execute artisan config:clear');

task('deploy', [
    'local:prepare',           // Create dirs locally
    'local:release',           // Release number locally
    'local:update_code',       // git clone locally
    'local:vendors',           // composer install locally
    'npm:local:install',       // npm install locally
    'npm:local:build',         // Build locally
    'local:symlink',           // Symlink /current locally
    'deploy:prepare',          // Create dirs on server
    'deploy:lock',             // Lock deploys on server
    'deploy:release',          // Release number on server
    'rsync',                   // Send files to server
    'deploy:writable',         // Ensure paths are writable on server
    'deploy:shared',           // Shared and .env linking on server
    'artisan:view:clear',      // Optimize on server
    'artisan:cache:clear',     // Optimize on server
    'artisan:config:cache',    // Optimize on server
    'artisan:config:clear',    // Optimize on server
    // 'artisan:optimize',        // Optimize on server // Removed as per Laravel 5.5 upgrade guide
    'artisan:migrate',         // Migrate DB on production server
    // 'artisan:migrate:refresh', // Refresh DB on test server
    'deploy:symlink',          // Symlink /current on server
    'deploy:unlock',           // Unlock deploys on server
    'cleanup',                 // Cleanup old releases on server
    'local:cleanup'            // Cleanup old releases locally
])->desc('Deploy your project');

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');
