==============================
Upgrading Your ownCloud Server
==============================

**Starting with version 7.0.11, ownCloud will be automatically put into 
maintenance mode after downloading upgraded packages. You must take it out of 
maintenance mode and then run the upgrade wizard to complete the upgrade.**

It is best to keep your ownCloud server upgraded regularly, and to install all 
point releases and major releases without skipping any of them. Major releases 
are 6.0 and 7.0, and point releases are intermediate releases for each 
major release. For example, 7.0.1 and 7.0.2 are point releases.

There are multiple ways to keep your ownCloud server upgraded: with the Updater 
App, with your Linux package manager, and by manually upgrading. In this chapter 
you will learn how to keep your ownCloud installation current with your Linux 
package manager, and by manually upgrading.

(See :doc:`update` to learn about the Updater App.)

.. note:: Before upgrading to a new major release, always first review any 
   third-party apps you have installed for compatibility with  
   the new ownCloud release. Any apps that are not developed by ownCloud show a 
   3rd party designation. Install unsupported apps at your own risk. Then, 
   before the upgrade, they must all be disabled. After the upgrade is 
   complete and you are sure they are compatible with the new ownCloud 
   release you may re-enable them.

Preferred Upgrade Method
------------------------

The best method for keeping ownCloud on Linux servers current is by 
configuring your system to use the `openSUSE Build Service 
<http://software.opensuse.org/download.html?project=isv:ownCloud:community& 
package=owncloud>`_ (see :doc:`../installation/linux_installation`); just 
follow the instructions on oBS for setting up your package manager. Then 
stay current by using your Linux package manager to upgrade.

You should always maintain regular backups (see :doc:`../maintenance/backup`), 
and make a backup before every upgrade.

When a new ownCloud release is available you will see a yellow banner in your 
ownCloud Web interface.

.. figure:: ../images/updater-1.png

**Upgrading is disruptive**. When you upgrade ownCloud with your Linux package 
manager, that is just the first step to applying the upgrade. After 
downloading the new ownCloud packages your session will be interrupted, and you 
must run the upgrade wizard to complete the upgrade, which is discussed in the 
next section.

Upgrading With Your Linux Package Manager
-----------------------------------------

When an ownCloud upgrade is available from the openSUSE Build Service 
repository, you can apply it just like any normal Linux upgrade. For example, 
on Debian or Ubuntu Linux this is the standard system upgrade command::

 $ sudo apt-get update && sudo apt-get upgrade
 
Or you can upgrade just ownCloud with this command::

 $ sudo apt-get update && sudo apt-get install owncloud
 
On Fedora, CentOS, and Red Hat Linux use ``yum`` to see all available updates::

 $ yum check-update
 
You can apply all available updates with this command::
 
 $ sudo yum update
 
Or update only ownCloud::
 
 $ sudo yum update owncloud
 
Your Linux package manager only downloads the current ownCloud packages. There 
are two more steps:

* Take your ownCloud server out of maintenance mode (7.0.11+)
* Run the upgrade wizard to perform the final steps of updating the database and 
  apps.

Your Linux package manager only downloads the current ownCloud packages. Then 
your ownCloud server is automatically put into maintenance mode. Take your 
server out of maintenance mode by changing ``'maintenance' => true,`` to 
``'maintenance' => false,`` in ``config.php``, or use the ``occ command``, like 
this example on Ubuntu::

 $ sudo -u www-data php occ maintenance:mode --off

``occ upgrade`` is more reliable, especially on installations with large 
datasets and large numbers of users because it avoids the risk of PHP timeouts. 

.. note:: The ``occ`` command does not download ownCloud updates. You must first 
   download the updated code, and then ``occ`` performs the final upgrade steps.

See :doc:`../configuration/occ_command` to learn more about using the 
``occ`` command, and see the **Setting Strong Directory Permissions** section 
of :doc:`../installation/installation_wizard` to learn how to find your 
HTTP user.

When the upgrade is successful you will be returned to the login screen.

If the upgrade fails, then you must try a manual upgrade.

Manual Upgrade Procedure
------------------------

Start by putting your server in maintenance mode. This prevents new logins, 
locks the sessions of logged-in users, and displays a status screen so users 
know what is happening. There are two ways to do this, and the preferred method 
is to use the ``occ`` command. This example is for Ubuntu Linux::

 $ sudo -u www-data php occ maintenance:mode --on

Please see :doc:`../configuration/occ_command` to learn more about ``occ``. 

The other way is by entering your ``config.php`` file and changing 
``'maintenance' => false,`` to ``'maintenance' => true,``.  When you're finished 
upgrading, remember to change ``true`` to ``false``.

Then:

1. Ensure that you are running the latest point release of your current major 
   ownCloud version.
2. Deactivate all third party applications (not core apps), and review them for 
   compatibility with your new ownCloud version.
3. Back up your existing ownCloud Server database, data directory, and 
   ``config.php`` file. (See :doc:`backup`.)
4. Download the latest ownCloud Server version into an empty directory outside 
   of your current installation. For example, if your current ownCloud is 
   installed in ``/var/www/owncloud/`` you could create a new directory called
   ``/var/www/owncloud2/``

On Linux operating systems, change to your new directory and download the 
current ownCloud tarball with ``wget``:

  ``wget http://download.owncloud.org/community/owncloud-latest.tar.bz2``

For Windows operating systems see the installation instruction in 
:doc:`../installation/windows_installation`.

5. Stop your web server.

Depending on your environment, you will be running either an Apache server or 
a Windows IIS server. To stop an Apache server, refer to the following table for 
specific commands to use in different Linux operating systems:

  +-----------------------+-----------------------------------------+
  | Operating System      | Command (as root)                       |
  +=======================+=========================================+
  | CentOS/ Red Hat       |  ``apachectl stop``                     |         
  +-----------------------+-----------------------------------------+
  | Debian                |                                         |
  | or                    | ``/etc/init.d/apache2 stop``            |
  | Ubuntu                |                                         |
  +-----------------------+-----------------------------------------+
  | SUSE Enterprise       |                                         |
  | Linux 11              | ``/usr/sbin/rcapache2 stop``            |       
  |                       |                                         |
  | openSUSE 12.3 and up  | ``systemctl stop apache2``              |
  +-----------------------+-----------------------------------------+

To stop the Windows IIS web server, you can use either the user interface (UI) 
or command line method as follows:

  
 +----------------------+---------------------------------------------------+
 | Method               | Procedure                                         |   
 |                      |                                                   |
 +======================+===================================================+
 | User Interface (UI)  | 1. Open IIS Manager and navigate to the           |
 |                      |    web server node in the tree.                   |  
 |                      |                                                   |
 |                      | 2. In the **Actions** pane, click **Stop**.       |  
 +----------------------+---------------------------------------------------+
 | Command Line         | 1. Open a command line window as                  |
 |                      |    administrator.                                 |
 |                      |                                                   |
 |                      | 2. At the command prompt, type **net stop WAS**   |
 |                      |    and press **ENTER**.                           |
 |                      |                                                   |
 |                      | 3. (Optional) To stop W3SVC, type **Y** and       |
 |                      |    then press **ENTER**.                          |
 +----------------------+---------------------------------------------------+

6. Rename or move your current ownCloud directory (named ``owncloud/`` if 
   installed using defaults) to another location.

7. Unpack your new tarball:

    ``tar xjf owncloud-latest.tar.bz2``
    
   In Microsoft Windows environments, you can unpack the release tarball using 
   WinZip or a similar tool (for example, Peazip). Always unpack server code 
   into an empty directory. Unpacking the server code into an existing, 
   populated directory is not supported and will cause all kinds of errors. 
    
8. This creates a new ``owncloud/`` directory populated with your new server 
   files. Copy this directory and its contents to the original location of your 
   old server, for example ``/var/www/``, so that once again you have 
   ``/var/www/owncloud`` .

9. Copy and paste the ``config.php`` file from your old version of 
   ownCloud to your new ownCloud version.

10. If you keep your ``data/`` directory in your ``owncloud/`` directory, copy 
    it from your old version of ownCloud to the ``owncloud/`` directory of your 
    new ownCloud version. If you keep it outside of ``owncloud/`` then you 
    don't have to do anything with it.

.. note:: We recommend storing your ``data/`` directory in a location other 
   than your ``owncloud/`` directory.

11. Restart your web server.

Depending on your environment, you will be running either an Apache server or a 
Windows IIS server. In addition, when running your server in a Linux 
environment, the necessary commands for stopping the Apache server might differ 
from one Linux operating system to another.

To start an Apache server, refer to the following table for specific commands 
to use in different Linux operating systems:

  +-----------------------+-----------------------------------------+
  | Operating System      | Command (as root)                       |
  +=======================+=========================================+
  | CentOS/ Red Hat       |  ``apachectl start``                    |         
  +-----------------------+-----------------------------------------+
  | Debian                |                                         |
  | or                    | ``/etc/init.d/apache2 start``           |
  | Ubuntu                |                                         |
  +-----------------------+-----------------------------------------+
  | SUSE Enterprise       |                                         |
  | Linux 11              | ``/usr/sbin/rcapache2 start``           |       
  |                       |                                         |
  | openSUSE 12.3 and up  | ``systemctl start apache2``             |
  +-----------------------+-----------------------------------------+
  
To start the Windows IIS web server, you can use either the user interface 
(UI) or command line method as follows:
  
 +----------------------+---------------------------------------------------+
 | Method               | Procedure                                         |   
 |                      |                                                   |
 +======================+===================================================+
 | User Interface (UI)  | 1. Open IIS Manager and navigate to the           |
 |                      |    web server node in the tree.                   |
 |                      |                                                   |
 |                      | 2. In the **Actions** pane, click **Stop**.       |   
 +----------------------+---------------------------------------------------+
 | Command Line         | 1. Open a command line window as                  |
 |                      |    administrator.                                 | 
 |                      |                                                   |
 |                      | 2. At the command prompt, type **net stop WAS**   |
 |                      |    and press **ENTER**.                           |
 |                      |                                                   |
 |                      | 3. (Optional) To stop W3SVC, type **Y** and       |
 |                      |    then press **ENTER**.                          |
 +----------------------+---------------------------------------------------+

12. Now you should be able to open a web browser to your ownCloud server and 
    log in as usual. You have a couple more steps to go: You should see a 
    **Start Update** screen, just like in the previous section. Review the 
    prequisites, and if you have followed all the steps click the **Start 
    Update** button.    
    
    If you  are running a large installation with a lot of files and users, you 
    should launch the update from the command line using ``occ`` to avoid 
    timeouts, like this example on Ubuntu Linux::
    
     $ sudo -u www-data php occ upgrade
     
   .. note:: The ``occ`` command does not download ownCloud updates. You must first 
      download and install the updated code, and then ``occ`` performs the final upgrade steps.
     
    Please see :doc:`../configuration/occ_command` to learn more about ``occ``.
    
13. The upgrade operation takes a few minutes, depending on the size of your 
    installation. When it is finished you will see a success message, or an 
    error message that will tell where it went wrong.   

Assuming your upgrade succeeded, take a look at the bottom of the Admin page to 
verify the version number. Check your other settings to make sure they're 
correct. Go to the Apps page and review the core apps to make sure the right 
ones are enabled.

Now you can enable your third-party apps.

Setting Strong Permissions
--------------------------

For hardened security we highly recommend setting the permissions on your 
ownCloud directory as strictly as possible. After upgrading, verify that your 
ownCloud directory permissions are set according to the Setting Strong Directory 
Permissions section of :doc:`../installation/installation_wizard`.
