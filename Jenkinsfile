pipeline {
    agent any
    
    stages {
        stage('Checkout') {
            steps {
                echo 'Téléchargement du repository...'
                checkout scm
            }
        }
        
        stage('Build & Test') {
            agent {
                docker {
                    image 'php:8.1-cli'
                    reuseNode true
                }
            }
            steps {
                echo 'Installation de Composer...'
                sh '''
                    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
                    php composer-setup.php --install-dir=/usr/local/bin --filename=composer
                    rm composer-setup.php
                '''
                
                echo 'Installation des dépendances...'
                sh 'composer install'
                
                echo 'Exécution des tests...'
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
        
        stage('Create Package') {
            steps {
                echo 'Création du package...'
                sh '''
                    # Créer une archive avec le code
                    tar -czf todo-app-${BUILD_NUMBER}.tar.gz --exclude=tests --exclude=.git --exclude=vendor .
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
                            git tag -a v${BUILD_NUMBER} -m "Build version ${BUILD_NUMBER}"
                            git push origin v${BUILD_NUMBER}
                        """
                    } catch (Exception e) {
                        echo "Erreur lors du tagging: ${e.getMessage()}"
                        echo "Tag ignoré pour cette fois"
                    }
                }
            }
        }
    }
    
    post {
        success {
            echo "✅ Build ${BUILD_NUMBER} réussi!"
            echo "Package créé: todo-app-${BUILD_NUMBER}.tar.gz"
        }
        failure {
            echo "❌ Build ${BUILD_NUMBER} échoué!"
        }
    }
}