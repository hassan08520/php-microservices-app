# PHP Multi-Container CI/CD with Jenkins

This repo contains a minimal 3-service app:
- `frontend` (PHP/Apache) calls the backend API
- `backend` (PHP/Apache) connects to MariaDB
- `db` (MariaDB official image)

## Quick Start (Local - optional)
```bash
export DOCKERHUB_USER=your-dockerhub-username
export BUILD_NUMBER=local
docker build -t $DOCKERHUB_USER/php-frontend:$BUILD_NUMBER ./frontend
docker build -t $DOCKERHUB_USER/php-backend:$BUILD_NUMBER ./backend
docker compose up -d
```

Frontend: http://localhost:8081  
Backend: http://localhost:8082/api.php

## Jenkins Setup
1. Create credentials:
   - `dockerhub-cred` (username/password)
   - `vm-ssh-cred` (SSH private key for your deployment VM)
2. Update `Jenkinsfile`:
   - `DOCKERHUB_USER`
   - Git repo URL
   - Replace `user@VM_IP` with your VM user & IP
3. Create a Pipeline job pointing to your Git repo and run it.
