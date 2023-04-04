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
    }
    post {
        success {
            sshagent(credentials: ['fuma_dev_srv']) {
                sh 'ssh -o StrictHostKeyChecking=no ams375@130.37.53.89 git -C /home/ams375/FUMA-webapp fetch --all'
                sh 'ssh -o StrictHostKeyChecking=no ams375@130.37.53.89 git -C /home/ams375/FUMA-webapp reset --hard origin/FUMA-webapp-new-production'
                // sh 'ssh -o StrictHostKeyChecking=no ams375@130.37.53.89 git -C /home/ams375/FUMA-webapp pull https://github.com/vufuma/FUMA-webapp.git FUMA-webapp-new'
                // script {
                //     try {
                //         sh 'ssh -o StrictHostKeyChecking=no ec2-user@13.40.116.143 sudo chmod 777 /var/www/html/storage -R'
                //     } catch (Exception e) {
                //         echo 'Some file permissions could not be updated.'
                //     }
                // }
            }                               
        }
        // always {
        //     sh 'docker compose down --remove-orphans -v'
        //     sh 'docker compose ps'
        // }
    }
}