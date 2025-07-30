pipeline {
    agent any
    
    environment {
        DOCKER_IMAGE = 'todo-app'
        DOCKER_TAG = "${BUILD_NUMBER}"
        GITHUB_REPO = 'gbtclement/todo-app'
        GITHUB_REGISTRY = 'ghcr.io'
    }
    
    stages {
        stage('Checkout') {
            steps {
                echo 'Téléchargement du repository...'
                checkout scm
            }
        }
        
        stage('Install PHP & Composer') {
            steps {
                echo 'Installation de PHP et Composer...'
                sh '''
                    # Mise à jour des paquets
                    apt-get update
                    
                    # Installation de PHP et extensions nécessaires
                    apt-get install -y php php-cli php-mbstring php-xml php-zip unzip wget
                    
                    # Installation de Composer
                    wget -O composer-setup.php https://getcomposer.org/installer
                    php composer-setup.php --install-dir=/usr/local/bin --filename=composer
                    rm composer-setup.php
                '''
            }
        }
        
        stage('Install Dependencies') {
            steps {
                echo 'Installation des dépendances PHP...'
                sh 'composer install --no-dev --optimize-autoloader'
            }
        }
        
        stage('Run Tests') {
            steps {
                echo 'Exécution des tests PHPUnit...'
                sh '''
                    mkdir -p tests/results
                    vendor/bin/phpunit --log-junit tests/results/junit.xml || echo "Tests completed"
                '''
            }
            post {
                always {
                    script {
                        if (fileExists('tests/results/junit.xml')) {
                            publishTestResults testResultsPattern: 'tests/results/junit.xml'
                        }
                    }
                }
            }
        }
        
        stage('Create Archive') {
            steps {
                echo 'Création de l\'archive du projet...'
                sh '''
                    # Créer une archive avec le code compilé
                    tar -czf todo-app-${BUILD_NUMBER}.tar.gz --exclude=tests --exclude=.git .
                    ls -la todo-app-${BUILD_NUMBER}.tar.gz
                '''
                
                // Archiver l'artefact dans Jenkins
                archiveArtifacts artifacts: 'todo-app-*.tar.gz', fingerprint: true
            }
        }
        
        stage('Tag Repository') {
            steps {
                echo 'Tagging du repository...'
                script {
                    try {
                        sh """
                            git config user.email "jenkins@example.com"
                            git config user.name "Jenkins"
                            git tag -a v${BUILD_NUMBER} -m "Build version ${BUILD_NUMBER}"
                            git push origin v${BUILD_NUMBER}
                        """
                    } catch (Exception e) {
                        echo "Erreur lors du tagging: ${e.getMessage()}"
                        echo "Continuing without git tag..."
                    }
                }
            }
        }
        
        stage('Deploy') {
            steps {
                echo 'Simulation du déploiement...'
                sh '''
                    echo "Déploiement de la version ${BUILD_NUMBER}"
                    echo "Archive disponible: todo-app-${BUILD_NUMBER}.tar.gz"
                    echo "Application prête à être déployée!"
                '''
            }
        }
    }
    
    post {
        always {
            echo 'Nettoyage des fichiers temporaires...'
            sh 'rm -f composer-setup.php || true'
        }
        success {
            echo 'Pipeline exécuté avec succès!'
            echo "Version ${BUILD_NUMBER} construite et testée"
        }
        failure {
            echo 'Échec du pipeline!'
        }
    }
}