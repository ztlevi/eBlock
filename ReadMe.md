# 566 Term Project - E-Guard ReadMe
Authur: Ting Zhou

## Software and Libiaries

1. MAMP or XAMPP is used as Apache server and Mysql DB setup.
2. Chrome.tabs is the main Chrome API I used to control the chrome's page. Everytime user access to a new page, the extension's background.js will check if the  URL is allowed.

## Installation Steps:

1. Create a DB in Phpmyadmin called e-guard. Import the *DB.sql* from the directory *'{server root directory}/E-Guard/E-Guard-Database'*
2. Load the extension directory "./E-Guard/E-Guard-Client" from the Chrome Extension Page.
3. You can click the E-Guard Extension -> Options to get to the configuration page.
4. You can uninstalll it by click the uninstall icon from the Chrome Extension page.
