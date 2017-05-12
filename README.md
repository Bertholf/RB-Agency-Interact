# RB Agency Interact
Allows profiles to self regsiter and manage their information.

### Current Version 2.4.4


## Change Log

### 2.4.4
* fixed - login not working for admin
* fixed - login redirection
* new - resume editor section on talent dashboard
* fixed - backslashes in custom fields registration

### 2.4.3
* new feature - admin's ability to customize email notification for model/talent registration
* fixed - gallery folder is not displaying on /profile-member/account/
* fixed - when user's email is updated via profile editor, the email is not updating in wordpress manage users.
* fixed - reset link is missing in email
* fixed - forgot password feature - user is redirected to /profile-login/ page but no notification if reset link is emailed to user.
* fixed - after admin approved casting agent, user is not getting an email notification.
* fixed - casting agents are duplicated when admin approves user
* new feature - added calendar select to the birthdate field in registration
* fixed - when someone registers as model/talent, the user status is set to inactive instead of pending approval which is set in the settings.
* fixed - when someone registers as model/talent, the user status is set to pending for approval but as soon as the user logs in to continue registration, the status becomes inactive.
* fixed - admin and users are not getting email notifications.

### 2.4.2
* created "RB Login Widget" widget which you can add to the sidebar. This widget will show the "Log Out" if user is logged in.
* fixed - blank page upon registration
* fixed - Login Settings > Redirect first time users - i set it to "Redirect to /profile-member/account/" but when I tried to login for the first time, I am still redirected to /profile-member/
* created a setting similar to models where site admin can redirect casting agents to another url. They want they clients to be * able to register but not view the /casting-dashboard/
* fixed - html codes in email notifications
* if "Default Profile Status" setting is "Change the status to "pending approval" whenever a profile is updated" - admin is sent an email notification when a users edited their profile

### 2.4.1
* fixed - Username Self-Generated Password Auto-Generated - username is being converted into random characters or letters e.g. rbagencytalent becomes demo_2c5rkin list Core Issues 
