set :stages,        %w(production staging)
set :default_stage, "production"
set :stage_dir,     "config/deploy"
require 'capistrano/ext/multistage'

set :application, "customize"
set(:domain) { "#{domain}" }
set :keep_releases, 3
set :user,        "beelab"
set :var_path,    "var"
set :web_path,    "public"
set :log_path,    var_path + "/log"
set :repository,  "git@bitbucket.org:beelab/customize.git"
set :scm,         :git
set :model_manager, "doctrine"

# You can use an old-style var/log/parameters.yml file with db parameters, to support db dump
#set :app_config_path, log_path
#set :app_config_file, "parameters.yml"

set :use_composer,    true
set :composer_options, "-qoan --no-dev --prefer-dist --no-progress --no-suggest"
set :use_sudo,        false
set :shared_children, [log_path, web_path + "/uploads"]

set :writable_dirs,       [var_path, log_path, web_path + "/uploads"]
set :webserver_user,      "www-data"
set :permission_method,   :acl
set :use_set_permissions, true
set :symfony_console,     "bin/console"
set :interactive_mode,    false
set :git_shallow_clone,   1

# Be more verbose by uncommenting the following line
#logger.level = Logger::MAX_LEVEL

set :assets_install, true
set :assets_install_path, web_path

set :default_environment, { 
  'APP_ENV' => 'prod',
  'APP_DEBUG' => '0',
  'APP_MAILER_URL' => 'null://localhost'
}

default_run_options[:pty] = true

after "deploy", "deploy:cleanup"