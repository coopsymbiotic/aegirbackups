# Aegir Backups

Allows CiviCRM administrators to create and download backups from Aegir.

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
chmod g+r /var/aegir/backups
```

This makes it possible to access files if the filename is known (filenames are somewhat
difficult to guess, although not random).

It might be best not to do this on servers where users can run arbitrary PHP code.

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
