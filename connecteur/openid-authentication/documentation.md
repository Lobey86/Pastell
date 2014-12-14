
J'ai ajouté comme "nom" Pastell le "user_name" OASIS 

Par contre, le "login" Pastell doit être associé Ã  l'"ID" OASIS. Ainsi, on a : 	447e1478-461b-4802-b3a5-81fb7ae912c2   comme exemple de login.


Mise en place:
1) Configurer le connecteur 
Les trois données sont fournis par OASIS

2) Tester la connexion avec un utilisateur "admin" (au sens OASIS)(i.e qui peut récupérer la liste des utilisateurs)

3) cela doit échouer car l'utilisateur n'existe pas : il faut copier l'ID donné par le message d'erreur et créer l'utilisateur à
  la main avec comme login, l'ID OASIS. Il faut aussi donner les droits admin sur l'entité racine à  cet utilisateur

4) Recommencer le test de connexion : cette fois cela doit fonctionner et on obtient une connexion admin

5) Demander la liste des comptes : cette fonction affiche les comptes à créer sur Pastell

6) Synchroniser les comptes : cette fonction créer les comptes qui n'existe pas dans Pastell

7) Une fois tous cela fait, on peut alors associer le connecteur OpenID au niveau global (Famille de connecteur Authentification) : Lorsque l'on arrive sur la page de connexion, on est automatiquement redirigé vers OpenID.

