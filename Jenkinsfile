pipeline {
    agent any
    
    environment {
        GIT_CREDENTIALS = credentials('github-credentials') // Ton ID de credentials Jenkins
    }
    
    stages {
        stage('Checkout') {
            steps {
                echo '📥 Récupération du code source...'
                checkout scm
            }
        }
        
        stage('Verify Environment') {
            steps {
                echo '🔍 Vérification de l\'environnement...'
                sh '''
                    php --version
                    composer --version
                '''
            }
        }
        
        stage('Install Dependencies') {
            steps {
                echo '📦 Installation des dépendances...'
                sh '''
                    rm -f composer.lock
                    rm -rf vendor/
                    composer install
                '''
            }
        }
        
        stage('Run Tests') {
            steps {
                echo '🧪 Exécution des tests unitaires...'
                sh '''
                    mkdir -p tests/results
                    vendor/bin/phpunit --log-junit tests/results/junit.xml --testdox
                '''
            }
            post {
                always {
                    junit 'tests/results/junit.xml'
                }
            }
        }
        
        stage('Push to Git') {
            when {
                expression {
                    currentBuild.result == null || currentBuild.result == 'SUCCESS'
                }
            }
            steps {
                echo '🚀 Push du code vers GitHub...'
                sh '''
                    git config user.email "clementgaubert44@gmail.com"
                    git config user.name "Jenkins CI"
                    
                    # Vérifier s'il y a des changements à commiter
                    if [ -n "$(git status --porcelain)" ]; then
                        git add .
                        git commit -m "✅ Tests passed - Jenkins auto-commit [Build #${BUILD_NUMBER}]"
                        git push https://${GIT_CREDENTIALS_USR}:${GIT_CREDENTIALS_PSW}@github.com/gbtclement/todo-app.git HEAD:main
                        echo "✅ Code poussé avec succès"
                    else
                        echo "ℹ️ Aucun changement à commiter"
                    fi
                '''
            }
        }
    }
    
    post {
        success {
            echo '✅ Build réussi ! Les tests sont passés et le code a été poussé.'
        }
        failure {
            echo '❌ Build échoué ! Les tests ont échoué, le code n\'a PAS été poussé.'
        }
        always {
            echo '🧹 Pipeline terminée'
        }
    }
}
