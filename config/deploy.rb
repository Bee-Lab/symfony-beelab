set :stages,        %w(production staging)
set :default_stage, "staging"
set :stage_dir,     "config/deploy"
require 'capistrano/ext/multistage'

set :application, "customize"
set(:domain) { "#{domain}" }
set :keep_releases, 3
set :var_path,    "var"
set :web_path,    "public"
set :cache_path,  var_path + "/cache"
set :log_path,    var_path + "/log"
set :repository,  "git@bitbucket.org:beelab/customize.git"
set :scm,         :git
set :model_manager, "doctrine"

# You can use an old-style var/log/parameters.yml file with db parameters, to support db dump
#set :app_config_path, log_path
#set :app_config_file, "parameters.yml"

set :use_composer,    true
set :composer_bin,    "/usr/local/bin/composer"
set :composer_options, "-qoan --no-dev --prefer-dist --no-progress --no-suggest"
set :use_sudo,        false
set :shared_children, [log_path, web_path + "/uploads"]

set :writable_dirs,       [var_path, log_path, cache_path, web_path + "/uploads"]
set :webserver_user,      "www-data"
set :permission_method,   :acl
set :use_set_permissions, true
set :symfony_console,     "bin/console"
set :interactive_mode,    false
set :git_shallow_clone,   1

# Be more verbose by uncommenting the following line
#logger.level = Logger::MAX_LEVEL

set :dump_assetic_assets, false
set :assets_install, false

set :default_environment, { 
  'APP_ENV' => 'prod',
  'APP_DEBUG' => '0',
  'APP_MAILER_URL' => 'null://localhost'
}

default_run_options[:pty] = true

namespace :assets do
  desc 'Run the precompile task locally and rsync with shared'
  task :precompile do
    capifony_pretty_print "--> Precompile assets"
    run_locally('./node_modules/.bin/encore production')
    run_locally('touch assets.tgz && rm assets.tgz')
    run_locally('tar zcvf assets.tgz public/build/')
    run_locally('mv assets.tgz public/build/')
    capifony_puts_ok
  end

  desc 'Upload precompiled assets'
  task :upload_assets do
    capifony_pretty_print "--> Install remote assets"
    upload "public/build/assets.tgz", "#{release_path}/assets.tgz"
    run "cd #{release_path}; tar zxvf assets.tgz; rm assets.tgz"
    capifony_puts_ok
  end
end

before 'deploy:update_code', 'assets:precompile' unless fetch(:skip_compile, false)
after 'deploy:create_symlink', 'assets:upload_assets'
after "deploy", "deploy:cleanup"
