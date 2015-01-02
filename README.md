xmlrpcupcheck
=============

This plugin provides XML-RPC methods for checking the update status of WordPress websites. You 
can query core, plugin, and themes individually, or check all at once. 

It defines four new methods:

- xmlrpcCoreUpdateCheck - checks core, returns true/false
- xmlrpcPluginUpdateCheck - checks plugins, returns true/false
- xmlrpcThemeUpdateCheck - checks themes, returns true/false
- xmlrpcUpdatesCheck - checks all of the above, returns an array of trues/falses.



