# SteemNova 1.8x

SteemNova 1.8x is designed as a clean starting point for individuals interested in games like SteemNova or OGame. Inspired by the original SteemNova project, this version addresses various issues such as point calculations not working, name-changing features being broken, and other bugs, providing a stable and bug-free environment for development and experimentation.

This version has been updated to PHP 8.2 and Smarty 4.3, ensuring compatibility with modern systems.

## Installation

Follow these steps to set up the project locally:

1. **Clone the project**
   - Create a folder named `steemnova` on your computer.
   - Open a terminal, navigate to the folder using `cd`, and clone the repository using Git.

2. **Set up XAMPP**
   - Download and install [XAMPP](https://www.apachefriends.org/).
   - Open `httpd.conf` in the Apache configuration and set the document root to:
     ```
     C:/xampp/htdocs/steemnova/steemnova-1.8-x
     ```

3. **Create a database**
   - Open [localhost/phpmyadmin](http://localhost/phpmyadmin) in your browser.
   - Create a new database for the project.
   - If necessary, create a user with full privileges and assign a password.

4. **Enable the installer**
   - In the `includes` folder of the game directory, create a new file named `ENABLE_INSTALL_TOOL` (without any extension).
   - Open your browser and navigate to:
     ```
     http://localhost/steemnova/steemnova-1.8-x
     ```
     The installation process will start automatically.

## Notes

- This project aims to provide a clean and functional starting point.

- Ensure your server meets the PHP 8.2 requirements and has all necessary extensions enabled.

- This project has previously received contributions from various developers, so there is no consistent coding style throughout. The main purpose of the project is to unify content written or added in different languages under a common language (English) and to apply a consistent coding style and notation across the entire codebase.

- The project does not prioritize UI design and is developed only as needed to test backend improvements for the GoW theme.

- The primary focus is on cleaning the existing codebase from unnecessary dependencies, resolving existing bugs, and creating a more useful and advanced monitoring system / admin panel.

## References

- Original SteemNova project: [https://github.com/steemnova/steemnova](https://github.com/steemnova/steemnova)  
- 2Moons Forum: [https://2moons.de/](https://2moons.de/)