# Alumni Connect Platform - System Analysis & Design Document

**Project:** Alumni Connect E-Commerce Platform  
**Course:** CS442 E-Commerce  
**Date:** November 2025  
**Version:** 1.0

---

## Table of Contents

1. [Executive Summary](#executive-summary)
2. [System Requirements Analysis](#system-requirements-analysis)
3. [Use Case Diagrams](#use-case-diagrams)
4. [Entity-Relationship Diagram](#entity-relationship-diagram)
5. [System Architecture](#system-architecture)
6. [Class Diagrams](#class-diagrams)
7. [Sequence Diagrams](#sequence-diagrams)
8. [Deployment Architecture](#deployment-architecture)
9. [Technology Stack](#technology-stack)

---

## 1. Executive Summary

Alumni Connect is a comprehensive e-commerce platform designed to facilitate connections between alumni and students while monetizing services through a marketplace model. The platform enables users to purchase job postings, mentorship sessions, event tickets, and premium features, creating a sustainable revenue stream for the alumni network.

**Key Features:**
- Service catalog with search and filtering
- Shopping cart management
- Secure checkout with Paystack payment integration
- Order tracking and invoice generation
- User authentication and role-based access

---

## 2. System Requirements Analysis

### 2.1 Functional Requirements

#### FR1: User Management
- **FR1.1:** Users must be able to register as Student, Alumni, or Admin
- **FR1.2:** Users must authenticate via email and password
- **FR1.3:** System must maintain user sessions securely
- **FR1.4:** Users must be able to update their profiles

#### FR2: Service Catalog Management
- **FR2.1:** System must display all available services (job postings, mentorship, events, premium features)
- **FR2.2:** Users must be able to search services by keyword
- **FR2.3:** Users must be able to filter services by:
  - Service type (job_posting, mentorship, event, premium_feature)
  - Category
  - Price range (min/max)
- **FR2.4:** System must display service details including price, description, availability

#### FR3: Shopping Cart Management
- **FR3.1:** Users must be able to add services to cart
- **FR3.2:** Users must be able to update quantities in cart
- **FR3.3:** Users must be able to remove items from cart
- **FR3.4:** System must validate stock availability before checkout
- **FR3.5:** System must calculate subtotal, tax (12.5%), and total amount

#### FR4: Order Processing
- **FR4.1:** System must collect billing information (name, email, phone)
- **FR4.2:** System must generate unique order numbers (format: ORD-YYYYMMDD-XXXX)
- **FR4.3:** System must create orders with status tracking
- **FR4.4:** System must clear cart after successful order creation

#### FR5: Payment Integration
- **FR5.1:** System must integrate with Paystack payment gateway
- **FR5.2:** System must verify payment status via Paystack API
- **FR5.3:** System must update order status upon successful payment
- **FR5.4:** System must record payment transactions
- **FR5.5:** System must handle payment failures gracefully

#### FR6: Order Management
- **FR6.1:** Users must be able to view order history
- **FR6.2:** System must display order status (pending, processing, completed, cancelled)
- **FR6.3:** System must display payment status (unpaid, paid, refunded)
- **FR6.4:** Users must be able to download invoices for paid orders
- **FR6.5:** System must allow order cancellation with stock restoration

### 2.2 Non-Functional Requirements

#### NFR1: Performance
- **NFR1.1:** Page load time must not exceed 3 seconds
- **NFR1.2:** Database queries must execute within 1 second
- **NFR1.3:** System must handle 100 concurrent users

#### NFR2: Security
- **NFR2.1:** User passwords must be hashed using password_hash()
- **NFR2.2:** All payment data must be transmitted via HTTPS
- **NFR2.3:** Session data must expire after 24 hours of inactivity
- **NFR2.4:** SQL injection prevention through prepared statements
- **NFR2.5:** XSS prevention through input sanitization

#### NFR3: Usability
- **NFR3.1:** Interface must be responsive (mobile, tablet, desktop)
- **NFR3.2:** Navigation must be intuitive with max 3 clicks to any feature
- **NFR3.3:** Error messages must be clear and actionable
- **NFR3.4:** Forms must provide real-time validation

#### NFR4: Scalability
- **NFR4.1:** Database must support 10,000+ services
- **NFR4.2:** System must handle 1,000+ orders per day
- **NFR4.3:** Architecture must support horizontal scaling

#### NFR5: Maintainability
- **NFR5.1:** Code must follow MVC architecture pattern
- **NFR5.2:** All classes must have PHPDoc comments
- **NFR5.3:** Database must use foreign key constraints
- **NFR5.4:** Code must follow PSR coding standards

---

## 3. Use Case Diagrams

### 3.1 Overall System Use Cases

```
                    Alumni Connect E-Commerce Platform
                    
    ┌─────────────┐                                      ┌─────────────┐
    │   Student   │                                      │   Alumni    │
    └──────┬──────┘                                      └──────┬──────┘
           │                                                    │
           │    Browse Services                                │
           ├───────────────────────────────────────────────────┤
           │                                                    │
           │    Search & Filter                                │
           ├───────────────────────────────────────────────────┤
           │                                                    │
           │    Add to Cart                                    │
           ├───────────────────────────────────────────────────┤
           │                                                    │
           │    Update Cart                                    │
           ├───────────────────────────────────────────────────┤
           │                                                    │
           │    Checkout                                       │
           ├───────────────────────────────────────────────────┤
           │                                                    │
           │    Make Payment ◄─────────┐                      │
           ├────────────────────────────┼───────────────────────┤
           │                            │                       │
           │    View Orders             │                       │
           ├────────────────────────────┼───────────────────────┤
           │                            │                       │
           │    Download Invoice        │                       │
           │                            │                       │
           │                      ┌─────┴─────┐                │
           │                      │  Paystack │                │
           │                      │  Gateway  │                │
           │                      └───────────┘                │
           │                                                    │
    ┌──────┴──────┐                                     ┌──────┴──────┐
    │   Manage    │                                     │   Manage    │
    │  Profile    │                                     │  Services   │
    └─────────────┘                                     └─────────────┘
           │                                                    │
           │                                                    │
    ┌──────┴──────────────────────────────────────────────────┴──────┐
    │                                                                  │
    │                           Admin                                 │
    │                                                                  │
    │  • Manage Users                                                 │
    │  • Manage Services                                              │
    │  • View All Orders                                              │
    │  • Generate Reports                                             │
    │  • System Configuration                                         │
    └─────────────────────────────────────────────────────────────────┘
```

### 3.2 Use Case: Complete Purchase Flow

```
Actor: Student/Alumni

1. Browse Services
   ├─ View service catalog
   ├─ Apply filters (type, category, price)
   └─ Search by keyword

2. Add to Cart
   ├─ Select service
   ├─ Specify quantity
   ├─ Add special requests (optional)
   └─ System validates availability

3. Review Cart
   ├─ View cart items
   ├─ Update quantities
   ├─ Remove unwanted items
   └─ View total with tax

4. Checkout
   ├─ Enter billing information
   ├─ Review order summary
   └─ Initiate payment

5. Payment (Paystack)
   ├─ Enter card details
   ├─ Authorize payment
   └─ Receive confirmation

6. Order Confirmation
   ├─ View order details
   ├─ Receive order number
   └─ Download invoice

Alternative Flows:
- A1: Service out of stock → Display error, prevent checkout
- A2: Payment fails → Return to checkout, show error
- A3: Session timeout → Redirect to login, preserve cart
```

---

## 4. Entity-Relationship Diagram

### 4.1 Complete ER Diagram

```
┌─────────────────┐
│     users       │
├─────────────────┤
│ PK user_id      │
│    email        │◄───────────┐
│    password     │            │
│    first_name   │            │
│    last_name    │            │
│    user_role    │            │
│    created_at   │            │
└─────────────────┘            │
                               │
┌─────────────────┐            │
│    students     │            │
├─────────────────┤            │
│ PK student_id   │            │
│ FK user_id      │────────────┤
│    major        │            │
│    grad_year    │            │
└─────────────────┘            │
                               │
┌─────────────────┐            │
│     alumni      │            │
├─────────────────┤            │
│ PK alumni_id    │            │
│ FK user_id      │────────────┘
│    grad_year    │
│    company      │
│    position     │
└─────────────────┘


┌─────────────────────────────┐
│         services            │
├─────────────────────────────┤
│ PK service_id               │
│    service_name             │
│    service_type             │◄─────┐
│    category                 │      │
│    description              │      │
│    price                    │      │
│    duration                 │      │
│    stock_quantity           │      │
│    provider_id              │      │
│    image_url                │      │
│    is_active                │      │
│    created_at               │      │
└─────────────────────────────┘      │
         │                            │
         │                            │
         │                            │
┌────────┴──────────┐                │
│       cart        │                │
├───────────────────┤                │
│ PK cart_id        │                │
│ FK user_id        │                │
│ FK service_id     │────────────────┘
│    quantity       │
│    selected_date  │
│    selected_time  │
│    special_req... │
│    added_at       │
└───────────────────┘
         │
         │
         │
┌────────┴──────────────────┐
│        orders             │
├───────────────────────────┤
│ PK order_id               │
│ FK user_id                │
│    order_number           │
│    order_status           │
│    payment_status         │
│    subtotal               │
│    tax_amount             │
│    total_amount           │
│    billing_name           │
│    billing_email          │
│    billing_phone          │
│    special_notes          │
│    order_date             │
│    payment_date           │
└───────────────────────────┘
         │
         ├──────────────────┐
         │                  │
┌────────┴──────────┐  ┌────┴────────────┐
│   order_items     │  │    payments     │
├───────────────────┤  ├─────────────────┤
│ PK item_id        │  │ PK payment_id   │
│ FK order_id       │  │ FK order_id     │
│ FK service_id     │  │    reference    │
│    quantity       │  │    amount       │
│    price          │  │    status       │
│    fulfillment... │  │    gateway      │
│    notes          │  │    paid_at      │
└───────────────────┘  └─────────────────┘
         │
         │
┌────────┴──────────┐
│     invoices      │
├───────────────────┤
│ PK invoice_id     │
│ FK order_id       │
│    invoice_num... │
│    generated_at   │
└───────────────────┘
```

### 4.2 Key Relationships

1. **users → students/alumni** (1:1)
   - One user account can be either student or alumni

2. **users → cart** (1:N)
   - One user can have multiple items in cart

3. **services → cart** (1:N)
   - One service can be in multiple carts

4. **users → orders** (1:N)
   - One user can place multiple orders

5. **orders → order_items** (1:N)
   - One order contains multiple items

6. **services → order_items** (1:N)
   - One service can appear in multiple orders

7. **orders → payments** (1:1)
   - One order has one payment transaction

8. **orders → invoices** (1:1)
   - One order generates one invoice

---

## 5. System Architecture

### 5.1 Three-Tier Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                     PRESENTATION LAYER                          │
│                                                                 │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐        │
│  │   index.php  │  │ services.php │  │   cart.php   │        │
│  └──────────────┘  └──────────────┘  └──────────────┘        │
│                                                                 │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐        │
│  │checkout.php  │  │  orders.php  │  │ dashboard.php│        │
│  └──────────────┘  └──────────────┘  └──────────────┘        │
│                                                                 │
│  Technologies: HTML5, Tailwind CSS 3.x, JavaScript ES6        │
└─────────────────────────────────────────────────────────────────┘
                              │
                              │ HTTP/HTTPS
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                      BUSINESS LOGIC LAYER                       │
│                                                                 │
│  ┌─────────────────────────────────────────────────────┐      │
│  │              Controllers                             │      │
│  │  • product_controller.php                           │      │
│  │  • cart_controller.php                              │      │
│  │  • order_controller.php                             │      │
│  │  • customer_controller.php                          │      │
│  └─────────────────────────────────────────────────────┘      │
│                              │                                  │
│  ┌─────────────────────────────────────────────────────┐      │
│  │                  Classes                             │      │
│  │  • service_class.php (422 lines)                    │      │
│  │  • cart_class.php (258 lines)                       │      │
│  │  • order_class.php (390 lines)                      │      │
│  │  • payment_class.php (63 lines)                     │      │
│  │  • customer_class.php                               │      │
│  └─────────────────────────────────────────────────────┘      │
│                              │                                  │
│  ┌─────────────────────────────────────────────────────┐      │
│  │                Actions (AJAX)                        │      │
│  │  • add_to_cart_action.php                           │      │
│  │  • update_cart_action.php                           │      │
│  │  • create_order_action.php                          │      │
│  │  • verify_payment_action.php                        │      │
│  └─────────────────────────────────────────────────────┘      │
│                                                                 │
│  Technologies: PHP 7.4+/8.x, OOP Principles, MVC Pattern      │
└─────────────────────────────────────────────────────────────────┘
                              │
                              │ MySQLi
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                        DATA LAYER                               │
│                                                                 │
│  ┌─────────────────────────────────────────────────────┐      │
│  │            Database: alumni_connect                  │      │
│  │                                                      │      │
│  │  Core Tables (13):                                   │      │
│  │  • users, students, alumni                          │      │
│  │  • categories, brands, products                     │      │
│  │  • cart, orders, order_details, payment             │      │
│  │                                                      │      │
│  │  E-Commerce Tables (6):                              │      │
│  │  • services (14 sample services)                    │      │
│  │  • cart (shopping cart)                             │      │
│  │  • orders (order records)                           │      │
│  │  • order_items (order line items)                   │      │
│  │  • payments (transaction records)                   │      │
│  │  • invoices (generated invoices)                    │      │
│  └─────────────────────────────────────────────────────┘      │
│                                                                 │
│  Technologies: MySQL 5.7+/8.0, InnoDB Engine, ACID Support    │
└─────────────────────────────────────────────────────────────────┘
```

### 5.2 External Integrations

```
┌─────────────────────────────────────────────────────────────┐
│                    Alumni Connect Platform                   │
└─────────────────────────────────────────────────────────────┘
                              │
                ┌─────────────┼─────────────┐
                │                           │
                ▼                           ▼
┌───────────────────────────┐   ┌───────────────────────────┐
│   Paystack Payment API    │   │    CDN Services           │
│   (Ghana)                 │   │                           │
│                           │   │  • Tailwind CSS CDN       │
│  • Card Payments          │   │  • Font Awesome 6.0       │
│  • Mobile Money           │   │  • Google Fonts           │
│  • Bank Transfer          │   │  • Alpine.js              │
│  • Payment Verification   │   │                           │
└───────────────────────────┘   └───────────────────────────┘
```

---

## 6. Class Diagrams

### 6.1 Service Management Classes

```
┌──────────────────────────────────────────────────────────┐
│                    Service                               │
├──────────────────────────────────────────────────────────┤
│ - db_connect: mysqli                                     │
├──────────────────────────────────────────────────────────┤
│ + __construct()                                          │
│ + addService(data: array): int                           │
│ + getAllServices(type, category, search, min, max): arr │
│ + getServiceById(service_id: int): array                 │
│ + updateService(service_id: int, data: array): bool      │
│ + deleteService(service_id: int): bool                   │
│ + getCategories(type: string): array                     │
│ + checkAvailability(service_id: int, qty: int): bool     │
│ + updateStock(service_id: int, qty: int): bool           │
│ + getServicesByProvider(provider_id: int): array         │
└──────────────────────────────────────────────────────────┘
```

### 6.2 Shopping Cart Classes

```
┌──────────────────────────────────────────────────────────┐
│                       Cart                               │
├──────────────────────────────────────────────────────────┤
│ - db_connect: mysqli                                     │
├──────────────────────────────────────────────────────────┤
│ + __construct()                                          │
│ + addToCart(user_id, service_id, qty, ...): bool        │
│ + getCartItems(user_id: int): array                      │
│ + getCartCount(user_id: int): int                        │
│ + getCartTotal(user_id: int): float                      │
│ + updateCartQuantity(cart_id, qty: int): bool            │
│ + removeFromCart(cart_id: int): bool                     │
│ + clearCart(user_id: int): bool                          │
│ + validateCart(user_id: int): array                      │
│ + getCartSummary(user_id: int): array                    │
└──────────────────────────────────────────────────────────┘
                              │
                              │ uses
                              ▼
┌──────────────────────────────────────────────────────────┐
│                      Service                             │
│  (to validate stock and get prices)                      │
└──────────────────────────────────────────────────────────┘
```

### 6.3 Order Management Classes

```
┌──────────────────────────────────────────────────────────┐
│                       Order                              │
├──────────────────────────────────────────────────────────┤
│ - db_connect: mysqli                                     │
├──────────────────────────────────────────────────────────┤
│ + __construct()                                          │
│ + createOrder(user_id, billing_info): int                │
│ + generateOrderNumber(): string                          │
│ + getOrderById(order_id: int): array                     │
│ + getOrderByNumber(order_number: string): array          │
│ + getOrderItems(order_id: int): array                    │
│ + getUserOrders(user_id: int): array                     │
│ + updateOrderStatus(order_id, status): bool              │
│ + updatePaymentStatus(order_id, status): bool            │
│ + cancelOrder(order_id: int): bool                       │
│ + generateInvoice(order_id: int): bool                   │
│ + getAllOrders(filters: array): array                    │
└──────────────────────────────────────────────────────────┘
                              │
                              │ uses
                              ▼
┌──────────────────────────────────────────────────────────┐
│                     Payment                              │
├──────────────────────────────────────────────────────────┤
│ - db_connect: mysqli                                     │
├──────────────────────────────────────────────────────────┤
│ + __construct()                                          │
│ + recordPayment(payment_data: array): int                │
│ + getPaymentByReference(reference: string): array        │
│ + getPaymentByOrderId(order_id: int): array              │
└──────────────────────────────────────────────────────────┘
```

### 6.4 Class Relationships

```
                 ┌─────────────┐
                 │  db_class   │
                 └──────┬──────┘
                        │ extends
           ┌────────────┼────────────┬────────────┐
           │            │            │            │
    ┌──────▼──────┐ ┌──▼──────┐ ┌──▼──────┐ ┌───▼────────┐
    │   Service   │ │  Cart   │ │  Order  │ │  Payment   │
    └─────────────┘ └─────────┘ └─────────┘ └────────────┘
           │            │            │
           │            │ uses       │ uses
           └────────────┴────────────┘
```

---

## 7. Sequence Diagrams

### 7.1 Add to Cart Sequence

```
User          Browser        CartAction       Cart Class      Service Class    Database
 │               │                │                │                │              │
 │  Click "Add" │                │                │                │              │
 ├──────────────>│                │                │                │              │
 │               │  AJAX POST     │                │                │              │
 │               ├────────────────>│                │                │              │
 │               │                │  addToCart()   │                │              │
 │               │                ├────────────────>│                │              │
 │               │                │                │ checkAvail()   │              │
 │               │                │                ├────────────────>│              │
 │               │                │                │                │  SELECT      │
 │               │                │                │                ├──────────────>│
 │               │                │                │                │  stock_qty   │
 │               │                │                │<───────────────┤              │
 │               │                │                │  validate      │              │
 │               │                │<───────────────┤                │              │
 │               │                │                │  INSERT cart   │              │
 │               │                │                ├────────────────┼──────────────>│
 │               │                │                │                │   success    │
 │               │                │<───────────────┴────────────────┴──────────────┤
 │               │                │  getCartCount()│                │              │
 │               │                ├────────────────>│                │  COUNT(*)    │
 │               │                │                ├────────────────┼──────────────>│
 │               │                │<───────────────┤      count     │<─────────────┤
 │               │  JSON response │                │                │              │
 │               │<───────────────┤                │                │              │
 │  Update UI    │                │                │                │              │
 │<──────────────┤                │                │                │              │
 │  (show badge) │                │                │                │              │
```

### 7.2 Checkout and Payment Sequence

```
User        Checkout Page    CreateOrder      Order Class     Cart Class    Paystack API    VerifyPayment    Database
 │               │                │                │                │              │               │              │
 │  Enter info   │                │                │                │              │               │              │
 ├──────────────>│                │                │                │              │               │              │
 │               │  Submit form   │                │                │              │               │              │
 │               ├────────────────>│                │                │              │               │              │
 │               │                │  validateCart()│                │              │               │              │
 │               │                ├────────────────┼────────────────>│              │               │              │
 │               │                │                │                │  SELECT      │               │              │
 │               │                │                │                ├──────────────┼───────────────┼──────────────>│
 │               │                │<───────────────┴────────────────┤  validation  │               │              │
 │               │                │  createOrder() │                │              │               │              │
 │               │                ├────────────────>│                │              │               │              │
 │               │                │                │  BEGIN TRANS   │              │               │              │
 │               │                │                ├────────────────┼──────────────┼───────────────┼──────────────>│
 │               │                │                │  INSERT order  │              │               │              │
 │               │                │                ├────────────────┼──────────────┼───────────────┼──────────────>│
 │               │                │                │  INSERT items  │              │               │              │
 │               │                │                ├────────────────┼──────────────┼───────────────┼──────────────>│
 │               │                │                │  COMMIT        │              │               │              │
 │               │                │                ├────────────────┼──────────────┼───────────────┼──────────────>│
 │               │                │<───────────────┤  order_id      │              │               │              │
 │               │<───────────────┤                │                │              │               │              │
 │  Show Paystack│                │                │                │              │               │              │
 │     popup     │                │                │                │              │               │              │
 │<──────────────┤                │                │                │              │               │              │
 │  Enter card   │                │                │                │              │               │              │
 │   details     │                │                │                │              │               │              │
 ├───────────────┼────────────────┼────────────────┼────────────────┼──────────────>│               │              │
 │               │                │                │                │   charge card │               │              │
 │               │                │                │                │<──────────────┤               │              │
 │  Callback     │                │                │                │   reference   │               │              │
 │<──────────────┤                │                │                │              │               │              │
 │               │  AJAX verify   │                │                │              │               │              │
 │               ├────────────────┼────────────────┼────────────────┼──────────────┼───────────────>│              │
 │               │                │                │                │              │  GET verify   │              │
 │               │                │                │                │              │<──────────────┤              │
 │               │                │                │                │              │  success      │              │
 │               │                │                │                │              ├───────────────>│              │
 │               │                │                │  updatePayment │              │               │  UPDATE      │
 │               │                │                ├────────────────┼──────────────┼───────────────┼──────────────>│
 │               │                │                │  generateInv() │              │               │  INSERT      │
 │               │                │                ├────────────────┼──────────────┼───────────────┼──────────────>│
 │               │<───────────────┴────────────────┴────────────────┴──────────────┴───────────────┤              │
 │  Redirect     │                │                │                │              │               │              │
 │   success     │                │                │                │              │               │              │
 │<──────────────┤                │                │                │              │               │              │
```

### 7.3 View Orders Sequence

```
User        Orders Page      Order Class      Database
 │               │                │                │
 │  Visit page   │                │                │
 ├──────────────>│                │                │
 │               │ getUserOrders()│                │
 │               ├────────────────>│                │
 │               │                │  SELECT orders │
 │               │                ├────────────────>│
 │               │                │  JOIN users    │
 │               │                │<───────────────┤
 │               │                │ getOrderItems()│
 │               │                ├────────────────>│
 │               │                │ SELECT items   │
 │               │                │<───────────────┤
 │               │<───────────────┤                │
 │  Display list │                │                │
 │<──────────────┤                │                │
 │               │                │                │
 │  Click invoice│                │                │
 ├──────────────>│                │                │
 │               │  Redirect to   │                │
 │               │  invoice.php   │                │
 │<──────────────┤                │                │
```

---

## 8. Deployment Architecture

### 8.1 Development Environment (XAMPP)

```
┌───────────────────────────────────────────────────────────────┐
│                    Windows OS (Local Machine)                 │
│                                                               │
│  ┌─────────────────────────────────────────────────────────┐ │
│  │                   XAMPP Stack                            │ │
│  │                                                          │ │
│  │  ┌──────────────────────────────────────────────────┐  │ │
│  │  │  Apache HTTP Server 2.4                          │  │ │
│  │  │  • Port: 80 (HTTP)                               │  │ │
│  │  │  • Port: 443 (HTTPS)                             │  │ │
│  │  │  • Document Root: C:\xampp\htdocs\AlumniConnect │  │ │
│  │  │  • mod_rewrite enabled                           │  │ │
│  │  └──────────────────────────────────────────────────┘  │ │
│  │                          │                              │ │
│  │  ┌──────────────────────▼──────────────────────────┐  │ │
│  │  │  PHP 8.x                                         │  │ │
│  │  │  • Extensions: mysqli, curl, json, mbstring      │  │ │
│  │  │  • Session handling enabled                      │  │ │
│  │  │  • Error reporting: E_ALL (development)          │  │ │
│  │  └──────────────────────────────────────────────────┘  │ │
│  │                          │                              │ │
│  │  ┌──────────────────────▼──────────────────────────┐  │ │
│  │  │  MySQL 8.0                                       │  │ │
│  │  │  • Port: 3306                                    │  │ │
│  │  │  • Database: alumni_connect                      │  │ │
│  │  │  • User: root (no password - dev only)           │  │ │
│  │  │  • Character Set: utf8mb4                        │  │ │
│  │  │  • 19 tables (13 core + 6 e-commerce)            │  │ │
│  │  └──────────────────────────────────────────────────┘  │ │
│  │                                                          │ │
│  └─────────────────────────────────────────────────────────┘ │
│                                                               │
│  Access: http://localhost/AlumniConnect                      │
└───────────────────────────────────────────────────────────────┘
```

### 8.2 Production Environment (Recommended)

```
┌──────────────────────────────────────────────────────────────────┐
│                        Cloud Hosting (AWS/Azure/GCP)             │
│                                                                  │
│  ┌────────────────────────────────────────────────────────────┐ │
│  │                    Load Balancer (HTTPS)                   │ │
│  │                    SSL Certificate                         │ │
│  └─────────────────────────┬──────────────────────────────────┘ │
│                            │                                     │
│              ┌─────────────┴─────────────┐                      │
│              │                           │                       │
│  ┌───────────▼──────────┐    ┌──────────▼──────────┐          │
│  │   Web Server 1       │    │   Web Server 2      │          │
│  │   • Apache/Nginx     │    │   • Apache/Nginx    │          │
│  │   • PHP 8.x          │    │   • PHP 8.x         │          │
│  │   • Application Code │    │   • Application Code│          │
│  └───────────┬──────────┘    └──────────┬──────────┘          │
│              │                           │                       │
│              └─────────────┬─────────────┘                      │
│                            │                                     │
│              ┌─────────────▼─────────────┐                      │
│              │   Database Server (RDS)   │                      │
│              │   • MySQL 8.0             │                      │
│              │   • Encrypted             │                      │
│              │   • Automated backups     │                      │
│              │   • Read replicas         │                      │
│              └───────────────────────────┘                      │
│                                                                  │
│  ┌────────────────────────────────────────────────────────────┐ │
│  │                    External Services                        │ │
│  │  • Paystack API (payments)                                 │ │
│  │  • CDN (Cloudflare/AWS CloudFront)                         │ │
│  │  • Email Service (SendGrid/AWS SES)                        │ │
│  │  • Monitoring (Datadog/New Relic)                          │ │
│  └────────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────┘
```

### 8.3 File Structure

```
AlumniConnect/
│
├── index.php                      # Landing page
│
├── actions/                       # AJAX handlers
│   ├── add_to_cart_action.php
│   ├── update_cart_action.php
│   ├── remove_from_cart_action.php
│   ├── create_order_action.php
│   ├── verify_payment_action.php
│   ├── login_action.php
│   └── register_action.php
│
├── classes/                       # Business logic classes
│   ├── service_class.php         # 422 lines
│   ├── cart_class.php            # 258 lines
│   ├── order_class.php           # 390 lines
│   ├── payment_class.php         # 63 lines
│   ├── customer_class.php
│   ├── product_class.php
│   ├── category_class.php
│   └── brand_class.php
│
├── controllers/                   # MVC controllers
│   ├── product_controller.php
│   ├── cart_controller.php
│   ├── order_controller.php
│   ├── customer_controller.php
│   ├── alumni_controller.php
│   └── student_controller.php
│
├── views/                         # User interface pages
│   ├── services.php              # Service catalog (294 lines)
│   ├── cart.php                  # Shopping cart (256 lines)
│   ├── checkout.php              # Checkout page (292 lines)
│   ├── orders.php                # Order history (192 lines)
│   ├── payment_success.php       # Success page (118 lines)
│   ├── order_details.php         # Order details view
│   ├── invoice.php               # Printable invoice
│   ├── dashboard.php
│   ├── profile.php
│   ├── alumni_search.php
│   ├── jobs.php
│   ├── events.php
│   ├── messages.php
│   └── includes/
│       ├── navbar.php            # Top navigation
│       └── sidebar.php           # Sidebar menu
│
├── admin/                         # Admin panel
│   ├── dashboard.php
│   ├── product.php
│   ├── category.php
│   └── brand.php
│
├── login/                         # Authentication
│   ├── login.php
│   ├── register.php
│   └── logout.php
│
├── settings/                      # Configuration
│   ├── core.php                  # Core functions
│   ├── db_class.php             # Database connection
│   └── db_cred.php              # Database credentials
│
├── db/                            # Database files
│   ├── dbforlab.sql             # Initial schema
│   └── ecommerce_update.sql     # E-commerce tables + data
│
├── js/                            # JavaScript files
│   ├── product.js
│   ├── cart.js
│   ├── checkout.js
│   └── login.js
│
├── uploads/                       # User uploads
│   └── products/
│
└── docs/                          # Documentation
    └── System_Analysis_and_Design.md
```

---

## 9. Technology Stack

### 9.1 Frontend Technologies

| Technology | Version | Purpose |
|------------|---------|---------|
| HTML5 | Latest | Structure and semantics |
| Tailwind CSS | 3.x (CDN) | Responsive styling |
| JavaScript | ES6 | Client-side interactivity |
| Font Awesome | 6.0.0 | Icons |
| Google Fonts | Inter | Typography |
| Alpine.js | 3.x | Lightweight reactivity |

### 9.2 Backend Technologies

| Technology | Version | Purpose |
|------------|---------|---------|
| PHP | 7.4+/8.x | Server-side logic |
| MySQLi | Native | Database connectivity |
| Sessions | PHP Native | User authentication |
| cURL | PHP Extension | API communication |
| JSON | PHP Native | Data exchange format |

### 9.3 Database

| Component | Details |
|-----------|---------|
| DBMS | MySQL 8.0 |
| Engine | InnoDB |
| Character Set | utf8mb4 |
| Collation | utf8mb4_unicode_ci |
| Tables | 19 total (13 core + 6 e-commerce) |
| Sample Data | 14 services, 4 orders, 3 payments |

### 9.4 Third-Party Services

| Service | Purpose | Integration |
|---------|---------|-------------|
| Paystack | Payment processing | REST API |
| Tailwind CDN | CSS framework | CDN Link |
| Font Awesome | Icons | CDN Link |
| Google Fonts | Web fonts | CDN Link |

### 9.5 Development Tools

| Tool | Purpose |
|------|---------|
| XAMPP | Local development environment |
| Git | Version control |
| VS Code | Code editor |
| phpMyAdmin | Database management |
| Chrome DevTools | Debugging |

---

## 10. Security Considerations

### 10.1 Authentication & Authorization
- Passwords hashed using `password_hash()`
- Session-based authentication
- Role-based access control (Student, Alumni, Admin)
- Session timeout after inactivity

### 10.2 Data Protection
- Prepared statements for all SQL queries (SQL injection prevention)
- Input sanitization with `htmlspecialchars()` (XSS prevention)
- CSRF protection for forms
- Payment data never stored (PCI DSS compliance)

### 10.3 API Security
- Paystack secret key stored server-side only
- HTTPS required for payment transactions
- API request verification
- Rate limiting on payment endpoints

---

## 11. Testing Strategy

### 11.1 Unit Testing
- Test individual class methods
- Validate database operations
- Check input validation logic

### 11.2 Integration Testing
- Test complete user flows
- Verify payment integration
- Test cart-to-order conversion

### 11.3 User Acceptance Testing
- Student flow: Browse → Cart → Checkout → Order
- Alumni flow: Same as student
- Admin flow: Manage services and orders

---

## 12. Future Enhancements

1. **Real-time Features**
   - WebSocket integration for live notifications
   - Real-time chat between alumni and students

2. **Advanced Analytics**
   - Sales dashboard
   - User behavior tracking
   - Revenue reports

3. **Mobile Application**
   - Native iOS/Android apps
   - React Native implementation

4. **Enhanced Features**
   - Service reviews and ratings
   - Recommendation engine
   - Loyalty program
   - Subscription services

---

## Conclusion

The Alumni Connect platform successfully implements a comprehensive e-commerce system that meets all CS442 course requirements. The system demonstrates:

- ✅ **Complete MVC Architecture** - Separation of concerns
- ✅ **Professional Database Design** - Normalized schema with 19 tables
- ✅ **Secure Payment Integration** - Paystack for Ghana market
- ✅ **Responsive Design** - Mobile-first approach
- ✅ **Production-Ready Code** - Comprehensive documentation
- ✅ **Scalable Architecture** - Ready for growth

The platform is ready for deployment and provides a solid foundation for connecting alumni with students while generating sustainable revenue through service offerings.

---

**Document Version:** 1.0  
**Last Updated:** November 25, 2025  
**Author:** Alumni Connect Development Team  
**Status:** Final for Submission
