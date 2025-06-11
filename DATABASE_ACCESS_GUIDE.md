# 🗄️ Database Location & Access Guide

## 📍 **Where is Your Database?**

Your database is running inside a **Docker container** named `event-booking-db` with the following details:

### **Container Information:**
- **Container Name:** `event-booking-db`
- **Image:** MySQL 8.0
- **Database Name:** `event_booking_system`
- **Status:** ✅ Running and Ready

## 🔗 **How to Access Your Database**

### **Method 1: phpMyAdmin (Web Interface) - EASIEST**
- **URL:** http://localhost:8081
- **Username:** `root`
- **Password:** `rootpassword`
- **Database:** `event_booking_system`

**Steps:**
1. Open browser → http://localhost:8081
2. Login with root/rootpassword
3. Click on `event_booking_system` database
4. Browse tables: users, events, bookings, cart_items, admin_users

### **Method 2: MySQL Workbench (External Tool)**
- **Host:** `localhost`
- **Port:** `3307` (external port)
- **Username:** `root`
- **Password:** `rootpassword`
- **Database:** `event_booking_system`

**Steps:**
1. Open MySQL Workbench
2. Create new connection with above details
3. Connect and browse your database

### **Method 3: Command Line Access**

**Enter the container:**
```bash
docker exec -it event-booking-db bash
```

**Connect to MySQL:**
```bash
mysql -u root -prootpassword event_booking_system
```

**Quick commands:**
```bash
# Show all tables
docker exec -it event-booking-db mysql -u root -prootpassword event_booking_system -e "SHOW TABLES;"

# Show events
docker exec -it event-booking-db mysql -u root -prootpassword event_booking_system -e "SELECT * FROM events LIMIT 5;"

# Show users
docker exec -it event-booking-db mysql -u root -prootpassword event_booking_system -e "SELECT * FROM users;"
```

### **Method 4: From Your PHP Application**
Your app connects automatically using these settings in `config/database.php`:
- **Host:** `db` (container name)
- **Port:** `3306` (internal port)
- **Username:** `root`
- **Password:** `rootpassword`

## 📊 **Your Database Structure**

### **Tables in event_booking_system:**
1. **`users`** - Customer accounts
2. **`events`** - Event listings
3. **`bookings`** - Event reservations
4. **`cart_items`** - Shopping cart
5. **`admin_users`** - Admin accounts

### **Sample Data Loaded:**
✅ **Events:** Tech Conference, Music Festival, Business Workshop
✅ **Admin User:** admin/admin123
✅ **Test Users:** john_doe, jane_smith

## 🔧 **Database Management Commands**

### **View Database Status:**
```bash
docker-compose ps
```

### **View Database Logs:**
```bash
docker-compose logs db
```

### **Backup Database:**
```bash
docker exec event-booking-db mysqldump -u root -prootpassword event_booking_system > backup.sql
```

### **Restore Database:**
```bash
docker exec -i event-booking-db mysql -u root -prootpassword event_booking_system < backup.sql
```

### **Reset Database:**
```bash
docker-compose down -v  # Removes data
docker-compose up -d    # Recreates with fresh data
```

## 📁 **Physical Data Location**

Your database data is stored in a **Docker volume** named `web_db_data`:

**View volume info:**
```bash
docker volume inspect web_db_data
```

**Volume location on Windows:**
```
\\wsl$\docker-desktop-data\data\docker\volumes\web_db_data\_data
```

## 🔍 **Quick Database Verification**

**Check if database is running:**
```bash
docker-compose ps
```

**Test connection:**
```bash
docker exec event-booking-db mysql -u root -prootpassword -e "SELECT 'Database is working!' as status;"
```

**Count records:**
```bash
docker exec event-booking-db mysql -u root -prootpassword event_booking_system -e "
SELECT 
  (SELECT COUNT(*) FROM users) as users,
  (SELECT COUNT(*) FROM events) as events,
  (SELECT COUNT(*) FROM bookings) as bookings;
"
```

## 🚨 **Troubleshooting**

### **Can't connect to database:**
1. Check if containers are running: `docker-compose ps`
2. Check database logs: `docker-compose logs db`
3. Restart containers: `docker-compose restart`

### **Port 3307 in use:**
Edit `docker-compose.yml` and change `3307:3306` to `3308:3306`

### **Database data lost:**
The data is persistent in Docker volumes. If lost, restart with:
```bash
docker-compose down -v
docker-compose up -d
```

---

## 🎯 **Quick Access Summary**

**🌐 Web Interface:** http://localhost:8081 (root/rootpassword)
**🔧 MySQL Workbench:** localhost:3307 (root/rootpassword)
**💻 Command Line:** `docker exec -it event-booking-db mysql -u root -prootpassword event_booking_system`

**Your database is ready and fully functional! 🎉**
