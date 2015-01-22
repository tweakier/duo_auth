roundcube duo_auth
==================

This is a Roundcube webmail plugin that enables Duo Two Factor Authentication.

It creates an additional page after successful username/password authentication that requires a 2nd Factor of Authentication using Duo Security (push, sms, call, code).

INSTALLATION
============
Same as any other Roundcube plugin.

**PLEASE NOTE -** If you have downloaded the plugin via the "Download Zip" button in git, rename the extracted folder to "duo_auth"

CONFIGURATION
=============
Enter all keys necessary for integration with Duo in the config.inc.php file.
Assuming a Duo integration has already been created in Duo's Admin Panel, you will be able to find all the information requested in the config.inc.php there.

CREDITS
=======
Author: Alexios Polychronopoulos - Please send any feedback, feature request or bug report to dev@pushret.co.uk
