=== EZPZ SAML SP Single Sign On (SSO) ===
Tags: saml, single sign on, ip based access, ip access control, shibboleth,
athens, sso, okta, onelogin, simplesamlphp
Donate link: http://www.ezpzsp.com
Requires at least: 4.9.8
Tested up to: 5.2.2
Requires PHP: 7.0
Stable tag: 1.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

EZPZ SP makes it easy for you to enable SAML Single Sign On to your
Wordpress website. EZPZ SP Works with Shibboleth, Onelogin, Okta, Athens
and more.

== Description ==
EZPZ SAML SP turns your wordpress website into a fully functioning SAML
service provider. EZPZ SP then allows you to use your existing Identity
Provider (IdP) to authenticate to your wordpress website. We support all
known SAML 2.0 IdPs such as: ADFS, Athens (Eduserv), AzureAD, Bitium,
Centrify, Google Apps, Okta, OneLogin, OpenAM, Oracle, Ping Identity,
Salesforce, Shibboleth, SimpleSAMLphp, WSO2 and many more.

In the premium version of the plugin you are also able to use IP Based
authentication. The premium version also allows you to configure
authentication to multiple IdPs instead of a single IdP.

You have the power to either switch on authentication for the entire
wordpress website to force Single Sign on site wide. Or you can choose to
enable per post / page authentication and require login for particular
posts and pages.

If you require any Single Sign On help or have any questions about the
plugin please email support@ezpzsp.com

= Free Features =
1. SAML Authentication
1. Automatic IDP metadata pull (not cached, pulled on every login)
1. Upload IDP metadata file
1. Protect whole site
1. Restrict access to posts or pages
1. Redirect wordpress login page to IDP
1. Direct IDP login link that can be used anywhere (not currently shown in
front-end)
1. Login via institution link on footer of wordpress login page
1. Auto fill username and optionally email from SAML attributes
1. SAML Single Logout
1. Automatic initial certificate generation
1. Custom SP certificate support
1. Auto creates missing users on login
1. Advanced security options

= Premium Features =
1. Option to disable user auto creation
1. Option to append institution name to user to avoid duplicates across IDPs
1. Options to select SAML Request binding type
1. Customized Role Mapping / Set users default role on creation
1. IDP Metadata caching
1. Custom Attribute Mapping (Any attribute which is stored in user-meta
table)
1. Sub-site specific SSO for Multisite
1. Bulk update to enable/disable post and page restrictions
1. Granular access controls per institution
1. Auto login via IP
1. Ability to restrict posts or pages to specific institutions and/or
specific groups?
1. File upload protection (new file manager so you can protect PDFs, images
etc)


== Installation ==
= FROM YOUR WORDPRESS DASHBOARD =
1. Visit Plugins > Add New.
1. Search for "EZPZ SAML SP". Find and Install "EZPZ SAML SP Single Sign On
(SSO)"
1. Activate the plugin from your Plugins page.

= FROM WORDPRESS.ORG =
1. Download EZPZ SAML SP Single Sign On (SSO) plugin.
1. Unzip and upload the ezpz-sp-saml-sso directory to your
/wp-content/plugins/ directory.
1. Activate EZPZ SAML SP Single Sign On (SSO) from your Plugins page.

== Frequently Asked Questions ==
= How do I set the plugin up =
Please see the installation instructions above or please watch the video
here https://ezpzsp.com/getting-started/
= What does the redirect wordpress login option do? =
This allows you to make it so that you cannot go to
www.yoursite.com/wp-admin and login with your manual admin account. When
you go there with this option enabled you will be sent to the IdP that has
been configured for this plugin.
= I have locked my self out of the admin and cannot login what do i do!? =
Have no fear, if you haven't enabled the redirect wordpress login option
you can just go to www.yoursite.com/wp-admin. If however you have enabled
that option then you can just go to www.yoursite.com/wp-admin?nologin to
bypass the redirect.
= How do I protect the whole website and force login via SAML / SSO? =
You can do that by going to the EZPZ SP Plugin settings and clicking on
login options tab and then press protect whole website. That will force a
login to any of the pages (please note that doesn't protect the wp-content
files so all of your media files you'll need the premium plugin for that).
= How do i get support? =
If you have any questions then you can email support@ezpzsp.com

== Screenshots ==
1. SP Settings Page
2. Login Options Page
3. Protecting Per Page / Post

== Changelog ==
= 1.2 = 
* Allow SAML attributes to use the unfriendly names

= 1.0 =
* First stable release
