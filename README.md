# Customer Relationship Management (CRM) System

## Project Description

A web-based Customer Relationship Management (CRM) system developed during an industrial training program at Visivest Corporation Sdn Bhd.

The system provides a centralized platform for managing customers, contacts, and sales leads. It allows users to store, track, update, and organize customer information throughout the sales process, improving accessibility and reducing manual record keeping.

The CRM is designed to help businesses manage relationships with current and potential customers while providing a structured workflow from lead acquisition to customer conversion.

---

## Stack Technology

### Frontend

* HTML5
* Tailwind CSS
* Alpine.js
* Laravel Blade

### Backend

* PHP
* Laravel Framework

### Database

* MySQL
* phpMyAdmin

### Development Tools

* VS Code
* Laragon
* Git
* GitHub

---

## Database Structure

### User

Stores system user information.

| Field      |
| ---------- |
| User_ID    |
| User_Name  |
| User_Email |
| User_Role  |

### Company (Customer)

Stores company/customer information.

| Field           |
| --------------- |
| Company_ID      |
| Company_Name    |
| Company_Email   |
| Company_No      |
| Company_Address |
| Status          |
| Closed_Date     |
| deleted_at      |

### Contact

Stores contact persons associated with a company.

| Field         |
| ------------- |
| Contact_ID    |
| Contact_Name  |
| Contact_Email |
| Country_Code  |
| Contact_No    |
| Contact_Role  |
| Contact_Note  |
| Company_ID    |
| deleted_at    |

### Leads

Stores potential sales opportunities.

| Field      |
| ---------- |
| Lead_ID    |
| Lead_Name  |
| Source     |
| Status     |
| Lead_Note  |
| User_ID    |
| Company_ID |
| deleted_at |

---

## Database Relationships

```text
Company (1)
│
├── Contact (Many)
│
└── Lead (Many)

User (1)
│
└── Lead (Many)
```

---

## Project Structure

```text
app/
├── Http/
│   └── Controllers/
│       ├── DashboardController.php
│       ├── CustomerController.php
│       ├── ContactController.php
│       └── LeadController.php
│
├── Models/
│   ├── Customer.php
│   ├── Contact.php
│   ├── Leads.php
│   └── User.php
│
resources/
├── views/
│   ├── dashboard.blade.php
│   ├── customers.blade.php
│   ├── customer-view.blade.php
│   ├── customer-create.blade.php
│   ├── customer-edit.blade.php
│   ├── contacts.blade.php
│   ├── contact-view.blade.php
│   ├── contact-create.blade.php
│   ├── contact-edit.blade.php
│   ├── leads.blade.php
│   ├── lead-view.blade.php
│   ├── lead-create.blade.php
│   ├── lead-edit.blade.php
│   └── recycle-bin.blade.php
│
routes/
└── web.php
```

---

## Features

### Dashboard

* KPI summary cards
* Total customers overview
* Total leads overview
* Won leads tracking
* Lost leads tracking
* Recent customers panel
* Upcoming follow-up section
* Recent activity feed
* Customer growth graph (planned)

### Customer Management

* Create customer
* View customer details
* Edit customer information
* Search customer by company name
* Filter customers by status
* Soft delete customer
* Restore customer from recycle bin
* Permanently delete customer

### Contact Management

* Create contact
* View contact details
* Edit contact information
* Search contact by name
* Search contact by company
* Assign contacts to companies
* Country code support

  * Malaysia (+60)
  * Thailand (+66)
* Soft delete contact
* Restore deleted contact

### Lead Management

* Create lead
* View lead details
* Edit lead information
* Search leads
* Assign lead to company
* Assign lead to user
* Track lead status:

  * Contacted
  * Qualified
  * Proposal Sent
  * Won
  * Lost
* Soft delete lead
* Restore deleted lead

### Recycle Bin

* View deleted customers
* View deleted contacts
* View deleted leads
* Restore records
* Permanently delete records
* Confirmation prompts before permanent deletion

### Responsive Design

* Mobile-friendly layout
* Responsive dashboard cards
* Responsive tables
* Mobile navigation menu
* Collapsible sidebar

---

## Installation

### Clone Repository

```bash
git clone <repository-url>
```

### Navigate to Project

```bash
cd project-folder
```

### Install Dependencies

```bash
composer install
npm install
```

### Configure Environment

```bash
cp .env.example .env
```

Generate application key:

```bash
php artisan key:generate
```

### Configure Database

Update the `.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mini_crm
DB_USERNAME=root
DB_PASSWORD=
```

### Run Migrations

```bash
php artisan migrate
```

### Start Development Server

```bash
php artisan serve
npm run dev
```

---

## Current Status

### Completed

* Dashboard
* Customer CRUD
* Contact CRUD
* Lead CRUD
* Database Relationships
* Search and Filtering
* Soft Delete / Recycle Bin
* Responsive UI

### In Progress

* Dashboard Charts
* Follow-up Management
* Activity Tracking

---

## Future Improvements

### High Priority

* User authentication
* User roles and permissions
* Lead-to-customer conversion
* Dashboard analytics
* Sales pipeline tracking

### Medium Priority

* Activity logs
* Follow-up scheduling
* Customer timeline
* Export to Excel
* Export to PDF
* Global search

### Future Enhancements

* Email integration
* WhatsApp integration
* Calendar integration
* File attachments
* Company profile images
* REST API
* Advanced reporting
* Audit trail system
* Dark mode

---

Ahmad Muzahid Fahmi bin Ahmad Zamidi - Intern CRM Project - YPC International College
