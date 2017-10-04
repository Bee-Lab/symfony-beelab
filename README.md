Symfony Beelab Edition
======================

Install with `composer create-project beelab/symfony-beelab path/to/install`

Then, replace the following contents:

* This file should contain your actual README
* `composer.json` should contains your project's name, description, authors, etc.
* replace "%customize%" with your project's name in `build.xml`
* replace "%customize%" with your project's repository name in `config/deploy.rb`, `config/deploy/staging.rb`,
  and `config/production.rb`
* remove references about BeeLab in main layout and in favicon
* remove `LICENSE` file (or replace it with your license)

CI
==

There is a predefined `build.xml` file, that works with our current internal Jenkins configuration.
