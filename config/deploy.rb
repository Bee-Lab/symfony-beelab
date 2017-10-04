set :stages,        %w(production staging)
set :default_stage, "production"
set :stage_dir,     "app/config/deploy"
require 'capistrano/ext/multistage'

set :application, "customize"
set(:domain) { "#{domain}" }
set :keep_releases, 3
set :user,        "beelab"
set :var_path,    "var"
set :cache_path,  var_path + "/cache"
set :log_path,    var_path + "/log"
set :repository,  "git@bitbucket.org:beelab/customize.git"
set :scm,         :git
set :model_manager, "doctrine"

set :use_composer,    true
set :composer_options, "--no-dev --verbose --prefer-dist --optimize-autoloader --classmap-authoritative --no-progress"
set :use_sudo,        false
set :shared_children, [log_path, web_path + "/uploads"]

set :writable_dirs,       [cache_path, log_path, web_path + "/uploads"]
set :webserver_user,      "www-data"
set :permission_method,   :acl
set :use_set_permissions, true
set :symfony_console,     "bin/console"
set :interactive_mode,    false

# Be more verbose by uncommenting the following line
#logger.level = Logger::MAX_LEVEL

set :dump_assetic_assets, false

set :default_environment, { 
    'APP_ENV' => 'prod',
    'APP_DEBUG' => '0',
    'APP_MAILER_URL' => 'null://localhost'
}

default_run_options[:pty] = true

after "deploy", "deploy:cleanup"
