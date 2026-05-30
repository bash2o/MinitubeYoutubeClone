# 🎬 Minitube-Youtube-Clone
<div align="center">

### A Simplified YouTube Clone Built with PHP & MySQL

<img src="https://img.shields.io/badge/PHP-8+-777BB4?style=for-the-badge&logo=php&logoColor=white">
<img src="https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white">
<img src="https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white">
<img src="https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white">

**A database-driven video sharing platform inspired by YouTube**

</div>

---

## 📖 Overview

MiniTube is a full-stack web application developed as a database systems project.

The platform allows users to:

- Authenticate and access a personalized feed
- Browse subscribed channels
- Watch videos
- View and create comment threads
- Subscribe and unsubscribe from channels
- Track video popularity through dynamic badges
- Explore channel statistics and uploaded content

The project focuses heavily on **database design**, **SQL querying**, and **relational data modeling** while maintaining a clean YouTube-inspired user interface.

---

## ✨ Features

### 👤 User System

- User authentication
- Profile information
- Personalized feed

### 📺 Video Feed

- Displays videos from subscribed channels
- Ordered by upload date
- Thumbnail previews
- Direct navigation to video pages

### 🎥 Video Page

- Embedded YouTube player
- View counter (increments automatically)
- Popularity badge generation via SQL CASE expression
- Video metadata display
- Comment system

### 💬 Comment Threads

- Top-level comments
- Nested replies
- Self-referencing database design
- Thread rendering using SQL self-joins

### 📡 Channel Page

- Channel information
- Subscriber count
- Subscribe / Unsubscribe functionality
- Uploaded video listing
- Missing description handling

### 📊 Dynamic Statistics

- Top channels sidebar
- View counts
- Subscriber counts
- Popularity classification

---

## 🗄️ Database Design

The system is designed according to relational database principles and normalized structure.

### Main Entities

- USERS
- CHANNELS
- VIDEOS
- COMMENTS
- SUBSCRIPTIONS

### Design Principles

✔ Third Normal Form (3NF)

✔ Elimination of repeating groups

✔ Separation of concerns between entities

✔ Many-to-many relationships handled through junction tables

✔ Self-referencing hierarchy for comment threads

---

## 🏗️ Entity Relationship Model

### USERS
Stores user profiles and authentication information.

### CHANNELS
Represents content creators and channel metadata.

### VIDEOS
Stores video information and statistics.

### COMMENTS
Supports hierarchical discussions through:

```sql
parent_comment_id
```

allowing YouTube-style reply threads.

### SUBSCRIPTIONS

Handles the many-to-many relationship between:

```text
User ↔ Channel
```

where:

- One user can subscribe to many channels
- One channel can have many subscribers

---

## ⚙️ Technologies Used

| Technology | Purpose |
|------------|----------|
| PHP | Backend Logic |
| MySQL | Relational Database |
| HTML5 | Structure |
| CSS3 | Styling |
| SQL | Data Management |

---

## 📂 Project Structure

```text
MiniTube/
│
├── channel.php
├── watch.php
├── feed.php
├── login.php
├── install.php
│
├── database.php
├── _nav.php
│
├── seed.sql
├── generate_data.php
│
├── style.css
│
└── README.md
```

---

## 🚀 Installation

### 1. Clone Repository

```bash
git clone https://github.com/yourusername/minitube.git
```

### 2. Configure Database

Edit:

```php
database.php
```

and update your MySQL credentials.

### 3. Run Installation Script

Navigate to:

```text
http://localhost/minitube/install.php
```

This will:

- Create the database
- Create tables
- Insert seed data

### 4. Login

Open:

```text
http://localhost/minitube/login.html
```

and sign in using one of the generated demo accounts.

---

## 🔄 Application Flow

```text
User Login
      │
      ▼
Load Feed
      │
      ▼
Select Video
      │
      ▼
Increment View Count
      │
      ▼
Load Comment Thread
      │
      ▼
Add Comment
      │
      ▼
Reload Video Page
      │
      ▼
Visit Channel
      │
      ▼
Subscribe / Unsubscribe
      │
      ▼
Update Subscriber Count
```

---

## 🎯 Learning Outcomes

This project demonstrates:

- Relational database modeling
- SQL joins
- SQL self-joins
- Aggregate queries
- Database normalization
- PHP backend development
- Dynamic content rendering
- Session management
- CRUD operations

---

<div align="center">

### 🚀 Built as a Database Systems Project

*MiniTube demonstrates how relational database concepts power modern content platforms.*

</div>
