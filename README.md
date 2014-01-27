manual_wp_edd_plugin
==================
Easy Digital Downloads - Manual Purchase Gateway (100% compabtible with Fundify Astoundify)

Description 
--------------
A manual (transfers) gateway for Easy Digital Downloads.

Configuration
--------------
1) Set gateway default values on *Campaings - Settings - Gateways*.

- Fields information:
	* Platform IBAN/BIC => IBAN/BIC related to platform where purchases will be asked to transfer. (Only used if *One or multiple IBAN* is set to *ONE*)
	* One or multiple IBAN => Allowed values are *ONE* and *MULTIPLE* meaning whether each campaign has its own IBAN/BIC or if every transfer goes to your platform IBAN/BIC.
	* Transfer info => Message that will be displayed in checkout section previous to purchase. Here you can inform to your user that a email with instructions will be send.
	* FROM / SUBJECT / BODY => This is instruction's email.

2) Set IBAN/BIC on each campaign (only if *One or multiple IBAN* is set to *MULTIPLE*). This is single edition of every campaign post on side bar box named *Campaign's purchase bank account*



Working flow
--------------
Once a *manual* purchase has been done, an email will be send to purchaser with *IBAN / BIC* information. The purchase will be marked as *pending* until admin confirms complete status through *Campaigns - Payment History*. 

