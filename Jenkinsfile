
pipeline {
  agent any

  environment {
    DOCKERHUB_USER = 'your-dockerhub-username'  // TODO: change this
  }

  stages {
    stage('Clone Repo') {
      steps {
        // TODO: Replace URL with your actual repo
        git branch: 'main', url: 'https://github.com/YOUR-USERNAME/php-microservices-app.git'
      }
    }

    stage('Build Images') {
      steps {
        script {
          docker.build("${DOCKERHUB_USER}/php-frontend:${BUILD_NUMBER}", "frontend")
          docker.build("${DOCKERHUB_USER}/php-backend:${BUILD_NUMBER}", "backend")
        }
      }
    }

    stage('Push Images') {
      steps {
        withCredentials([usernamePassword(credentialsId: 'dockerhub-cred', usernameVariable: 'DOCKER_USER', passwordVariable: 'DOCKER_PASS')]) {
          sh '''
            echo "$DOCKER_PASS" | docker login -u "$DOCKER_USER" --password-stdin
            docker push ${DOCKERHUB_USER}/php-frontend:${BUILD_NUMBER}
            docker push ${DOCKERHUB_USER}/php-backend:${BUILD_NUMBER}
          '''
        }
      }
    }

    stage('Deploy to VM') {
      steps {
        sshagent (credentials: ['vm-ssh-cred']) {
          sh '''
            ssh -o StrictHostKeyChecking=no user@VM_IP '
              set -e
              mkdir -p ~/php-microservices-app &&
              cd ~/php-microservices-app &&
              cat > docker-compose.yml <<EOF
version: "3.9"

services:
  frontend:
    image: ${DOCKERHUB_USER}/php-frontend:${BUILD_NUMBER}
    ports:
      - "8081:80"
    depends_on:
      - backend

  backend:
    image: ${DOCKERHUB_USER}/php-backend:${BUILD_NUMBER}
    ports:
      - "8082:80"
    environment:
      - DB_HOST=db
      - DB_USER=root
      - DB_PASS=rootpass
      - DB_NAME=appdb
    depends_on:
      db:
        condition: service_started

  db:
    image: mariadb:10.5
    environment:
      MYSQL_ROOT_PASSWORD: rootpass
      MYSQL_DATABASE: appdb
    volumes:
      - db_data:/var/lib/mysql
    ports:
      - "3306:3306"

volumes:
  db_data:
EOF
              docker compose down || docker-compose down || true
              (docker compose pull || docker-compose pull) || true
              (docker compose up -d || docker-compose up -d)
            '
          '''
        }
      }
    }
  }
}
