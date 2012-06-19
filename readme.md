# JACLI - ˈdʒæk ˈliː

## Abstract
JACLI is a PHP application based on the Joomla! Platform. It can be used to install other PHP applications.

Currently supported applications (in alphabetical order):

* [Dokuwiki](http://dokuwiki.org)
  2012-01-25a and development (git)
* [Drupal](http://drupal.org) (almost ```*```)
  7.14 and development (git)
* [Joomla! CMS](http://joomla.org)
  2.5.4 and development (git)
* [Wordpress](http://wordpress.org) (almost ```*```)
  3.3.2 and development (svn)
* ...

The sources are **not** provided and can be downloaded automatically or copied manually to a given directory.

## Usage
Open a terminal window and type: ```jacli --help``` - that should help ;)

## Requirements
* A Joomla! Platform version min 12.1 (@todo to be released)
	An enviroment variable ```JOOMLA_PLATFORM_PATH``` set up.
* The required sources for the applications to build can be downloaded or copied into the source directory
	```wget``` (optional) to download package files
	```git``` or ```svn``` (optional) to checkout from version control systems

## Supported interfaces:

* CLI - The default interface - should work on all operating systems
* KDE, Gnome - Message boxes

## Frontends:

* Web (WIP)
* Ruby/QT, Python/QT, C++/QT (very WIP)

## Supported Operating Systems
* Developed and tested only on [OpenSuSE Linux](http://opensuse.org)
	So it should probably work on most Unixoide systems

The CLI version should run on any system that is able to run PHP scripts.

* ... **WANTED**: Developers ...

If you find **your** operating system unsupported, please use the facilities here on GitHub to send me a pull request with the corresponding code changes.

## What's in a name
JACLI

 * **J** oomla! **A** pplication **CLI**
 * **J** ack of **A** ll trades (even **CLI**)
 * Jack Lee (The brother of Bruce..-).

Last but not least: Meet [The World Champion Jack Lee](http://www.youtube.com/watch?v=Z4CRwrR_lBE)

have Fun,
<tt>=;)</tt>

<hr />
* ```*``` - means that the application will be copied to the workspace and the database will be set up. Then you have to run the applications install routine.
