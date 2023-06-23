# student_records
with a SQL database included.

> This repository contains a system for managing student accounts and recording student performance. It provides various functionalities for users with appropriate access, such as adding or editing student accounts and records.

# User Roles
*	Professor (admin)
*	Student

# Features
1.	Logout: This feature allows the user to log out of the system and end the current session.
2.	Welcome, Professor!: Upon logging into the system as a professor, a welcome message is displayed.
3.	Add Student Account: This feature enables the professor to add a new student account. The professor needs to provide the student's number and password to create the account.
4.	Edit Student Account: The professor can use this feature to modify an existing student account. The student's number, old password, and new password are required to make the changes.
5.	Edit Your Account: This functionality allows the professor to update their own account details, such as the username and password. The professor needs to provide the old password and specify the new password.
6.	Add Record: The professor can add a new performance record for a student. This includes providing the student number, type of assessment (e.g., exam, quiz, activity, etc.), score, grade, and date taken.
7.	All Records: This feature displays all existing student records in a tabular format. Each record includes the student number, reference number, type of assessment, score, grade, date taken, and an action option to edit the record.

# Usage

- Professor Access:
  > 1.	Login: Use the provided professor/admin login credentials to access your account. Enter your username and password in the respective fields.
  > 2.	Add Student Account: Proceed to the "Add Student Account" section and enter the student's number and password in the provided fields.
  > 3.	Edit Student Account: Proceed to the "Edit Student Account" section and enter the student's number, old password, and new password in the appropriate fields.
  > 4.	Edit Your Account: Proceed to the "Edit Your Account" section and enter the username (currently set as "prof"), old password, and new password in the corresponding fields.
  > 5.	Add Record: Proceed to the "Add Record" section and enter the student number, type of assessment, score, grade, and date taken in the respective fields.
  > 6.	All Records: To view all student records, navigate to the "All Records" section. The table displays the student number, reference number, type, score, grade, date taken, and an option to edit each record.

- Student Access:
  > 1.	Login: Use the provided student login credentials to access your account. Enter your student number and password in the respective fields.
  > 2.	View Records: Once logged in as a student, you will be greeted with a welcome message. Your performance records will be displayed, showing the type of assessment, grade, and date taken.

# Login Credentials
- Professor:
  > Username: prof ;
  > Password: prof123

- Students:
  > - Student Number: 20201234 ;
  > Password: student 
  >	- Student Number: 20202222 ;
  > Password: student
  >	- Student Number: 20203333 ;
  > Password: student

Please note that these login credentials are provided as examples and may not represent real accounts. The system already contains pre-existing student accounts and records for demonstration purposes.

# Disclaimer
This system is a demonstration and should not be used in a production environment. It is recommended to enhance security measures, such as implementing proper user authentication and data validation, before deploying it for real-world usage.
For any issues or questions regarding the system, please contact the system administrator or the developer.
