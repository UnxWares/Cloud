pipeline {
    agent any
    environment {
        HARBOR_REGISTRY = 'oci.external.infra.unxwares.com'
        HARBOR_PROJECT = 'websites'
        APP_NAME = 'com-cloud'
        IMAGE_TAG = "v${env.BUILD_NUMBER}"
        FULL_IMAGE_NAME = "${HARBOR_REGISTRY}/${HARBOR_PROJECT}/${APP_NAME}:${IMAGE_TAG}"
        HARBOR_CREDS_ID = 'harbor-creds'
    }
    stages {
        stage('Checkout Code') {
            steps {
                checkout scm
            }
        }
        stage('Build Docker Image') {
            steps {
                script {
                    echo "Construction de l'image Laravel Cloud..."
                    withCredentials([usernamePassword(credentialsId: 'unxwares-npm-token', passwordVariable: 'NPM_PASSWORD', usernameVariable: 'NPM_USERNAME')]) {
                        appImage = docker.build("${FULL_IMAGE_NAME}", "--network=host --build-arg NPM_USERNAME=${NPM_USERNAME} --build-arg NPM_PASSWORD=${NPM_PASSWORD} -f Dockerfile .")
                    }
                }
            }
        }
        stage('Push to Harbor') {
            steps {
                script {
                    echo "Envoi de l'image vers Harbor..."
                    docker.withRegistry("https://${HARBOR_REGISTRY}", "${HARBOR_CREDS_ID}") {
                        appImage.push()
                        appImage.push("latest")
                    }
                }
            }
        }
    }
    post {
        always {
            sh "docker rmi ${FULL_IMAGE_NAME} || true"
        }
    }
}
