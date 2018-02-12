<?php
namespace Deployer;

require 'recipe/laravel.php';
require 'recipe/npm.php';

set('allow_anonymous_stats', false);
set('git_tty', true); // Allocate tty for git clone. Default value is false.
set('base_path', '/var/www/vhosts/prontogioco.it');
set('default_stage', 'testing');
set('repository', 'git@bitbucket.org:tegola/prontogioco.git');
set('bin/php', '/opt/plesk/php/7.1/bin/php');
set('bin/composer', function() { // Always use composer.phar
    run("cd {{release_path}} && curl -sS https://getcomposer.org/installer | {{bin/php}}");
    $composer = '{{release_path}}/composer.phar';

    return '{{bin/php}} ' . $composer;
});

// Shared files/dirs between deploys 
add('shared_files', []);
add('shared_dirs', []);

// Writable dirs by web server 
add('writable_dirs', []);

// Hosts
host('testing')
	->stage('testing')
	->user('prontogioco')
	->hostname('vps512931.ovh.net')
    ->set('deploy_path', '{{base_path}}/testing');
    
// Tasks
/*
task('build', function() {
    run('cd {{release_path}} && build');
});
*/

task('php_path', function() {
	run('PATH=/opt/plesk/php/7.1/bin:$PATH');
});
before('deploy:vendors','php_path');

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

// Migrate database before symlink new release.
before('deploy:symlink', 'artisan:migrate');

