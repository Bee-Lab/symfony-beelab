<?php

namespace Deployer;

require 'recipe/symfony.php';

set('application', 'customize');
set('repository', 'git@bitbucket.org:garak/{{application}}.git');
set('git_tty', true);
set('keep_releases', 2);
set('shared_files', []);
set('shared_dirs', ['var/log']);
set('writable_dirs', ['var']);
set('release_name', static fn (): string => \date('YmdHis'));

desc('Update database');
task('deploy:db:update', static function (): void {
    $url = run('grep APP_DATABASE_URL /etc/nginx/sites-available/{{application}}');
    \preg_match('/(mysql:\/\/.*[^;])/', $url, $matches);
    run('APP_DATABASE_URL=\''.$matches[0].'\' {{bin/console}} doctrine:schema:update --force {{console_options}}');
});

desc('Copy remote database to local');
task('deploy:db:from_remote_copy', static function (): void {
    $url1 = run('grep APP_DATABASE_URL /etc/nginx/sites-available/{{application}}');
    \preg_match('/mysql:\/\/(\w+):(.+)@.+\/(.+)/', $url1, $matches);
    $name = '{{application}}_'.\date('YmdHis').'.sql';
    run('mysqldump -u '.$matches[1].' -p'.$matches[2].' '.$matches[3].' > /tmp/'.$name.'; gzip /tmp/'.$name);
    runLocally('scp {{user}}@{{hostname}}:/tmp/'.$name.'.gz .');
    $url2 = runLocally('env | grep APP_DATABASE_URL');
    \preg_match('/APP_DATABASE_URL=mysql:\/\/(\w+):(.+)@(.+)\/(.+)/', $url2, $matches);
    runLocally('gunzip '.$name.'.gz');
    runLocally('mysql -u'.$matches[1].' -p'.$matches[2].' -h'.$matches[3].' -D'.$matches[4].' < '.$name);
    runLocally('rm '.$name);
});
desc('Copy local database to remote');
task('deploy:db:to_remote_copy', static function (): void {
    $url1 = runLocally('env | grep APP_DATABASE_URL');
    \preg_match('/mysql:\/\/(\w+):(.+)@(.+)\/(.+)/', $url1, $matches);
    $name = '{{application}}_'.\date('YmdHis').'.sql';
    runLocally('mysqldump -u '.$matches[1].' -p'.$matches[2].' -h'.$matches[3].' '.$matches[4].' > /tmp/'.$name.'; gzip /tmp/'.$name);
    runLocally('scp /tmp/'.$name.'.gz {{user}}@{{hostname}}:/tmp/');
    $url2 = run('grep APP_DATABASE_URL /etc/nginx/sites-available/{{application}}');
    \preg_match('/APP_DATABASE_URL mysql:\/\/(\w+):(.+)@(.+)\/(.+);/', $url2, $matches);
    run('gunzip /tmp/'.$name.'.gz');
    run('mysql -u'.$matches[1].' -p'.$matches[2].' -h'.$matches[3].' -D'.$matches[4].' < /tmp/'.$name);
    run('rm /tmp/'.$name);
});
desc('Precompile assets');
task('deploy:assets:build_local', static function (): void {
    runLocally('./node_modules/.bin/encore production');
    runLocally('tar zcvf assets.tgz public/build/');
    runLocally('mv assets.tgz public/build/');
});
desc('Upload precompiled assets');
task('deploy:assets:upload', static function (): void {
    runLocally('scp public/build/assets.tgz {{user}}@{{hostname}}:{{deploy_path}}');
    run('mv {{deploy_path}}/assets.tgz {{release_path}}; cd {{release_path}}; tar zxvf assets.tgz; rm assets.tgz');
    runLocally('rm public/build/assets.tgz');
});

host('production')
    ->setHostname('myhost') // TODO set your hostname
    ->set('deploy_path', '~/{{application}}')
    ->set('user', 'myuser') // TODO set your username
;

after('deploy:failed', 'deploy:unlock');
after('deploy:prepare', 'deploy:assets:build_local');
before('deploy:symlink', 'deploy:assets:upload');
before('deploy:symlink', 'deploy:db:update');
