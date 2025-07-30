pipeline {
    agent any
    
    stages {

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