pipeline {
    agent {
        docker {
            image 'php:8.2-cli'
            args '-u root:root'
        }
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
        
        stage('Setup') {
            steps {
                echo '🔧 Installation des outils...'
                sh '''
                    apt-get update -qq && apt-get install -y -qq git curl unzip
                    curl -sS https://getcomposer.org/installer | php
                    mv composer.phar /usr/local/bin/composer
                    chmod +x /usr/local/bin/composer
                '''
            }
        }
        
        stage('Verify Environment') {
            steps {
                echo '🔍 Vérification de l\'environnement...'
                sh '''
                    php --version
                    composer --version
                    git --version
                '''
            }
        }
        
        stage('Install Dependencies') {
            steps {
                echo '📦 Installation des dépendances...'
                sh 'composer install --no-interaction --prefer-dist'
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
        
        stage('Push to Main') {
            when {
                allOf {
                    branch 'pending'
                    expression {
                        currentBuild.result == null || currentBuild.result == 'SUCCESS'
                    }
                }
            }
            steps {
                echo '🚀 Tests OK ! Push du code vers main...'
                sh '''
                    git config user.email "clementgaubert44@gmail.com"
                    git config user.name "Jenkins CI"
                    
                    # Récupérer le SHA actuel
                    COMMIT_SHA=$(git rev-parse HEAD)
                    echo "Commit à pusher: $COMMIT_SHA"
                    
                    # Push vers main
                    git push https://${GIT_CREDENTIALS_USR}:${GIT_CREDENTIALS_PSW}@github.com/gbtclement/todo-app.git HEAD:refs/heads/main
                    
                    echo "✅ Code validé et poussé sur main avec succès !"
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
