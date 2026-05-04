# **Technical Brief: Esirom Social Media Client Hub**

## **1\. Project Overview**

### **1.1. Vision**

To create a comprehensive, client-facing web application that centralizes social media reporting and content management. The platform will provide a customizable and interactive experience for clients to review their social media performance, approve content, and track their marketing goals.

### **1.2. Goals**

* **Streamline Client Reporting:** Automate the data collection process and provide a single source of truth for social media performance.  
* **Enhance Client Collaboration:** Create a transparent workflow for content approval and feedback.  
* **Provide Actionable Insights:** Go beyond raw data to offer insights and track progress against client-specific KPIs.  
* **Scalable Architecture:** Build a system that can grow with your agency, accommodating more clients and social media platforms in the future.

### **1.3. Target Audience**

* **Primary Users:** Your agency's team (Admins, Brand Representatives).  
* **Secondary Users:** Your clients, who will use the platform to access their reports and content calendars.

## **2\. Core Features**

### **2.1. Client Dashboard**

A secure, customizable portal for each client.

* **Client View:**  
  * Displays key metrics for each social media platform.  
  * Visualizes progress towards KPIs.  
  * Provides a view of the upcoming content calendar.  
  * Allows clients to approve or request edits to scheduled posts.  
* **Admin Customization:**  
  * Admins can toggle specific widgets and reports on or off for each client, tailoring the dashboard to their needs.

### **2.2. Content Calendar**

An interactive calendar for planning and managing social media content.

* **Multi-Platform Scheduling:** Create and schedule posts for Meta (Facebook & Instagram), YouTube, LinkedIn, and X.  
* **Approval Workflow:**  
  1. **Draft:** A post is created.  
  2. **Pending Approval:** The post is submitted for client review.  
  3. **Approved/Needs Revision:** The client can approve the post or send it back with comments.  
  4. **Scheduled:** Once approved, the post is scheduled for publishing.  
  5. **Published:** The post is live, and the system begins to track its performance.

### **2.3. Reporting System**

A flexible system for generating both automated and manual reports.

* **Automated Reports:**  
  * Integrates with the APIs of Meta, YouTube, LinkedIn, and X.  
  * Fetches data for organic posts and paid ad campaigns.  
  * Metrics to include: Reach, Impressions, Engagement (Likes, Comments, Shares), Views, Follower Growth, Link Clicks, Ad Spend, CPM, etc.  
* **Manual Reports:**  
  * A report builder that allows for manual data entry, ideal for platforms without API access or for adding qualitative analysis.  
* **Hybrid Reports:**  
  * Admins can pull in data via the API and then edit or annotate it to add context and insights.

### **2.4. KPI Tracking**

A module for setting and tracking client-specific goals.

* **Goal Setting:** Define monthly or quarterly KPIs for each client (e.g., "Increase Instagram followers by 10%," "Achieve a 5% engagement rate on LinkedIn").  
* **Performance Visualization:** Charts and graphs that show progress towards these goals over time.

## **3\. Technical Architecture**

### **3.1. Frontend**

* **Technology:** A modern JavaScript framework like **React** or **Vue.js**. This will create a fast, interactive user experience for your clients.

### **3.2. Backend**

* **Technology:** A robust backend framework like **Node.js with Express** or **Python with Django**.  
* **Responsibilities:**  
  * User authentication and authorization.  
  * Handling API requests to and from social media platforms.  
  * Processing and storing data.  
  * Serving data to the frontend.

### **3.3. Database**

* **Technology:** A separate database from your WordPress site. A NoSQL database like **MongoDB** is highly recommended for its flexibility in handling diverse social media data structures. A relational database like **PostgreSQL** is also a viable option.  
* **Hosting:** A cloud-based database service (e.g., MongoDB Atlas, Amazon RDS) for scalability and easy management.

### **3.4. API Integrations**

* **Authentication:** Securely connect to client social media accounts using OAuth 2.0.  
* **Data Fetching:** Regularly fetch data from the APIs of Meta, YouTube, LinkedIn, and X.  
* **Publishing:** (Optional, future enhancement) Ability to publish approved content directly to the social media platforms.

### **3.5. Deployment Strategy**

* **Recommended Approach:** The application should be hosted on a dedicated subdomain of your main website (e.g., hub.esirom.com).  
* **Implementation on Siteground:**  
  1. A subdomain will be created using your Siteground control panel.  
  2. This subdomain will point to a new directory within your hosting account.  
  3. The custom-built application (frontend and backend) will be deployed to this directory.  
  4. This approach keeps the application completely separate from your main WordPress installation, ensuring better performance, security, and easier maintenance.  
* **Alternative (Not Recommended):** While it's technically possible to embed the application within a page on your existing WordPress site, this often leads to conflicts with themes and plugins, slower performance, and a more complicated development process.

## **4\. Data Model (High-Level)**

* **Users:** Stores user accounts (admins, brand reps, clients) and their roles.  
* **Clients:** Information about each client, their linked social media accounts, and their dashboard configuration.  
* **Posts:** All content calendar posts with their status, scheduled time, and performance data.  
* **Reports:** The generated reports, including both raw data and custom analysis.  
* **KPIs:** The key performance indicators for each client, their targets, and progress.

## **5\. User Roles and Permissions**

* **Admin:** Full access to the platform. Can manage clients, users, and system settings.  
* **Brand Representative:** Can manage the content calendar and reports for assigned clients.  
* **Client:** Can view their dashboard, reports, and content calendar. Can approve or reject posts.