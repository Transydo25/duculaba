# CONTACT EMAILS

This module provides a more versatile interface and functionality for managing
emails that get sent from Drupal Core Contact submissions. It allows users with
the new permission 'manage contact form emails' to add as many emails as
desired, each with a different recipient or set of recipients (including the
submitter of the form), each a different subject or message.

## EXAMPLE USE CASE

The website is for a larger organisation and you want one email saying thank
you to the submitter of the form, one to the office administrator with some
basic info, and one to the marketing manager with specific details about the
form, this module would handle that.

## INSTALLATION

To install this module, place it in your modules folder and enable it on the
modules page.

## CONFIGURATION

There is nothing to configure. Enable the module and users with the required
permission can find the new 'Manage Emails' section

## HOW TO USE

Users with the 'manage contact form emails' will find a 'Manage emails'
operation in the list of operations on the page containing the list of all
contact forms. You can find that at `/admin/structure/contact`.

If the user does not have access that page, you can additionally add a link
to `/admin/structure/contact/manage/email-settings` anywhere in your menu.

## FEEDBACK

Please add issues with feature requests as well as feedback on the existing
functionality.

## SUPPORTING ORGANIZATION

Initial development of this module was sponsored by Fat Beehive until mid-2018.

## MAINTAINERS

- Scott Euser (scott_euser) - <https://www.drupal.org/u/scott_euser>
