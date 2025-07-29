pipeline {
    agent any
    
    environment {
        DOCKER_IMAGE = 'todo-app'
        DOCKER_TAG = "${BUILD_NUMBER}"
        GITHUB_REPO = 'your-username/todo-app'
        GITHUB_REGISTRY = 'ghcr.io'
    }
    
    stages {
        stage('Checkout') {
            steps {
                echo 'Téléchargement du repository...'
                checkout scm
            }
        }

        stage('Install Composer') {
            steps {
                sh '''
                    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
                    php composer-setup.php --install-dir=/usr/local/bin --filename=composer
                    rm composer-setup.php
                '''
            }
        }
        
        stage('Install Dependencies') {
            steps {
                echo 'Installation des dépendances...'
                sh 'composer install'
            }
        }
        
        stage('Run Tests') {
            steps {
                echo 'Exécution des tests...'
                sh 'vendor/bin/phpunit --log-junit tests/results/junit.xml'
            }
            post {
                always {
                    junit 'tests/results/junit.xml'
                }
            }
        }
        
        stage('Build Docker Image') {
            steps {
                echo 'Construction de l\'image Docker...'
                script {
                    dockerImage = docker.build("${DOCKER_IMAGE}:${DOCKER_TAG}")
                }
            }
        }
        
        stage('Tag Repository') {
            steps {
                echo 'Tagging du repository...'
                sh """
                    git tag -a v${BUILD_NUMBER} -m "Build version ${BUILD_NUMBER}"
                    git push origin v${BUILD_NUMBER}
                """
            }
        }
        
        stage('Push to GitHub Packages') {
            steps {
                echo 'Push vers GitHub Packages...'
                script {
                    docker.withRegistry("https://${GITHUB_REGISTRY}", 'github-token') {
                        dockerImage.push("${DOCKER_TAG}")
                        dockerImage.push("latest")
                    }
                }
            }
        }
        
        stage('Deploy') {
            steps {
                echo 'Déploiement de l\'application...'
                sh """
                    docker stop todo-app-container || true
                    docker rm todo-app-container || true
                    docker run -d --name todo-app-container -p 8080:80 ${DOCKER_IMAGE}:${DOCKER_TAG}
                """
            }
        }
    }
    
    post {
        always {
            echo 'Nettoyage...'
            sh 'docker system prune -f'
        }
        success {
            echo 'Pipeline exécuté avec succès!'
        }
        failure {
            echo 'Échec du pipeline!'
        }
    }
}