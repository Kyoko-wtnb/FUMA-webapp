String optimization_command = "composer install --optimize-autoloader \\&\\& php artisan config:cache \\&\\& php artisan event:cache \\&\\& php artisan route:cache \\&\\& php artisan view:cache"
String abs_path_of_jenkins_home_on_host = "/var/www/.laradock/data/jenkins/jenkins_home"
String abs_path_of_project_root_on_host = "/var/www/html/FUMA-webapp"

// ssh config
String user_on_main_server = "ams375"
String ip_of_main_server = "130.37.53.63"

pipeline {
    agent any
    stages {
        stage("Verify tooling") {
            steps {
                sh '''
                    docker version
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
                sh "docker run --rm \
                    -u \$(id -u):\$(id -g) \
                    -v ${abs_path_of_jenkins_home_on_host}/workspace/${JOB_NAME}:/var/www/html \
                    -w /var/www/html \
                    laravelsail/php82-composer:latest \
                    composer install --ignore-platform-reqs --no-interaction"
            }
        }
        // stage("Run artisan tests") {
        //     steps {
        //         sh "docker run --rm \
        //                 -u \$(id -u):\$(id -g) \
        //                 -v ${abs_path_of_jenkins_home_on_host}/workspace/${JOB_NAME}:/var/www/html \
        //                 -w /var/www/html \
        //                 laravelsail/php82-composer:latest \
        //                 php artisan test"
        //     }
        // }
    }
    post {
        success {
            sshagent(credentials: ['fuma_main_srv']) {
                sh "ssh -o StrictHostKeyChecking=no ${user_on_main_server}@${ip_of_main_server} git -C ${abs_path_of_project_root_on_host} fetch --all"
                sh "ssh -o StrictHostKeyChecking=no ${user_on_main_server}@${ip_of_main_server} git -C ${abs_path_of_project_root_on_host} reset --hard origin/FUMA-webapp-new-production"
                sh "ssh -o StrictHostKeyChecking=no ${user_on_main_server}@${ip_of_main_server} docker compose -f ${abs_path_of_project_root_on_host}/laradock-FUMA/production_docker-compose.yml exec --user laradock workspace bash -c \\'${optimization_command}\\'"

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