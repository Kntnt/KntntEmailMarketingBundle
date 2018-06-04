# Kntnt's Email Marketing Bundle for Mautic

Plugin that provides integration to the newsletter service [EditNews](https://www.multinet.com/en/editnews/) for the open source marketing automation system [Mautic](https://www.mautic.org/), 

## Description

Mautic is an open source marketing automation system, and EditNews is an email marketing software as a service. This plugin allows Mautic to push contacts to an address list of EditNews.

## Requirements

This plugin have some requirements…

1. Working Mautic 2.13.1 or later installation.
2. Mautic must run on PHP7.
3. You must have [PHP's SOAP extension](http://php.net/manual/en/book.soap.php) installed. To install it on Ubuntu and derivatives (e.g. Kubuntu) run `sudo apt install php7.0-soap` in the terminal. On RedHat and derivatives (e.g. Fedora and Centos) run `yum install php-soap`. Restart Apache, PHP-FPM, PHP FastCGI or whatever you use to manage your PHP processes.

## Installation

Install this plugin by following Mautic's standard procedure:

1. [Download](https://github.com/TBarregren/KntntEmailMarketingBundle/archive/master.zip) or [clone](https://github.com/TBarregren/KntntEmailMarketingBundle.git) this bundle into the`plugins` folder of your Mautic installation.
2. Delete the production cache of your Mautic installation. You can do that either by running `app/console cache:clear --env=prod` from the root of your Mautic installation, or by manually delete `app/cache/prod`.
3. Log in to Mautic. Click the gear icon in the upper right corner. Choose Click *Plugins* from the menu appearing.
4. Click on the button *Install/Upgrade Plugins* in the upper right corner.

You should now see EditNews among the other plugins. Go ahead and configure it.

## Configuration

Before configuring the plugin, make sure you have following information at hand:

1. Your EditNews API key. If you don't have one,  contact [EditNews support](https://support.editnews.com/support/tickets/new) and ask for an API key.
2. The EditNews name of the address list onto which you want the plugin to push contacts.
3. The EditNews name of the welcome letter (i.e. verification of subscription email) to be sent for double opt-in.
4. The EditNews name of the sender of the welcome letter.

With this information yu can follow these step-by-step instructions to configure the plugin:

1. Log in to Mautic. Click the gear icon in the upper right corner. Choose Click *Plugins* from the menu appearing.
2. Click on the EditNews plugin.
3. In the dialogue showing, go to tab *Enabled/Auth*, fill in your API key on the  tab, and click the *Apply* button.
4. Go to the tab *Features* and select your address list.
5. Go to the tab *Contact Mapping* and select a Mautic fields for each Integration field. Only the *Email* field is mandatory.
6. Click on the *Save & Close* button.

## Frequently Asked Questions

### It doesn't work! What to do?

There are two things you always should test first of all: purge cache and check file permissions. Often that solves the issue. If not, take a look in both Mautic's log file (located in `app/logs`) and in your web server's error log, to see if there is any relevant error messages that can help you.

See also [Mautic's documentation on troubleshooting](https://www.mautic.org/docs/en/tips/troubleshooting.html).

### How can I get help?

If you have a questions about the plugin, and cannot find an answer here, start by looking at [issues](https://github.com/Kntnt/KntntEmailMarketingBundle/issues) and [pull requests](https://github.com/Kntnt/KntntEmailMarketingBundle/pulls). If you still cannot find the answer, feel free to ask in the the plugin's [issue tracker](https://github.com/Kntnt/KntntEmailMarketingBundle/issues) at Github.

### How can I report a bug?

If you have found a potential bug, please report it on the plugin's [issue tracker](https://github.com/Kntnt/KntntEmailMarketingBundle/issues) at Github.

### How can I contribute?

Contributions to the code or documentation are much appreciated.

If you are unfamiliar with Git, please date it as a new issue on the plugin's [issue tracker](https://github.com/Kntnt/KntntEmailMarketingBundle/issues) at Github.

If you are familiar with Git, please do a pull request.

## Changelog

### 1.0.1

* Fixed bug: Mautic requires a dummy username together with password.
* Improved documentation.

### 1.0.0

Initial release. Fully functional bundle.
