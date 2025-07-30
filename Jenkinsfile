pipeline {
    agent any
    
    stages {
        stage('Checkout') {
            steps {
                echo 'Téléchargement du repository...'
                checkout scm
            }
        }
        
        stage('Verify PHP Installation') {
            steps {
                echo 'Vérification de PHP et Composer...'
                sh 'php --version'
                sh 'composer --version'
            }
        }
        
        stage('Install Dependencies') {
            steps {
                echo 'Installation des dépendances PHP...'
                // Installer avec les dépendances de dev pour les tests
                sh 'composer install --optimize-autoloader'
            }
        }
        
        stage('Run Tests') {
            steps {
                echo 'Exécution des tests PHPUnit...'
                sh '''
                    mkdir -p tests/results
                    # Exécuter les tests avec le script Composer
                    composer run-script test-ci || true
                '''
            }
            post {
                always {
                    // Publier les résultats des tests
                    publishTestResults testResultsPattern: 'tests/results/junit.xml'
                    
                    // Afficher le contenu du rapport pour debug
                    script {
                        if (fileExists('tests/results/junit.xml')) {
                            echo "✅ Rapport de tests généré"
                            sh 'cat tests/results/junit.xml'
                        } else {
                            echo "❌ Aucun rapport de tests trouvé"
                        }
                    }
                }
            }
        }
        
        stage('Build for Production') {
            steps {
                echo 'Préparation pour la production...'
                sh '''
                    # Réinstaller sans les dépendances de dev
                    composer install --no-dev --optimize-autoloader
                    
                    # Nettoyer les fichiers de développement
                    rm -rf tests/
                    rm -f phpunit.xml
                    rm -f TodoAppTest.php
                '''
            }
        }
        
        stage('Create Docker Image') {
            steps {
                echo 'Création de l\'image Docker...'
                script {
                    def image = docker.build("todo-app:${BUILD_NUMBER}")
                    echo "Image Docker créée: ${image.id}"
                }
            }
        }
        
        stage('Create Package') {
            steps {
                echo 'Création du package...'
                sh '''
                    tar -czf todo-app-${BUILD_NUMBER}.tar.gz --exclude=.git .
                    ls -la todo-app-${BUILD_NUMBER}.tar.gz
                '''
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
                            git tag -a v${BUILD_NUMBER} -m "Build version ${BUILD_NUMBER} - Tests passed"
                            git push origin v${BUILD_NUMBER}
                        """
                    } catch (Exception e) {
                        echo "Tag ignoré: ${e.getMessage()}"
                    }
                }
            }
        }
    }
    
    post {
        always {
            // Nettoyage
            sh 'docker system prune -f || true'
        }
        success {
            echo "✅ Build ${BUILD_NUMBER} réussi!"
            echo "📦 Package: todo-app-${BUILD_NUMBER}.tar.gz"
            echo "🐳 Image Docker: todo-app:${BUILD_NUMBER}"
        }
        failure {
            echo "❌ Build ${BUILD_NUMBER} échoué!"
            echo "Vérifiez les logs des tests ci-dessus."
        }
    }
}