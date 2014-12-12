set :stages,        %w(production staging)
set :default_stage, "production"
set :stage_dir,     "app/config/deploy"
require 'capistrano/ext/multistage'

set :application, "customize"
set(:domain) { "#{domain}" }
set :keep_releases, 3
set :user,        "beelab"
set :deploy_to,   "/var/www/vhost/customize"
set :app_path,    "app"
set :repository,  "git@bitbucket.org:beelab/customize.git"
set :scm,         :git
set :model_manager, "doctrine"

set :use_composer,    true
set :use_sudo,        false
set :shared_files,    [app_path + "/config/parameters.yml"]
set :shared_children, ["var/logs", web_path + "/uploads"]

set :writable_dirs,       ["var/cache", "var/logs", web_path + "/uploads"]
set :webserver_user,      "www-data"
set :permission_method,   :acl
set :use_set_permissions, true
set :symfony_console,     "bin/console"

# Be more verbose by uncommenting the following line
# logger.level = Logger::MAX_LEVEL

set :dump_assetic_assets, true

default_run_options[:pty] = true
