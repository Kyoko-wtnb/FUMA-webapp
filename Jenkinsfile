pipeline {
    agent any
    stages {
        stage("Verify tooling") {
            steps {
                sh '''
                    docker info
                    docker version
                    docker compose version
                '''
            }
        }   
        stage("Start Docker") {
            steps {
                sh './vendor/bin/sail up'
            }
        }
        stage("Run Composer Install") {
            steps {
                sh 'docker run --rm \
                        -u "$(id -u):$(id -g)" \
                        -v "$(pwd):/var/www/html" \
                        -w /var/www/html \
                        laravelsail/php82-composer:latest \
                        composer install --ignore-platform-reqs'
            }
        }
             
        stage("Run Tests") {
            steps {
                sh './vendor/bin/sail test'
            }
        }
    }
    post {
        success {
                               
        }
        always {
            sh 'docker compose down --remove-orphans -v'
            sh 'docker compose ps'
        }
    }
}