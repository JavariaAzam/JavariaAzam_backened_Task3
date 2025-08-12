# JavariaAzam_backened_Task3
The Student Task Management system utilizes CRUD operations to create, edit, read, and delete tasks successfully.
This **Student Task Management System** is a simple yet secure web application built with **PHP**, **MySQL**, **CSS**, and **Bootstrap**.
It allows students to **register, log in, and manage their personal tasks** in a structured way.

Once a student logs in, they can access the **Task Manager (tasks.php)**, where they can **add new tasks** with a title and description, **view their own tasks** in a neat Bootstrap-styled table, **edit existing tasks**, and **delete tasks** they no longer need. Each task is stored in a MySQL database with its **ID, user ID, title, description, and creation timestamp**.

The project prioritizes **security** by implementing **prepared statements** to protect against **SQL injection attacks**. It also restricts page access, ensuring that **only logged-in users** can manage tasks, and automatically redirects unauthorized users to the login page.

For styling, a **responsive Bootstrap layout** is used, ensuring the system works well on both desktop and mobile devices. The CSS is enhanced for a clean, modern, and user-friendly experience.

The project is organized into separate PHP files for **login, registration, logout, and CRUD operations**, making it **easy to maintain**. An accompanying `tasks.sql` file sets up the necessary database structure quickly. This makes it perfect for learning **backend development with PHP** while following **secure coding practices**.

