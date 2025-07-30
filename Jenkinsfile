pipeline {
    agent any
    
    stages {
        stage('Checkout') {
            steps {
                echo 'Téléchargement du repository...'
                checkout scm
            }
        }
        
        stage('Verify Environment') {
            steps {
                echo 'Vérification de l\'environnement...'
                sh 'php --version'
                sh 'composer --version'
            }
        }
        
        stage('Install Dependencies') {
            steps {
                echo 'Installation des dépendances...'
                sh '''
                    rm -f composer.lock
                    rm -rf vendor/
                    composer install
                '''
            }
        }
        
        stage('Run Tests') {
            steps {
                echo 'Exécution des tests...'
                sh '''
                    mkdir -p tests/results
                    vendor/bin/phpunit --log-junit tests/results/junit.xml --testdox
                '''
            }
            post {
                always {
                    script {
                        if (fileExists('tests/results/junit.xml')) {
                            // Utiliser junit au lieu de publishTestResults
                            junit 'tests/results/junit.xml'
                            echo "✅ Tests publiés dans Jenkins"
                        } else {
                            echo "❌ Fichier de résultats introuvable"
                            sh 'ls -la tests/ || echo "Dossier tests absent"'
                        }
                    }
                }
            }
        }
        
        stage('Create Package') {
            steps {
                echo 'Création du package...'
                sh '''
                    # Réinstaller sans les dépendances de dev
                    composer install --no-dev --optimize-autoloader
                    
                    # Créer le package
                    tar -czf todo-app-${BUILD_NUMBER}.tar.gz \
                        --exclude=tests \
                        --exclude=.git \
                        --exclude=composer.lock \
                        .
                '''
                archiveArtifacts artifacts: 'todo-app-*.tar.gz'
            }
        }
    }
    
    post {
        success {
            echo "✅ Build ${BUILD_NUMBER} terminé avec succès!"
        }
        failure {
            echo "❌ Build ${BUILD_NUMBER} a échoué!"
        }
        always {
            echo "🧹 Nettoyage..."
        }
    }
}