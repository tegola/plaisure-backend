<?php
namespace Deployer;

require 'recipe/laravel.php';
require 'recipe/npm.php';

set('allow_anonymous_stats', false);
set('git_tty', true); // Allocate tty for git clone. Default value is false.
set('default_stage', 'testing');
set('deploy_path', '/var/www/vhosts/prontogioco.it/{{stage}}');
set('repository', 'git@bitbucket.org:tegola/prontogioco.git');
set('bin/php', '/opt/plesk/php/7.1/bin/php');
set('bin/composer', function() { // Always use composer.phar
    run("cd {{release_path}} && curl -sS https://getcomposer.org/installer | {{bin/php}}");
    $composer = '{{release_path}}/composer.phar';

    return '{{bin/php}} ' . $composer;
});

// Hosts
host('testing')
	->stage('testing')
	->user('prontogioco')
	->hostname('vps512931.ovh.net');

host('production')
	->stage('production')
	->user('prontogioco')
	->hostname('vps512931.ovh.net');
    
// Tasks
task('php_path', function() {
	run('PATH=/opt/plesk/php/7.1/bin:$PATH');
});
task('npm:build', function() {
	run("cd {{release_path}} && {{bin/npm}} run production");
});

after('deploy:update_code', 'npm:install'); // Install NPM packages
after('npm:install', 'npm:build'); // Build NPM packages
before('deploy:vendors','php_path'); // Set php path so laravel uses it
after('deploy:failed', 'deploy:unlock'); // Unlock if deploy fails
before('deploy:symlink', 'artisan:migrate'); // Migrate database before link to the new release

