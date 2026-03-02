# Chickanji 🍗
### Created & Developed by Mohammad Ayoub

Chickanji is a high-performance, full-stack restaurant management and ordering system designed with a premium, modern aesthetic. The application offers a seamless end-to-end experience for both customers and administrators, combining sleek glassmorphic UI with a robust PHP/MySQL backend.

---

## 🚀 Key Features

### 🛒 Customer Experience (End-to-End Flow)
- **Modern Landing Page**: High-impact hero section with interactive video backgrounds and smooth typography.
- **Secure Authentication**: 
    - Full Signup/Login system with **PHP password hashing** (`password_hash`).
    - Protected user sessions and dynamic navigation states.
- **Interactive Menu**: 
    - Real-time product browsing with responsive CSS Grid layouts.
    - Glassmorphic hover effects and "Quick Add" functionality.
- **Advanced Cart Management**:
    - Persistent cart stored securely in the database.
    - AJAX-ready logic for increasing, decreasing, or removing items with instant subtotal updates.
- **One-Click Checkout**:
    - Streamlined order processing with success confirmation and order history recording.

### 🔐 Administrative Power (Pro Dashboard)
- **Live Metrics Dashboard**:
    - Real-time tracking of **Total Revenue**, **Total Orders**, and **Active Customers**.
- **Order Management System**:
    - Centralized list of all customer orders with detailed timestamping and status badges.
    - On-the-fly status updates (Pending → Completed/Cancelled).
- **Pro Menu Management**:
    - **JS Modals**: Add or Edit menu items instantly without page reloads using a sleek modal interface.
    - **Real-Time Search**: Instant filtering of the menu table by name or description.
    - **Inventory Control**: One-click "Deactivate" to hide items from the public menu without deleting records.

---

## 🛠️ Technical Architecture

### Tech Stack
- **Backend Core**: PHP 8.x with PDO (PHP Data Objects) for secure, prepared SQL statements.
- **Database**: MySQL with a relational schema optimized for high-traffic ordering.
- **Frontend Architecture**: 
    - **Vanilla CSS3**: Custom design system using CSS Variables, Flexbox, and Grid.
    - **ES6 JavaScript**: Modular logic for modals, search filtering, and UI interactions.
- **Asset Optimization**: Structured media handling for fast load times and professional organization.

### Project Structure
```text
/chickanji
  ├── admin/          # Admin-only dashboard, order logic, and setup scripts
  ├── assets/         # Publicly served static files
  │   ├── css/        # Layered design system (Glassmorphism, Responsiveness)
  │   ├── images/     # High-quality media, logos, and background video
  │   └── js/         # Client-side interactivity and AJAX handlers
  ├── includes/       # Backend core: DB connections, Auth guards, Cart logic
  ├── sql/            # Complete database schemas and migration history
  ├── index.php       # Dynamic portal & Hero section
  ├── menu.php        # Real-time menu display
  ├── cart.php        # Secure shopping cart interface
  └── ...             # Support pages (About, Contact, Checkout)
```

### Security Implementation
- **SQL Injection Prevention**: 100% of database interactions use PDO Prepared Statements.
- **Credential Security**: Passwords are never stored in plain text; industry-standard hashing is used.
- **Route Protection**: Admin directories and checkout flows are protected by session-based authentication guards.

---

## 🏁 Setup Instructions

1.  **Repository Setup**:
    ```bash
    git clone https://github.com/M7mdAyoub/chickanji.git
    ```
2.  **Database Configuration**:
    - Import `/sql/database.sql` into your MySQL server.
    - Rename `/includes/connectdb.php.example` to `connectdb.php` and update your credentials.
3.  **Administrator Initialization**:
    - Run `admin/setup.php` to generate the default secure admin credentials.

---

## 📝 License
This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---
© 2026 **Mohammad Ayoub**. All Rights Reserved.
