# Online Bookstore: A PHP-based Web Application

## Introduction
The aim of this project was to create a user-friendly and intuitive web application that specializes in selling books online. With the decline of interest in reading, especially among younger generations, the goal was to encourage people to read by offering a platform where they can browse and purchase books easily.

## Features
The web application provides the following features for different types of users:
- **Guest users** can freely browse books, add them to the cart, and place orders to a specified shipping address.
- **Registered users** can perform the same actions as guest users, as well as rate and comment on books, save multiple shipping addresses for faster checkout, and receive personalized book recommendations based on their preferences.
- **Moderators** have the ability to review comments and delete inappropriate ones.
- **Admins** have access to real-time sales statistics, and can add, modify, and delete books from the database.

## Requirements
To use the web application, you need a device with an internet connection and a web browser. The application is implemented using PHP, HTML, CSS, and JavaScript, and requires a server with a web stack (e.g. Apache, MySQL, PHP) installed.

## Installation
To install the application, follow these steps:
1. Clone the repository to your local machine.
2. Install a web server and a database server on your machine, if not already installed.
3. Create a new database and import the sample data from the **'bookstore.sql'** file in the *database* folder.
4. Configure the **'env.php'** file with your database credentials in the *page/includes* folder.
5. Open the **'index.php'** file in your web browser and start using the application.
Warning! The *page/covers* folder is not uploaded, so the images to the books **will not** load.

## Credits
This project was developed by Jazehin (Bence Kiss). Special thanks to my consulent teachers for their valuable feedback and support.

## License
This project is licensed under the [Do What the Fuck You Want to Public License](http://www.wtfpl.net/).