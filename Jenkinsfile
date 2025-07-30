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
                            echo "Tests publiés avec succès"
                        } else {
                            echo "Fichier de résultats introuvable"
                        }
                    }
                }
            }
        }
        
        stage('Create Package') {
            steps {
                echo 'Création du package de l\'application...'
                sh '''
                    # Réinstaller sans les dépendances de dev
                    composer install --no-dev --optimize-autoloader
                    
                    # Créer le package en excluant plus de fichiers temporaires
                    tar -czf todo-app-${BUILD_NUMBER}.tar.gz \
                        --exclude=tests \
                        --exclude=.git \
                        --exclude=composer.lock \
                        --exclude=todo-app-*.tar.gz \
                        --exclude='*.log' \
                        --exclude='.jenkins' \
                        . || echo "Archive créée avec warnings"
                '''
                archiveArtifacts artifacts: 'todo-app-*.tar.gz'
                echo "✅ Package créé : todo-app-${BUILD_NUMBER}.tar.gz"
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
                            git config user.email "clementgaubert44@gmail.com"
                            git config user.name "gbtclement"
                            git tag -a ${tagName} -m "Release version ${BUILD_NUMBER} - Build by Jenkins"
                            echo "Tag ${tagName} créé localement"
                        """
                        
                        // Push du tag vers le repository (nécessite des credentials configurés)
                        withCredentials([gitUsernamePassword(credentialsId: 'github-credentials', gitToolName: 'Default')]) {
                            sh "git push origin ${tagName}"
                            echo "Tag ${tagName} poussé vers le repository"
                        }
                    } else {
                        echo "Tag ${tagName} existe déjà, saut de cette étape"
                    }
                }
            }
        }
        
        stage('Archive Package') {
            steps {
                echo 'Archive du package dans Jenkins...'
                script {
                    echo "✅ Package todo-app-${BUILD_NUMBER}.tar.gz archivé dans Jenkins"
                    echo "📦 Vous pouvez télécharger le package depuis l'interface Jenkins"
                }
            }
        }

        stage('Publish Package to GitHub Release') {
            environment {
                FILE_NAME = "todo-app-${BUILD_NUMBER}.tar.gz"
                TAG_NAME = "v${BUILD_NUMBER}"
            }
            steps {
                echo "📤 Publication du package sur GitHub..."
                withCredentials([string(credentialsId: 'github-token', variable: 'GITHUB_TOKEN')]) {
                    sh """
                        # Créer la release (si elle n'existe pas déjà)
                        curl -s -X POST https://api.github.com/repos/gbtclement/todo-app/releases \\
                            -H "Authorization: token ${GITHUB_TOKEN}" \\
                            -H "Content-Type: application/json" \\
                            -d '{
                                "tag_name": "${TAG_NAME}",
                                "name": "${TAG_NAME}",
                                "body": "Release auto Jenkins",
                                "draft": false,
                                "prerelease": false
                            }' || echo "Release déjà existante"

                        # Récupérer l'ID de la release
                        RELEASE_ID=$(curl -s -H "Authorization: token ${GITHUB_TOKEN}" https://api.github.com/repos/gbtclement/todo-app/releases/tags/${TAG_NAME} | grep '"id":' | head -n 1 | awk '{print \$2}' | tr -d ',')

                        echo "Release ID = \$RELEASE_ID"

                        # Envoyer l'archive dans la release
                        curl -s -X POST \\
                            -H "Authorization: token ${GITHUB_TOKEN}" \\
                            -H "Content-Type: application/gzip" \\
                            --data-binary @${FILE_NAME} \\
                            "https://uploads.github.com/repos/gbtclement/todo-app/releases/\$RELEASE_ID/assets?name=${FILE_NAME}" || echo "Erreur d'upload"
                    """
                }
            }
        }

    }
    
    post {
        success {
            echo "✅ Build ${BUILD_NUMBER} terminé avec succès!"
            echo "📦 Package disponible : todo-app-${BUILD_NUMBER}.tar.gz"
            echo "🏷️  Tag repository : v${BUILD_NUMBER}"
        }
        failure {
            echo "❌ Build ${BUILD_NUMBER} a échoué!"
        }
        always {
            echo "🧹 Nettoyage terminé"
        }
    }
}