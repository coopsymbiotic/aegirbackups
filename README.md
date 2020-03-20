# Aegir Backups

Allows CiviCRM administrators to create and download backups from Aegir.

![Screenshot](/images/screenshot.png)

This assumes that the site is managed by [Aegir](http://www.aegirproject.org/)
and that the site was created using [hosting_restapi](https://github.com/coopsymbiotic/hosting_restapi).

The extension is licensed under [AGPL-3.0](LICENSE.txt).

## Requirements

* PHP v7.2+
* CiviCRM 5.10+

## Installation

Enable as a regular CiviCRM extension.

On the Aegir server, we need to provide limited access so that the webserver can access
downloads:

```
chgrp www-data /var/aegir/backups
chmod g+x /var/aegir/backups
```

This makes it possible to access files if the filename is known (filenames are somewhat
difficult to guess, although not random).

It might be best not to do this on servers where users can run arbitrary PHP code.

# Configuring an existing site

The Aegir server must have the [hosting_restapi](https://github.com/coopsymbiotic/hosting_restapi)
module enabled (and its dependancy, `hosting_saas`).

Then add an entry for the site on the Aegir server:

```
drush @hm sqlc

> INSERT INTO hosting_restapi_order (site, current_status, created, updated, invoice_id, token, ip)
VALUES ('www.example.org', 5, UNIX_TIMESTAMP(NOW()), UNIX_TIMESTAMP(NOW()), 12345, UUID(), '::1');
```

Now fetch the token

```
> SELECT token FROM hosting_restapi_order WHERE site = 'www.example.org';
```

Now set the required variables on the CiviCRM site:

```
drush @www.example.org vset hosting_restapi_hostmaster https://aegir.example.org
drush @www.example.org vset hosting_restapi_token 'the-token'
```

Then from CiviCRM, go to Administer > Aegir Backups and schedule a new backup.
After it few minutes, reload the page and it should be listed for download.
Test to make sure that it works (if not, check the file permissions as per
installation steps above).

# Support

Please post bug reports in the issue tracker of this project:  
https://github.com/coopsymbiotic/aegirbackups/issues

This is a community contributed extension written thanks to the financial
support of organisations using it, as well as the very helpful and collaborative
CiviCRM community.

While we do our best to provide volunteer support for this extension, please
consider financially contributing to support or development of this extension
if you can.

Commercial support via Coop SymbioTIC:  
https://www.symbiotic.coop/en

# License

(C) 2019-2020 Mathieu Lutfy <mathieu@symbiotic.coop>  
(C) 2019-2020 Coop SymbioTIC <info@symbiotic.coop>

Distributed under the terms of the GNU Affero General public license (AGPL).
See LICENSE.txt for details.
