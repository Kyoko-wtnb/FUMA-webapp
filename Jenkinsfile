pipeline {
    agent any
    stages {
        stage("Verify tooling") {
            steps {
                sh '''
                    docker version
                    docker compose version
                '''
            }
        }
        stage("Populate .env file") {
            steps {
                dir("/var/jenkins_home/workspace/envs/${JOB_NAME}") {
                    fileOperations([fileCopyOperation(excludes: '', flattenFiles: true, includes: '.env', targetLocation: "${WORKSPACE}")])
                }
            }
        }
        stage("Run Composer Install") {
            steps {
                sh 'docker run --rm \
                        -u "$(id -u):$(id -g)" \
                        -v "/home/ams375/FUMA-webapp/laradock-FUMA/jenkins/jenkins_home/workspace/${JOB_NAME}:/var/www/html" \
                        -w /var/www/html \
                        laravelsail/php82-composer:latest \
                        composer install --ignore-platform-reqs'
            }
        }
        stage("Run artisan tests") {
            steps {
                sh 'docker run --rm \
                        -u "$(id -u):$(id -g)" \
                        -v "/home/ams375/FUMA-webapp/laradock-FUMA/jenkins/jenkins_home/workspace/${JOB_NAME}:/var/www/html" \
                        -w /var/www/html \
                        laravelsail/php82-composer:latest \
                        php artisan test'
            }
        }
        // stage("Start Docker") {
        //     steps {
        //         sh './vendor/bin/sail up'
        //     }
        // }
        // stage("Run Tests") {
        //     steps {
        //         sh './vendor/bin/sail test'
        //     }
        // }
    }
    // post {
    //     // success {
                               
    //     // }
    //     // always {
    //     //     sh 'docker compose down --remove-orphans -v'
    //     //     sh 'docker compose ps'
    //     // }
    // }
}