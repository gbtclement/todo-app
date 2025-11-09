pipeline {
    agent any
    
    triggers {
        pollSCM('* * * * *')
    }
    
    environment {
        GIT_CREDENTIALS = credentials('github-credentials')
    }
    
    stages {
        stage('Checkout') {
            steps {
                echo '📥 Récupération du code depuis pending...'
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
                echo '🧪 Exécution des tests...'
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
        
        stage('Push to Main') {
            when {
                expression {
                    // Vérifier qu'on est sur pending ET que les tests sont OK
                    env.GIT_BRANCH == 'origin/pending' && 
                    (currentBuild.result == null || currentBuild.result == 'SUCCESS')
                }
            }
            steps {
                echo '🚀 Tests OK ! Push vers main...'
                sh '''
                    git config user.email "clementgaubert44@gmail.com"
                    git config user.name "Jenkins CI"
                    
                    echo "📍 Branche actuelle: $(git rev-parse --abbrev-ref HEAD)"
                    echo "📍 Commit: $(git rev-parse HEAD)"
                    
                    git push https://${GIT_CREDENTIALS_USR}:${GIT_CREDENTIALS_PSW}@github.com/gbtclement/todo-app.git HEAD:refs/heads/main
                    
                    echo "✅ Code poussé sur main avec succès !"
                '''
            }
        }
    }
    
    post {
        success {
            echo '✅ Pipeline réussie ! Tests OK et code déployé sur main.'
        }
        failure {
            echo '❌ Pipeline échouée ! Tests KO - le code reste sur pending.'
        }
        always {
            echo '🧹 Nettoyage terminé'
        }
    }
}