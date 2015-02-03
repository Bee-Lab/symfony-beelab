Symfony Beelab Edition
======================

Install with ``composer create-project beelab/symfony-beelab path/to/install``

Then, replace the following contents:

* This file should contain your actual README
* ``composer.json`` should contains your project's name, description, authors, etc.
* replace "%customize%" with your project's name in ``build.xml``
* replace "%customize%" with your project's repository name in ``app/config/deploy.rb``, ``app/config/deploy/staging.rb``,
  and ``app/config/production.rb``

CI
==

There is a predefined ``build.xml`` file, that works with our current internal Jenkins configuration.
