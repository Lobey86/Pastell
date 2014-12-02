Les flux fournisseurs
===

Les **flux fournisseurs** permettent l'échange de documents entre des collectivités territorials et leurs fournisseurs
(factures, bons de commande, ...).

Afin de mettre en place la communication entre un fournisseur et une collectivité il est nécessaire que le fournisseur 
s'inscrive sur Pastell. Cette inscription se fait suite à l'invitation d'une collectivité initialement inscrite sur Pastell.

Avant de commencer
---
Il faut s'assurer qu'un rôle fournisseur existe sur la plateforme et dispose des droits suivants :

* utilisateur:lecture
* journal:lecture
* fournisseur-inscription:*

En fonction des flux que l'on souhaite utiliser, il peut-être nécessaire d'ajouter les droits suivants: 
* fournisseur-facture:*
* fournisseur-message: *

Il est également nécessaire d'avoir configurer un connecteur global de type "validation-fournisseur" et d'y avoir indiqué
une entité Pastell qui sera responsable de la validation finale des fournisseurs.


Invitation du fournisseur
---

Le flux *Invitation fournisseur* nécessite un connecteur de type *mail-fournisseur-invitation* 
qui permet de spécifier les propriétés du mail d'invitation.


Une fois le flux configuré, un utilisateur de Pastell, ayant le droit d'édition sur une collectivité pour le flux "Invitation fournisseur" 
peut créer un document de ce type en spécifiant la raison sociale du fournisseur ainsi qu'une adresse électronique. 

L'utilisateur peut renvoyer l'invitation autant de fois qu'il le souhaite (perte du mail par le fournisseur par exemple).
Une fois le fournisseur inscrit, il n'est plus possible de lui ré-envoyer une invitation.

Il est possible d'envoyer un grand nombre d'invitation via un fichier au format CSV, cette possibilité est à configurer dans le 
connecteur mail-fournisseur-invitation et demandera donc des droits d'administration.

Le mail d'invitation contient un lien permettant au fournisseur d'effectuer une préinscription sur Pastell en lui demandant un minimum 
d'information. Une fois le formulaire validé, une entité de type "fournisseur" est créée, ainsi qu'un compte utilisateur rattaché 
à cette entité. L'utilisateur prend automatiquement le rôle *fournisseur*. Il est ainsi nécessaire de bien valider que ce rôle existe sur Pastell
et dispose des bons droits.

Adhésion du fournisseur
---

Le fournisseur doit d'abord soumettre ces informations d'adhésions à une ou plusieurs collectivités avec laquelle il souhaite communiquer.

Lors de son inscription, un document de type "Adhésion fournisseur" à été créé, le fournisseur doit le compléter et l'envoyer à la ou 
aux collectivités qui l'ont précedemment invité.

Une fois que la collectivité reçoit le document, elle peut soit :
* accepter le document
* refuser le document.

En cas de refus, le document est retourné au fournisseur qui pourra alors le modifier et le soumettre à nouveau.

En cas d'acceptation, le document est envoyé à l'entité Pastell défini par le connecteur global *validation-fournisseur*.
Cette entité pourra à son tour accepter ou refuser le document.

Le refus, comme précédemment, renvoi le document au fournisseur.

En cas d'acceptation, il est alors possible pour cette collectivité et ce forunisseur d'utiliser les services de factures, de messagerie, etc.

Ce flux, ainsi que le flux d'invitation prennent en compte les situations suivantes:
* envoi d'une invitation à un fournisseur qui a déjà un compte Pastell : dans ce cas, le fournisseur se logue sur la page issu du mail 
d'invitation et doit juste envoyer son document d'adhésion à la nouvelle collectivité. Si ce document à déjà été modéré, alors
on ne passe pas par l'étape de modération.
* Modification du document d'adhésion. Le fournisseur peut modifier son document d'adhésion, mais dans ce cas, il doit le resoumettre 
à toutes les collectivités et, de ce fait, à la modération.



Flux factures fournisseur
---

Ce flux n'est utilisable que pour les fournisseur qui ont envoyé un document d'adhésion qui a été accepté par une collectivité et par 
l'entité modératrice.

Le flux est initié par le fournisseur qui choisi une collectivité avec laquelle il est en relation, puis il saisie sa facture (le 
document PDF, ainsi que divers meta-données).

La collectivité qui reçoit la facture peut :
* la renvoyer en indiquant un commentaire et éventuellement des pièces jointes
* l'envoyer dans son SI qui a pour effet de l'envoyer sur une GED (à configurer via un connecteur)

Une fois la facture envoyer au SI, il est possible d'en notifier la liquidation au fournisseur qui reçoit alors un email.








