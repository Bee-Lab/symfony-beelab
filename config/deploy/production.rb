set :domain,           "customize";
set :branch,           "master"
set :symfony_env_prod, "prod"
set :deploy_to,        "/var/www/vhost/customize"

role :web,        domain                      # Your HTTP server, Apache/etc
role :app,        domain                      # This may be the same as your `Web` server
role :db,         domain, :primary => true    # This is where Symfony2 migrations will run

