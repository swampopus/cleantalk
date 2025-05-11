Anti-Spam by CleanTalk
======================

Provides complex, powerful, and invisible spam protection. Blocks spam comments, bots, 
and protects all forms on your website – no CAPTCHAs, puzzles, or delays.

Makes use of the CleanTalk service, found at https://cleantalk.org


Requirements
------------

* While this module comes with the CleanTalk PHP-antispam classes,
  should you need them, they are available at https://github.com/CleanTalk/php-antispam
* You will need to sign up with https://cleantalk.org to get your access key.


Installation
------------

* Install this module using the official Backdrop CMS instructions at
  https://backdropcms.org/guide/modules
* Once the module is enabled, navigate to its config page
  and add your CleanTalk access key.


Help and Functionality
----------------------

Some notes about this module:

Key features
* No needs in CAPTCHA, etc.
* Protection from spam bots and manual spam comments.
* Automoderation - automatic publication of relevant comments.
* Contact forms protection.

What CleanTalk is
* CleanTalk is a SaaS spam protection service for Web-sites.
* CleanTalk uses protection methods which are invisible for site visitors.
* Using CleanTalk eliminates needs in CAPTCHA, questions and answers, and other
   methods of protection, complicating the exchange of information on the site.

How it works
* Messages or registration requests are sent to the CleanTalk cloud, data is
  tested with several methods on the cloud, then the site receives a response
  decision to approve or deny the message/registration.

CleanTalk Service Testing  (once installed)
* Try to register account with "stop_email@example.com" as email address.
* Set "Enable comments test via stop list" flag at https://cleantalk.org/my/
* Then try to put comment with "stop_word" in its body.


Current Maintainers
-------------------

- [Richard Peacock](https://github.com/swampopus) - Originally ported to Backdrop CMS.
- Seeking additional maintainers.

Credits
-------

This module is based on the Drupal module cleantalk-7.x-5.0

Project page: https://www.drupal.org/project/cleantalk

Drupal Maintainers:
- [Alexey Znaev](https://www.drupal.org/u/znaeff)
- [alexandergull](https://www.drupal.org/u/alexandergull)
- [anton1211](https://www.drupal.org/u/anton1211)
- [glomberg](https://www.drupal.org/u/glomberg)
- [sergefcleantalk](https://www.drupal.org/u/sergefcleantalk)
- [CleanTalk Support](https://www.drupal.org/u/cleantalk-support)

Contact CleanTalk at https://cleantalk.org/contacts

License
-------

This project is GPL v2 software. See the LICENSE.txt file in this directory for
complete text.