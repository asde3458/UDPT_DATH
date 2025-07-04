# User Service Node.js (MySQL)

## Cài đặt

1. Tạo database MySQL tên `user`:

```sql
CREATE DATABASE IF NOT EXISTS user;
USE user;
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(80) NOT NULL UNIQUE,
    password VARCHAR(120) NOT NULL,
    role VARCHAR(20) NOT NULL
);
```

2. Cài đặt package:
```bash
npm install
```

3. Tạo file `.env` (nếu chưa có):
```
PORT=5000
DB_HOST=localhost
DB_USER=root
DB_PASS=root
DB_NAME=user
```

4. Chạy service:
```bash
npm start
```

## API
- POST `/api/register` { username, password, role }
- POST `/api/login` { username, password }

## Kết nối với frontend
- Đảm bảo biến `USER_SERVICE_URL` trong frontend là `http://localhost:5000/api` 