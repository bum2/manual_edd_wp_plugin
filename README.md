edd-manual-gateway
==================

Description
--------------
Easy Digital Downloads - Manual Purchase Gateway (designed to join Marketify and Fundify Astoundify), forked from https://github.com/aleph1888/manual_edd_wp_plugin and adapted to marketify for getfaircoin.net


Configuration
--------------
1) Set gateway default values on *Campaings(or Downloads or whatever you've set) -> Settings -> Gateways*.

- Fields information:
	* One or multiple IBAN => Allowed values are *ONE* and *MULTIPLE* meaning whether each campaign has its own IBAN/BIC or if every transfer goes to your platform IBAN/BIC.
	* Platform IBAN/BIC => IBAN/BIC related to platform where purchases will be asked to transfer. (Only used if *One or multiple IBAN* is set to *ONE*)
	* Transfer info => Message that will be displayed in checkout section previous to purchase. Here you can inform to your user that an email with instructions will be send.
	* FROM / SUBJECT / BODY => This is instructions email for the user.
	* Receipt info => Message that will be displayed on purchase, in the screen receipt (normally, same info as the email, but web page format)

2) Set IBAN/BIC on each campaign-post (only if *One or multiple IBAN* is set to *MULTIPLE*). This is single edition of every campaign post on side bar box named *Campaign's purchase bank account*.

- The main fields can be set in the post level:
	* IBAN/BIC
	* Transfer Info
	* User's Email (from, subject and body)
	* Receipt Info

3) Check the Settings -> Emails tab to find the admin notification email settings, general for all gateways (you can use all email_tags).

 
Working flow
--------------
Once a *manual* purchase has been done, an email will be send to purchaser with *IBAN / BIC* information. The purchase will be marked as *pending* until admin confirms complete status through *Campaigns - Payment History*.


Notice
--------------
You can use this plugin for literal manual purchases just adding some writing in the information mail. Once you receive the payment proceed the flow as explained.


Contribute
--------------
new fork (bum2): @FAIR fThesXCU7FfekYNNui2MtELfCNoa9pctJk / @BTC 13f5TfiYgWeqTFxfzwyraA1LUV6RMFjxnq

original dev (hackafou): @BTC 1DNxbBeExzv7JvXgL6Up5BSUvuY4gE8q4A
