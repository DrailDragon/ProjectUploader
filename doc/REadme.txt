1. Download and install XAAMP. 
   http://www.apachefriends.org/en/xampp.html
   The details regarding the installation of XAAMP is given in XAAMP.pdf

2. Download the application from 
      http://172.16.26.213/ProjectUploader.zip
   Unzip to get the application "ProjectUploader".

3. Download all the additional files from 
       http://172.16.26.213/doc.zip
   Unzip to get the folder "doc", It contains:
   readme.txt ==> The file you are reading now.
   XAAMP.pdf ==> How to install XAMMP.
   ProjectUploader.sql ==> THe SQL script to generate the data base through phpMyAdmin.
   Uoplad ==> Folder to store the thesis.

4. Read XAAMP.pdf to understand about how to change the MySQL database password.
   Set the MySQL database username as: root
   and username as: project

5. Copy paste the unzipped "ProjectUploader" application into the root folder of local host.
   For example, if you installed XAMMP in "C:/XAAMP" then the root folder is "C:/XAAMP/htdocs/"

6. Copy paste the folder "Upload" into the parent directory of root. 
   For example, if the root is "C:/XAAMP/htdocs/" then place Upload in "C:/XAAMP/".


7. Create a database through phpMyAdmin with name "project".

8. Click on the database "project". Now click on "import" and load the SQL script file "ProjectUploader.sql".
   The complete database will be created automatically.

9. The project is ready. If you installed it properly, you can run it from the link
   http://localhost/ProjectUploader/
    
****Note that the project will install with some previous information stored in database. The ProjectUploader.sql will create the existing database. 
    You have to add your own records into the database.

    If you have any problem regarding the project then feel free to come and meet us (Mayank or Shirshendu) in RS lab. 

    File (thesis) uploading may create some problem. If you face any issue regarding pdf upload via the application then contact us.
    
