<?php

namespace Deployer;

require 'recipe/symfony.php';

set('application', 'customize');
set('repository', 'git@bitbucket.org:garak/customize.git');
set('git_tty', true);
set('keep_releases', 2);
set('shared_files', []);
set('shared_dirs', ['var/log']);
set('writable_dirs', ['var']);
set('release_name', static function (): string {
    return \date('YmdHis');
});

desc('Update database');
task('deploy:db:update', static function (): void {
    $url = run('grep APP_DATABASE_URL /etc/nginx/sites-available/customize');
    preg_match('/(mysql:\/\/.*[^;])/', $url, $matches);
    run('APP_DATABASE_URL=\''.$matches[0].'\' {{bin/console}} doctrine:schema:update --force {{console_options}}');
});

desc('Copy remote database to local');
task('deploy:db:from_remote_copy', static function (): void {
    $url = run('grep APP_DATABASE_URL /etc/nginx/sites-available/customize');
    preg_match('/mysql:\/\/(\w+):(.+)@.+/', $url, $matches);
    $name = '{{application}}_'.date('YmdHis').'.sql';
    run('mysqldump -u '.$matches[1].' -p'.$matches[2].' {{application}} > /tmp/'.$name.'; gzip /tmp/'.$name);
    runLocally('scp {{user}}@{{hostname}}:/tmp/'.$name.'.gz .');
    $url2 = runLocally('env | grep APP_DATABASE_URL');
    preg_match('/APP_DATABASE_URL=mysql:\/\/(\w+):(.+)@(.+)\/(.+)/', $url2, $matches);
    runLocally('gunzip '.$name.'.gz');
    runLocally('mysql -u'.$matches[1].' -p'.$matches[2].' -h'.$matches[3].' -D'.$matches[4].' < '.$name);
    runLocally('rm '.$name);
});
desc('Precompile assets');
task('deploy:assets:build_local', static function (): void {
    runLocally('./node_modules/.bin/encore production');
    runLocally('touch assets.tgz && rm assets.tgz');
    runLocally('tar zcvf assets.tgz public/build/');
    runLocally('mv assets.tgz public/build/');
});
desc('Upload precompiled assets');
task('deploy:assets:upload', static function (): void {
    runLocally('scp public/build/assets.tgz {{user}}@{{hostname}}:{{deploy_path}}');
    run('mv {{deploy_path}}/assets.tgz {{release_path}}; cd {{release_path}}; tar zxvf assets.tgz; rm assets.tgz');
});

host('production')
    ->setHostname('contabo')
    ->set('deploy_path', '~/sf/{{application}}')
    ->set('user', 'garak')
;

task('deploy:update_code', static function (): void {
    run('git archive --remote {{repository}} --format=tar {{branch}} | (cd {{release_path}} && tar xf -)');
});
// TODO remove this task after new release (post-beta3)
// see https://github.com/deployphp/deployer/issues/2244
task('success', static function (): void {
    info('successfully deployed!');
});

after('deploy:failed', 'deploy:unlock');
after('deploy:prepare', 'deploy:assets:build_local');
before('deploy:symlink', 'deploy:assets:upload');
