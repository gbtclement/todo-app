# 📝 To-Do App – Intégration Continue avec Jenkins

## 🚀 1. Prérequis
- Installer **Docker Desktop**.
- Ouvrir **PowerShell** et se placer dans le répertoire contenant la configuration **Jenkins** du projet.

---

## ⚙️ 2. Lancement de Jenkins avec Docker Compose
Exécuter les commandes suivantes dans PowerShell :
docker-compose up -d

Vérifier que le conteneur Jenkins est bien en cours d’exécution :
docker-compose ps

---

## 🌐 3. Accès à l’interface Jenkins
Ouvrir un navigateur et accéder à l’adresse suivante :
http://localhost:8080

---

## 🔐 4. Connexion à Jenkins
Se connecter avec les identifiants administrateur créés lors de la première configuration.

---

## 🧩 5. Accès au job
Depuis la page d’accueil de Jenkins :
- Sélectionner le job “TestVerif” pour afficher les builds disponibles.

---

## 🧪 6. Résultats des tests
- Cliquer sur le build souhaité.
- Ouvrir l’onglet “Résultats des tests” pour consulter les rapports générés par PHPUnit.

---

## 🔎 7. Vérification du pipeline
- Dans le même build, accéder à “Pipeline Overview”.
- Vérifier le bon enchaînement des étapes et l’état de chaque stage (succès ou échec).

---

## 🌍 8. Déploiement en production
- Se connecter au serveur distant via SFTP/SSH.
- Se rendre dans le dossier du projet To-Do App.
- Mettre à jour le code avec :
git pull
- Vérifier que la dernière version de l’application est bien déployée.

---

✅ Pipeline complète :
1. Récupération du code depuis pending.
2. Installation des dépendances.
3. Exécution des tests PHPUnit.
4. Fusion automatique vers main si tous les tests réussissent.

---

💡 Astuce :
En cas d’échec du pipeline, le code reste sur la branche pending et n’est pas fusionné — il suffit de corriger les erreurs, puis de relancer le build.
    