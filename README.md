roundcube duo_auth
==================

This is a Roundcube webmail plugin that enables Duo Security Two Factor Authentication.

It creates an additional page after successful username/password authentication that requires a 2nd Factor of Authentication using Duo Security (push, sms, call, code).

INSTALLATION
============
Same as any other Roundcube plugin. Clone the repository in the plugins directory of your Roundcube installation or download the zip from GitHub and unzip it in that directory.

**PLEASE NOTE -** If you have downloaded the plugin via the "Download Zip" button in GitHub, rename the extracted folder to "duo_auth"

CONFIGURATION
=============
Enter all keys necessary for integration with Duo in the config.inc.php file.
Assuming a Duo integration has already been created in Duo's Admin Panel, you will be able to find all the information requested in the config.inc.php there.

