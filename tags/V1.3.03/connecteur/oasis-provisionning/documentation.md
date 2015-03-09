Attention, avant de procéder à la mise à jour, il convient de supprimer le connecteur global OASIS précédent.
(sinon, il y aura un bug dans le protocole de supression d'une instance)


Il faut donc :
* créer un connecteur global OASIS provisionning en spécifiant 
- les secrets pour les URL de création et de supression ;
- l'url qui sera utilisé pour la création du connecteur d'authentification OpenID ( https://accounts.ozwillo-preprod.eu/ dans ce cas) ;
- le rôle qui sera attribué à l'utilisateur qui crée l'instance (admin par exemple), ce rôle est attribué uniquement sur la collectivité crée.

* Associer le connecteur global OASIS provisionning

A ce moment-là, Pastell sait répondre au requête de création d'instance. Il enregistre celle-ci directement comme fichier attaché au connecteur ( Instances en attente ).

Pour le moment, il n'est possible que de traiter manuellement les demandes de création (on peut l'automatiser si nécessaire) : il suffit de cliquer sur "Traitement de la première instance en attente".
Cela crée la collectivité, l'utilisateur et un connecteur d'authentification OpenID attaché à la collectivité. Cela envoie également le message d'acquittement à OASIS.

L'utilisateur peux alors se connecter sur Pastell via le portail OASIS.

Pastell répond également au demande de supression des instances en désactivant la collectivité associé à l'instance.


L'admin de collectivité peut (une fois connecté via OpenID) vérifié et synchroniser les utilisateurs dans le connecteur OpenID.
