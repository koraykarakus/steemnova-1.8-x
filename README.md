# SteemNova 1.8x - 2 Moons engine based browser game for steemnova community

Main Objective of the project is to fix bugs related to steemnova v1.8

What is done so far ?

* BBCodeParser2 was causing problems at the stage of install, wrong url links of BBCodeParser2 is corrected

* BBCodeParser2 problems on Tickets page corrected, tickets working efficiently

* Login system changed, now users login with email instead of username

* Recaptcha is updated to new version

* Login and register systems now uses recaptcha if captcha is activated from admin panel

* Register links on Index page corrected

* Some visual corrections for register page

* Manual stats page on admin panel was not reporting stats update time for languages Tr and Fr, these language files corrected

* new javascript added for login and register pages to avoid form submit on page refresh

* steem connect removed from admin login page

* includes/pages/adm converted from mysqli (old database) to new database ( PDO )

* bug fix: ShowAccountEditorPage - removing buildings from planets, avoid negative level for buildings

* bug fix: ShowAccountEditorPage - removing buildings from planets, avoid negative level for planet fields

* bug fix: ShowAccountEditorPage - removing ships from planets, avoid negative number of ships

* bug fix: ShowAccountEditorPage - removing defence units from planets, avoid negative number of defences units

* bug fix: ShowAccountEditorPage - removing research levels from planets, avoid negative number of research

* bug fix: ShowAccountEditorPage - removing resources from planets, avoid negative number of resources

* bug fix: ShowAccountEditorPage - removing darkmatter from users, avoid negative number of dark matter

* Added an option to activate and deactivate recaptcha for login and register pages separately

* Added an ability to chose current theme from admin panel , nova, gow, epic blue, DEFAULT_THEME constant removed, theme class changed

* A bug in alliance page is fixed caused by BBCode

* Added a search bar for admin panel

* bugs caused by page.error.default.tpl fixed

* Theme update: Epic Blue XIII buildings page is updated and visual problems solved

* Theme update: Epic Blue XIII research page is updated and visual problems solved

* Theme update: Epic Blue XIII shipyard page is updated and visual problems solved


**Code copyright 07.05.2020-2020 @IntinteDAO released under the AGPLv3 License.**
