manual_wp_edd_plugin
==================

Description
--------------
Easy Digital Downloads - Manual Purchase Gateway (designed to join Fundify Astoundify)

Configuration
--------------
1) Set gateway default values on *Campaings - Settings - Gateways*.

- Fields information:
	* Platform IBAN/BIC => IBAN/BIC related to platform where purchases will be asked to transfer. (Only used if *One or multiple IBAN* is set to *ONE*)
	* One or multiple IBAN => Allowed values are *ONE* and *MULTIPLE* meaning whether each campaign has its own IBAN/BIC or if every transfer goes to your platform IBAN/BIC.
	* Transfer info => Message that will be displayed in checkout section previous to purchase. Here you can inform to your user that an email with instructions will be send.
	* FROM / SUBJECT / BODY => This is instructions email.

2) Set IBAN/BIC on each campaign (only if *One or multiple IBAN* is set to *MULTIPLE*). This is single edition of every campaign post on side bar box named *Campaign's purchase bank account*



Working flow
--------------
Once a *manual* purchase has been done, an email will be send to purchaser with *IBAN / BIC* information. The purchase will be marked as *pending* until admin confirms complete status through *Campaigns - Payment History*. 

Notice
--------------
You can use this plugin for literal manual purchases just adding some writing in the information mail. Once you receive the payment proceed the flow as explained.

Contribute
--------------
@BTC 1DNxbBeExzv7JvXgL6Up5BSUvuY4gE8q4A

