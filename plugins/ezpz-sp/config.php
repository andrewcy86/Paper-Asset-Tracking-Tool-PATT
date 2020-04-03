<?php

  // Absolute path to OneLogin library with a trailing slash
  $config['onelogin_path'] = dirname( __FILE__ )."/lib/php-saml-2.13.0/";
  // If 'strict' is True, then the PHP Toolkit will reject unsigned
  // or unencrypted messages if it expects them to be signed or encrypted.
  // Also it will reject the messages if the SAML standard is not strictly
  // followed: Destination, NameId, Conditions ... are validated too.
  $config['onelogin_strict'] = true;
  // Enable debug mode
  $config['onelogin_debug'] = false;
