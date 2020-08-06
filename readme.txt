1. Setup Database
- After unpacking folder and putting it into /htdocs in the Xampp folder, set the database up.

- Navigate to Assignment2/Config/db_setup.php, if run successfully, the database will be set up.

- When running db_setup it overwrites the current database and its values, so be careful navigating there again.

- After database is setup, the tables: users, topics and entries should be set up.

- The topics and entries tables contains some placeholder/filler values, as to have something to immediately show, when navigating to the index page.

- In the users table, there are created 2 users: A regular user or "author" called test and an admin user called admin.

- The test user is expendable, as to say, the test user is not important, as a new "author" can be made in a matter of seconds on the register page.

- The admin user on the other hand is very important, as it is the one and only user with admin privileges in the application.

- As the assignment did not specify that criteria, the registering of admins has not been added and therefore, the admin user made on database setup, is the only admin user there can and will be.

- Therefore, be careful about handling the admin, it's not possible to delete the user through the application interface, but be careful nonetheless.

2. User privileges
2.1 Visitor
- The visitor's only privileges are accessing the index page/home page of the application, the login page and the register page.

- It cannot add any new entries or topics, but it can view all the topics and entries that exist.

- It can do so either through the list on the right, which can also be sorted by clicking the sorting buttons on the top left of the page. The list can either be sorted 
by chronological order (topic creation date) or by popularity (number of entries per topic). Clicking any of the list items (topics) will display them in the element box to the
right side of it. The title of the topic will be shown, as well as the creation date and the creator. All of the topic's entries will be shown underneath, also displaying
creation date and its creator.

- The second way to view entries and topics is by viewing the right side of the index page after a refresh. At default, a new topic and its entries will be randomly shown in the right element,
whenever the page is refreshed.

- The third way is through the search function. The search results can either be topics or entries, as long as they match the search criteria. When finished typing the search text
/string, press the "Enter" key and the search will be executed. Every search result will then display all entries and topics matched, and you can tell the type of the result,
by either the ENTRY or TOPIC tag underneath the title/text. In the right side of an element one will se a link, which will send you to the topic listed, or the topic of the entry listed.

- A visitor cannot log in before it has registered. The register page has several validation criteria needed to pass before it can be submitted. After a visitor has registered as a user/author,
it can then log in.

2.2 User
- The user privileges, in addition to the ones granted to the visitor, are access to the profile page (shown in the top right when logged in) and its functionalities.

- On the user profile page one can first and foremost, create new topics and entries. A topic is created by opening the topic form, writing a topic title in the text field
and then click on the create topic button. An entry is created by first opening the entry creation form, then select the topic to write an entry into, and then write a entry in the
text field, followed by clicking the create entry button.

- Under the creation forms, one can view some generic user information.

- The possibility of editing one's username and password is located in the form, under the user information. Users can edit both their usernames and passwords, while admins
can only edit their password. Editing your username will refresh the page and editing your password will log you out and redirect you to the login page.

- Under the edit user information form, are the topics and entries created by the current user. A user who has created topics or written entries, are able to delete
its own entries or topics. 

2.3 admin
- The admin privileges, in addition to the ones granted to the visitor and the user, are access to the admin page (shown in the top right when logged in as an admin)
and its functionalities.

- The admin page gives an overview of all users, topic and entries. An admin can see all users, their userID, their username and their user type and has the ability 
to delete them(excluding the admin itself). An admin can see all topics, its topicID, the topic itself(topicname), who the topic was created by and the number of entries that
topic has, and can also delete them. An admin can see all entries, its entryID, the entry description (the entry), who the entry was written by and its creation date, and also
has the possibility to delete entries.

- Be careful when deleting eiter users, topics or entries as they are not retrieveable. In addition, deleting a user will also delete their created topics and written
entries. Furthermore, deleting a topic will also delete all of its entries. 

3 Notes
- I'm quite happy with the result and the assignment, as it helped me learn a lot of new things.

- This assignment has been worked on for some time and during all that time, i've been constantly learning and trying new things. As a result of that,
queries, variables, functions and so on, may look different and differ from page to page. Some pages were given more priority over others and the result MAY show that.

