Final Project CF7 "Notes App".


**System Analysis**
1.1 Web Application Description
The "Notes App" provides the following online services:

User Registration: The ability to create an account is provided through a registration form with a username and password. This data is stored in the "users" table in MySQL.

User Login: The user logs in to access their notes. Their identity is verified via an SQL query: ($sql = "SELECT id, password_hash FROM users WHERE username =?";).

Notes Management: 
The user can:

Create new notes
Update existing notes
Delete notes

1.1.1 Web Application Architecture
The system architecture is based on the following three-tier model:

Presentation Layer (Web Server – Apache)
The Web Server (Apache via XAMPP) is responsible for processing requests from the user (HTTP requests) and sending HTML, CSS, and JavaScript responses to the browser. This layer acts as an interface between the user and the application.

Logic Layer (Application Server – PHP)
The application's logic is implemented via PHP. This is the layer that handles the business logic, such as data processing, rules, and procedures. The Application Server communicates with the Database Server for data retrieval or storage.

Data Layer (Database Server – MySQL)
The application's data is stored in a MySQL database. The Database Server is responsible for storing, retrieving, and managing the data.

The technologies on which the above web application was implemented are the following:

Web Server: The web application is hosted on Apache (via XAMPP).

Application Server: The web application is implemented with PHP logic.

Database Server: The application data is stored in MySQL.

Operating System: The XAMPP server is hosted on Windows.

The database (appnotes_db) in MySQL is shown below.

CREATE DATABASE appnotes_db;

USE appnotes_db;

CREATE TABLE users (
 id INT AUTO_INCREMENT PRIMARY KEY,
 username VARCHAR(50) UNIQUE NOT NULL,
 password_hash VARCHAR(255) NOT NULL,
 created_At DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE notes(
 id INT AUTO_INTCREMENT PRIMARY KEY,
 user_id INT NOT NULL,
 title VARCHAR(100) NOT NULL,
 content TEXT NOT NULL,
 created_at DATETIMEDEFAULT CURRENT_TIMESTAMP,
 updated_at DATETIME ON UPDATE CURRENT_TIMESTAMP,
 FOREIGN KEY (user_id) REFERENCES users(id)
);

More specifically: we create the "appnotes_db" database and select it with the USE command. We create a table named "users" which will contain user information. The columns of the "users" table are as follows:

id INT AUTO_INCREMENT PRIMARY KEY: The id column is of type INT (integer). It is the primary key (PRIMARY KEY) of the table, which means that each record will have a unique value. The value is automatically incremented (AUTO_INCREMENT) for each new record.

username VARCHAR (50) UNIQUE NOT NULL: The username column stores the username as an alphanumeric value (maximum length 50 characters). It is UNIQUE, which means that two users cannot have the same name. It is NOT NULL, which means it cannot be empty.

password_hash VARCHAR (255) NOT NULL: Stores the user's encrypted password as an alphanumeric value (maximum length 255 characters). It is NOT NULL, i.e. it is mandatory.

created_at DATETIME DEFAULT CURRENT_TIMESTAMP: Stores the date and time the user was created. DEFAULT CURRENT_TIMESTAMP means that when a new record is created, the current date and time will be automatically saved.

Additionally, we create a table named "notes", which contains the users' notes. The columns of the "notes" table are as follows:

id INT AUTO_INCREMENT PRIMARY KEY: The id column is of type INT (integer). It is the primary key (PRIMARY KEY) of the table and is automatically incremented for each new record.

user_id INT NOT NULL: Connects each note to a user via the id from the "users" table. It is NOT NULL, i.e. it is mandatory.

title VARCHAR(100) NOT NULL: Stores the title of the note as an alphanumeric value (maximum length 100 characters). It is NOT NULL, i.e. it is mandatory.

content TEXT NOT NULL: Stores the content of the note. TEXT is a data type used for long texts. It is NOT NULL, i.e. it is mandatory.

created_at DATETIME DEFAULT CURRENT_TIMESTAMP: Stores the date and time the note was created. DEFAULT CURRENT_TIMESTAMP automatically adds the current date and time when a new note is created.

updated_at DATETIME ON UPDATE CURRENT_TIMESTAMP: Stores the date and time of the last update of the note. ON UPDATE CURRENT_TIMESTAMP automatically updates the value every time a change is made to the note.

FOREIGN KEY (user_id) REFERENCES users(id): Defines that the user_id column is a foreign key that is connected to the id column of the users table. This means that each note must correspond to a user from the users table.

1.1.2 Asset Model Creation
1st Computing System: Web Server

Hardware: Local computer
Software:
Operating System: Windows
Web Server: Apache (via XAMPP)

Network: Local network
Data: Apache Settings and Logs

2nd Computing System: Application Server

Hardware: Local computer
Software: PHP (via XAMPP)
Network: Local network
Data: Web application code (PHP and HTML files)

3rd Computing System: Database Server
Hardware: Local computer
Software: MySQL (via XAMPP)

Data:
User data: usernames, hashed passwords
Note data: note titles and content

1.1.3 Mapping Services and Computing Systems

Service
User Registration	Web Server
User Login	Web Server
Notes Management	Web Server

Computing Systems
Database Server

**SSL Encryption**
SSL (Secure Sockets Layer) encryption ensures the secure transfer of data between the user and the browser. In our web application, we created a self-signed certificate using OpenSSL in XAMPP.
In the C:\xampp\apache\bin\ folder, we use the command:
"openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout localhost-key.pem -out localhost.pem -config "C:\xampp\apache\conf\openssl.cnf""

openssl req: Command for creating certificate requests.

-x509: Indicates that we want to create a self-signed X.509 certificate. X.509 is the standard for digital certificates used in SSL/TLS.

-nodes: (no DES encryption). Prevents the private key from being encrypted with a password, making it easier to use the private key without requiring a password each time.

-days 365: Sets the certificate's validity period to 365 days from the date of creation.

-newkey rsa:2048: Creates a new RSA private key with a length of 2048 bits. RSA is a public-key encryption algorithm, and the key length (2048 bits) is critical for security.

-keyout localhost-key.pem: Specifies the name of the file where the private key will be stored. In your case, this is localhost-key.pem.

-out localhost.pem: Specifies the name of the file where the self-signed certificate will be stored. In your case, this is localhost.pem.

-config "C:\xampp\apache\conf\openssl.cnf": Indicates the Openssl configuration file to be used. This file contains predefined settings and parameters that facilitate certificate creation (e.g., information such as country name, organization, etc.).

We filled in the requested information, setting the Country Name to GR, the Organization Name to Unipi, and the Common Name to localhost. Then, two files are created: localhost-key.pem (private key) and localhost.pem (certificate), which we moved to the C:\xampp\apache\conf\ssl folder. We then configured Apache, and specifically in the httpd-ssl.conf file, we set the SSLCertificateFile and SSLCertificateKeyFile with the appropriate paths. We enabled the SSL Module and restarted Apache from the XAMPP Control Panel.

**Authentication Mechanism**
By "authentication," we mean that only authorized users have access to their accounts. In our web application, we implement this mechanism as follows:
The user provides their credentials (username and password).
The password is compared to the stored hash in the database (appnotes_db).
The technology used is:
Hashing with password_hash and password_verify. Specifically:
During registration, the user's password is saved in the database as a hash (password_hash).
During login, password_verify is used to compare the password the user provided with the hash.

**Access Control Mechanism**
With "access control," we ensure that users only have access to data that concerns them. In our web application, we implement this mechanism as follows:
The user's identity is maintained through sessions. When the user logs in successfully, a session is created that contains information about the user. Specifically, it includes the user_id, which represents the user's unique identifier from the database.
The "protected" page checks if there is an active session and if it contains valid information, such as the user_id. If the session does not exist or is empty, the user is redirected to the login page.
The "protected" page is the one that requires access control and is related to the user's personal data. In our web application, this is:
The Notes Page, where users can view, edit, and create only their own notes. The page checks if the user is logged in via the session before loading the data.
If the user logs out, the session is destroyed, and all pages become inaccessible. This is achieved with the session_destroy command.
In short, the access control mechanism protects user data, as everyone has access only to their own notes. A non-logged-in user does not have access to the notes page, as access is protected via sessions. If they try to connect to this page, they are redirected to the login page.

Input filtering and validation ensure that user data is safe and valid before being stored or used. Specifically, input filtering uses filter_input, and via the POST method, it checks for any dangerous data. In addition, the page is protected from SQL Injection attacks with the "prepared statements" and "bind parameters" commands. All data displayed on the website is passed through htmlspecialchars to avoid XSS (Cross-Site Scripting) attacks.


**Installation Instructions**
The web application was implemented using PHP and HTML.

After installing Visual Studio Code, open the .txt files located in the Codes folder by going to File > Open File.

To develop the application in a local environment, install XAMPP. XAMPP is a free and open-source software that includes Apache, MySQL, and PHP.

XAMPP: Install XAMPP for Windows (PHP 8.2.12).

In the XAMPP Control Panel, start the Apache and MySQL modules.

In your browser, type: http://localhost/phpmyadmin/.

In phpMyAdmin, create the database and tables so the application's data is stored in MySQL.

In the Databases section, create your database with the name appnotes_db.

In the SQL section, copy and paste the content of the appnotes_db.txt file, which is located in the Codes folder.

This will create a database (appnotes_db) with two tables (users and notes).

In your browser, type: http://localhost/appnotes/index.php.

Here, you will see your website (in HTTP format).

Installing SSL Certificate for HTTPS
To convert your website to function via HTTPS, you need to create a self-signed SSL certificate. XAMPP includes Apache, which supports HTTPS. Here are the detailed steps:

Go to the folder: C:\xampp\apache\conf\ and check if the file named openssl.cnf exists.

Open the command prompt (cmd) and navigate to the XAMPP installation folder.

cd C:\xampp\apache\bin

Create the SSL certificate with the command: openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout localhost-key.pem -out localhost.pem -config "C:\xampp\apache\conf\openssl.cnf"

Fill in the requested information, entering GR for Country Name, Unipi for Organization Name, and localhost for Common Name.

Two files will be created: localhost-key.pem (private key) and localhost.pem (certificate).

Create a new folder named ssl in C:\xampp\apache\conf\ and save the private key and certificate there.

Open the httpd-ssl.conf file, located at C:\xampp\apache\conf\extra\httpd-ssl.conf. Configure the VirtualHost (locate the sections and fill them in). Save with Ctrl + S.

Go to C:\xampp\apache\conf\httpd.conf. Find the following lines and remove the # symbol. Save with Ctrl + S:

#LoadModule ssl_module modules/mod_ssl.so

#Include conf/extra/httpd-ssl.conf

Open the XAMPP Control Panel, stop, and then start Apache again.

In your browser, type: https://localhost/appnotes/index.php.

Click Advanced > Proceed to localhost.

You will now see your website (in HTTPS format).
