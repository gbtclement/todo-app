# todo-app

1 - Prérequis
Installer Docker Desktop.
Récupérer le dossier Jenkins sur le drive contenant le docker-compose.yml et le package .tar.gz.

2 - Lancement de Jenkins avec Docker Compose
Ouvrir PowerShell et se placer dans le dossier Jenkins.
Lancer la commande : docker-compose up -d
Vérifier que le conteneur Jenkins fonctionne : docker-compose ps

3 - Accès à Jenkins
Interface web : http://localhost:8080
Les données Jenkins (jobs, configuration, plugins) sont stockées dans le volume persistant jenkins_home.

4 - Cloner le repo git
Récupérer le dépôt Git du projet Todo et le cloner localement.
Effectuer une modification dans le code et réaliser un commit pour déclencher la pipeline CI/CD.

5 - Vérification
Sur l’interface Jenkins, vérifier les résultats de la pipeline.
Si tous les tests passent, le déploiement du projet a été effectué avec succès.
