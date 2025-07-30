pipeline {
    agent any
    
    environment {
        DOCKER_IMAGE = "todo-app"
        GITHUB_REPO = "votre-username/votre-repo" // À modifier avec vos informations
        DOCKER_REGISTRY = "ghcr.io" // GitHub Container Registry
    }
    
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
                sh 'docker --version'
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
                    vendor/bin/phpunit --log-junit tests/results/junit.xml --testdox || echo "Tests terminés"
                '''
            }
            post {
                always {
                    script {
                        if (fileExists('tests/results/junit.xml')) {
                            junit 'tests/results/junit.xml'
                            echo "✅ Tests publiés dans Jenkins - Consultez l'onglet 'Test Results'"
                        } else {
                            echo "❌ Fichier de résultats introuvable"
                        }
                    }
                }
            }
        }
        
        stage('Build Docker Image') {
            steps {
                echo 'Construction de l\'image Docker...'
                script {
                    def imageTag = "${DOCKER_REGISTRY}/${GITHUB_REPO}/${DOCKER_IMAGE}:${BUILD_NUMBER}"
                    def latestTag = "${DOCKER_REGISTRY}/${GITHUB_REPO}/${DOCKER_IMAGE}:latest"
                    
                    sh """
                        docker build -t ${imageTag} -t ${latestTag} .
                        echo "Image construite : ${imageTag}"
                    """
                    
                    // Stocker les tags pour les étapes suivantes
                    env.IMAGE_TAG = imageTag
                    env.LATEST_TAG = latestTag
                }
            }
        }
        
        stage('Tag Repository') {
            steps {
                echo 'Tagging du repository avec la version du build...'
                script {
                    def tagName = "v${BUILD_NUMBER}"
                    
                    // Vérifier si le tag existe déjà
                    def tagExists = sh(
                        script: "git tag -l ${tagName}",
                        returnStdout: true
                    ).trim()
                    
                    if (!tagExists) {
                        sh """
                            git config user.email "jenkins@example.com"
                            git config user.name "Jenkins CI"
                            git tag -a ${tagName} -m "Release version ${BUILD_NUMBER} - Build by Jenkins"
                            echo "Tag ${tagName} créé localement"
                        """
                        
                        // Push du tag vers le repository (nécessite des credentials configurés)
                        withCredentials([gitUsernamePassword(credentialsId: 'github-credentials', gitToolName: 'Default')]) {
                            sh "git push origin ${tagName}"
                            echo "✅ Tag ${tagName} poussé vers le repository"
                        }
                    } else {
                        echo "Tag ${tagName} existe déjà, saut de cette étape"
                    }
                }
            }
        }
        
        stage('Deploy to GitHub Packages') {
            steps {
                echo 'Déploiement de l\'image vers GitHub Container Registry...'
                script {
                    // Login vers GitHub Container Registry
                    withCredentials([usernamePassword(credentialsId: 'github-token', usernameVariable: 'GITHUB_USER', passwordVariable: 'GITHUB_TOKEN')]) {
                        sh """
                            echo \$GITHUB_TOKEN | docker login ${DOCKER_REGISTRY} -u \$GITHUB_USER --password-stdin
                            
                            # Push de l'image avec le numéro de build
                            docker push ${env.IMAGE_TAG}
                            echo "✅ Image poussée : ${env.IMAGE_TAG}"
                            
                            # Push de l'image latest
                            docker push ${env.LATEST_TAG}
                            echo "✅ Image latest poussée : ${env.LATEST_TAG}"
                        """
                    }
                }
            }
        }
    }
    
    post {
        success {
            echo "✅ Build ${BUILD_NUMBER} terminé avec succès!"
            echo "🐳 Image Docker disponible : ${env.IMAGE_TAG}"
            echo "🏷️  Tag repository : v${BUILD_NUMBER}"
        }
        failure {
            echo "❌ Build ${BUILD_NUMBER} a échoué!"
        }
        always {
            echo "🧹 Nettoyage des images Docker locales..."
            sh '''
                # Nettoyer les images non utilisées
                docker image prune -f || true
            '''
        }
    }
}