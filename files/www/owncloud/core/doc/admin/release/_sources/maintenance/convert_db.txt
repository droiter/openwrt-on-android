=======================================================
Converting From SQLite to MySQL, MariaDB, or PostgreSQL
=======================================================

You can convert a SQLite database to a more performing MySQL, MariaDB or 
PostgreSQL database with the ownCloud command line tool ``occ``, which first 
appeared in ownCloud version 7.0.0. You must have ownCloud 7 to perform this 
conversion. SQLite is sufficient for testing and for very small installations, 
but for production servers with multiple users it is better to use MySQL, 
MariaDB or PostgreSQL.

Please see :doc:`../configuration/occ_command` for more information on using 
the ``occ`` command.

Running the Conversion
----------------------

First set up the new database, in these examples called "new_db_name". In your 
ownCloud root folder call:

.. code-block:: bash

  php occ db:convert-type [options] type username hostname database

The available values for the ``type`` parameter are:

* mysql (for MySQL or MariaDB)
* pgsql (for PostgreSQL)

Conversion Options
------------------

* ``--port="3306"``  Your database port (optional, specify the port if it is a 
  non-standard port).
* ``username``  A database admin user.  
* ``--password="mysql_user_password"`` The database admin password, if there is 
  one.
* ``--clear-schema``  Clear the schema in the new DB (optional).
* ``--all-apps``  By default, tables for enabled apps are converted. Use this 
  option to convert tables of deactivated apps.

.. note:: The converter searches for apps in your configured app folders and 
   uses the schema definitions in the apps to create the new table. So 
   the tables of removed apps will not be converted even with the option 
   ``--all-apps``

This example converts the SQLite DB and tables for all installed apps to 
MySQL/MariaDB:

.. code-block:: bash

  php occ db:convert-type --all-apps mysql oc_mysql_user 127.0.0.1 new_db_name

To complete the conversion, type ``yes`` when prompted ``Continue with the 
conversion?`` On success the converter will automatically configure the new 
database in your ownCloud configuration in ``config.php``.

Unconvertible Tables
--------------------

After conversion some obsolete database tables may be left over. The converter 
will tell you what these are:

.. code-block:: bash

  The following tables will not be converted:
  oc_permissions
  ...

You can ignore these tables.
Here is a list of known old tables:

* oc_calendar_calendars
* oc_calendar_objects
* oc_calendar_share_calendar
* oc_calendar_share_event
* oc_fscache
* oc_log
* oc_media_albums
* oc_media_artists
* oc_media_sessions
* oc_media_songs
* oc_media_users
* oc_permissions
* oc_queuedtasks
* oc_sharing
