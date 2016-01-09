JavaScript and CSS Asset Management
===================================

In production environments, JavaScript and CSS files are delivered in a concatenated and compressed format.

ownCloud creates individual JavaScript and CSS files and saves them in a folder called 'assets' in the web root. This folder must be owned by the web server user and is used for static delivery of these files.

.. note:: Test this thoroughly on production systems as it should work reliably
   with core apps, but you may encounter problems with community/third-party apps.

Parameters
----------

.. code-block:: php

  <?php

    'asset-pipeline.enabled' => true,


You can set this parameters in the :file:`config/config.php`
