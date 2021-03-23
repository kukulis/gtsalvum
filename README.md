#Install instructions

#### Clone project to your disk
    git clone git@github.com:kukulis/gtsalvum.git gtsalvum
    
#### Install libraries
    cd gtsalvum
    composer install
  

#### Connect to database
    sudo mysql
    create database gts;
    grant all on gts.* to gts identified by 'gts';
    flush privileges;
    exit
    
    cp .env.example .env
    vim .env
    
Modify these lines:

    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=laravel
    DB_USERNAME=root
    DB_PASSWORD=

To

    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=gts
    DB_USERNAME=gts
    DB_PASSWORD=gts
    
##### Create database structure

    php artisan migrate
    
##### Fill database with initial data
   
    php artisan db:seed
    
##### Start local webserver
    
    php artisan serve
    

# API endpoints

##### Login

    GET http://localhost:8000/api/auth/login?email=admin@test.com&password=toptal

Other user login may look at db table  "users". The seeded passwords are all the same: 'toptal'.   

For example:

    GET http://localhost:8000/api/auth/login?email=una.torp@schmidt.biz&password=toptal

The result should look like:

    {
      "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTYxNjQ3MDYzNCwiZXhwIjoxNjE2NDc0MjM0LCJuYmYiOjE2MTY0NzA2MzQsImp0aSI6Ikc3dFFtVGYxZGlyTExKaEsiLCJzdWIiOjIsInBydiI6Ijg3ZTBhZjFlZjlmZDE1ODEyZmRlYzk3MTUzYTE0ZTBiMDQ3NTQ2YWEifQ.2ipkvW1sj0vXqXwjTw1puFHl8wzY-ReZ7y7NULbKFj0",
      "token_type": "bearer",
      "expires_in": 3600
    }
    
For all following endpoints you must set Header:
 
    Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTYxNjQ3MDYzNCwiZXhwIjoxNjE2NDc0MjM0LCJuYmYiOjE2MTY0NzA2MzQsImp0aSI6Ikc3dFFtVGYxZGlyTExKaEsiLCJzdWIiOjIsInBydiI6Ijg3ZTBhZjFlZjlmZDE1ODEyZmRlYzk3MTUzYTE0ZTBiMDQ3NTQ2YWEifQ.2ipkvW1sj0vXqXwjTw1puFHl8wzY-ReZ7y7NULbKFj0
    
The string after *Bearer* must be equal to the "access_token" value, received by login response.

##### Tasks list

    GET http://localhost:8000/api/tasks
    
##### View task 
    
    GET http://localhost:8000/api/task/show/1
    
##### Create new task

    PUT http://localhost:8000/api/task/create?Name=My%20task2&Description=Eni%20beni%20diki%20daki&Type=advanced&Status=hold 

##### Update task

###### Attach user to task
    
    POST http://localhost:8000/api/task/update/1?AttachUserId=2
    
###### Detach user from task
    
    POST http://localhost:8000/api/task/update/1?DetachUserId=2
    
###### Edit task fields

    POST http://localhost:8000/api/task/update/1?Name=Modified%20name&Description=Modified%20description&Type=basic&Status=todo
    
###### Close task

    POST http://localhost:8000/api/task/update/1?Status=closed 
    
##### Messages list

    GET http://localhost:8000/api/messages?offset=10&limit=15     

"Offset" and "limit" are paging implementation parameters.

##### Create new message

    PUT http://localhost:8000/api/message/create?Subject=Message%201&Message=First%20message.%20A.%20b.c.&task_id=1
    
Note, that a field "task_id" is a required field.

##### Update message    

    POST http://localhost:8000/api/message/update/1?Subject=updated%20message%20title&Message%20=updated%20message%20body
    
##### View message

    GET http://localhost:8000/api/message/view/1
    
##### View message log

    GET http://localhost:8000/api/message/viewlog/4  
    
    
# User stories

##### UC1.1:	As a user I expect to be able to login with my email address and password

Login with user:

    GET http://localhost:8000/api/auth/login?email=admin@test.com&password=toptal
    
Login with another user:
    
    GET http://localhost:8000/api/auth/login?email=user2@test.com&password=toptal
    
    
In both request you should receive different "access_token" value.

##### US2.1: 	As a logged in user I expect to be able to create a task

    PUT http://localhost:8000/api/task/create?Name=My%20task2&Description=Eni%20beni%20diki%20daki&Type=advanced&Status=todo
    
##### US2.2: As a logged in user I expect to be able to update a task that I am the owner of
    
--- Note that with a current seeder, tasks with ids: 1, 2, 3, 4, 5 belong to user admin@test.com ---

    
Lets try to update task with a access_token of user user2@test.com
    
    POST http://localhost:8000/api/task/update/1?Name=Modified%20name&Description=Modified%20description&Type=basic&Status=todo
    
You should receive a response with a http code 400:
    
    {
      "Error": "You are not allowed to update task 1",
      "Messages": []
    }    
    
Now dow the same with user admin@test.com access_token

    POST http://localhost:8000/api/task/update/1?Name=Modified%20name&Description=Modified%20description&Type=basic&Status=todo    
    
You should receive a response with http code 200:
    
    1
    
if updated field values are not valid:
    
    POST http://localhost:8000/api/task/update/2?Type=ezpert
    
You will receive a response:
        
    {
      "Error": "Invalid task data",
      "Messages": [
        "Task type should be one of [basic, advanced, expert]"
      ]
    }
  
##### US2.3: 	As a logged in user I expect to be able to view a task that I am attached to or I am the owner of with all its properties and relations

Lets try to view task with id 2 with user user2@test.com access_token:

    GET http://localhost:8000/api/task/show/2
    

You will receive a response with http code 400:

    {
      "Error": "You are not allowed to view task 2",
      "Messages": []
    }

Now do the same with user admin@test.com access_token:

    GET http://localhost:8000/api/task/show/2
    

You will receive a response with http code 200:

    {
      "data": {
        "id": 2,
        "Name": "Abagail Turner",
        "Description": "Aut aut qui qui. Maiores amet tenetur saepe omnis.",
        "Type": "basic",
        "Status": "closed",
        "Created": "2021-03-23T09:20:35.000000Z",
        "Updated": "2021-03-23T09:20:35.000000Z",
        "users": {
          "data": []
        }
      }
    }
 
Now, lets assign user user2@test.com (id 2) to the task with id 2, using user's admin@test.com access_token.
    
    POST http://localhost:8000/api/task/update/2?AttachUserId=2
    
Should receive a response with http code 200:
    
    1
    
Now lets view the same task with user user2@test.com access_token:
    
    GET http://localhost:8000/api/task/show/2
    
Now you should receive a response with http code 200:

    {
      "data": {
        "id": 2,
        "Name": "Abagail Turner",
        "Description": "Aut aut qui qui. Maiores amet tenetur saepe omnis.",
        "Type": "basic",
        "Status": "closed",
        "Created": "2021-03-23T09:20:35.000000Z",
        "Updated": "2021-03-23T09:45:30.000000Z",
        "users": {
          "data": [
            {
              "id": 2,
              "Name": "User2",
              "Email": "user2@test.com"
            }
          ]
        }
      }
    }
 
    
##### US2.4: 	As a logged in user I expect to be able to delete a task that I am the owner of

Again if you try to delete task 1 with user user2@test.com access_token:

    DELETE http://localhost:8000/api/task/delete/1

You will receive a response with code 400:

    {
      "Error": "You are not allowed to delete task 1",
      "Messages": []
    }
    
If you do the same with user admin@test.com access_token:

    DELETE http://localhost:8000/api/task/delete/1

You will receive a response with code 200:

    1
    
##### US2.5:	As a logged in user I expect to be able to attach users to a task when creating or updating

Attaching when updating:

    POST http://localhost:8000/api/task/update/2?AttachUserId=2
    
This was described at US2.3


Attaching when creating, lets do that with user admin@test.com access_token:

    PUT http://localhost:8000/api/task/create?Name=My%20task3&Description=Eni%20beni%20diki%20daki&Type=advanced&Status=hold&AttachUserId=2
    
You should receive the created task id with a http code 200:
    
    51
    
Now lets try to view a task with user user2@test.com access_token:

    GET http://localhost:8000/api/task/show/51
    
You should receive a response:

    {
      "data": {
        "id": 51,
        "Name": "My task3",
        "Description": "Eni beni diki daki",
        "Type": "advanced",
        "Status": "hold",
        "Created": "2021-03-23T09:59:29.000000Z",
        "Updated": "2021-03-23T09:59:29.000000Z",
        "users": {
          "data": [
            {
              "id": 2,
              "Name": "User2",
              "Email": "user2@test.com"
            }
          ]
        }
      }
    }
    
##### US2.6:	As a logged in user I expect to be able to close (status = ‘closed’) a task if it is attached to me or I am the owner of

This is done with the "task update" endpoint, but if you are not an owner, then only parameter status='closed' is accepted.

Lets try to close task with user user2@test.com access token:
    
    POST http://localhost:8000/api/task/update/2?Status=closed
    
You should receive http 200 response:
    
    1
    
But if you try to use other status:
   
    POST http://localhost:8000/api/task/update/2?Status=todo
    
Or modify other fields:
        
    POST http://localhost:8000/api/task/update/2?Type=expert
    
You will receive http 400 response:

    {
      "Error": "You are not allowed to update task 2",
      "Messages": []
    }
 
##### US2.7:	As a logged in user I expect to see a paginated list of all tasks that are attached to me or I am the owner of

Lets request tasks list with a user user2@test.com access_token:


    GET http://localhost:8000/api/tasks

I received a response when testing:

    {
      "data": [
        {
          "id": 6,
          "Name": "Lea Lebsack",
          "Description": "Qui dolor sint sit est id est. Alias quia et reiciendis quae qui.",
          "Type": "advanced",
          "Status": "hold",
          "Created": "2021-03-23T09:20:35.000000Z",
          "Updated": "2021-03-23T09:20:35.000000Z"
        },
        {
          "id": 7,
          "Name": "Shanel Romaguera",
          "Description": "Vero deserunt eos fugit dolores. Quasi doloribus quidem dicta ex consectetur.",
          "Type": "expert",
          "Status": "hold",
          "Created": "2021-03-23T09:20:35.000000Z",
          "Updated": "2021-03-23T09:20:35.000000Z"
        },
        {
          "id": 8,
          "Name": "Gaetano Harber",
          "Description": "Et sit eos aut quia ut eaque quasi. Qui id dicta quia cupiditate. Voluptas fuga qui labore soluta.",
          "Type": "advanced",
          "Status": "closed",
          "Created": "2021-03-23T09:20:35.000000Z",
          "Updated": "2021-03-23T09:20:35.000000Z"
        },
        {
          "id": 9,
          "Name": "Salvatore Orn",
          "Description": "Quasi earum nostrum aut enim. Reiciendis magni soluta alias ut. Error fuga vero quos optio.",
          "Type": "advanced",
          "Status": "hold",
          "Created": "2021-03-23T09:20:35.000000Z",
          "Updated": "2021-03-23T09:20:35.000000Z"
        },
        {
          "id": 10,
          "Name": "Dr. Kianna Quigley",
          "Description": "Eos magnam molestiae ut cupiditate rem rem. Enim consequatur hic rem dolores a pariatur quia minus.",
          "Type": "expert",
          "Status": "todo",
          "Created": "2021-03-23T09:20:35.000000Z",
          "Updated": "2021-03-23T09:20:35.000000Z"
        },
        {
          "id": 2,
          "Name": "Abagail Turner",
          "Description": "Aut aut qui qui. Maiores amet tenetur saepe omnis.",
          "Type": "basic",
          "Status": "closed",
          "Created": "2021-03-23T09:20:35.000000Z",
          "Updated": "2021-03-23T10:22:15.000000Z"
        },
        {
          "id": 51,
          "Name": "My task3",
          "Description": "Eni beni diki daki",
          "Type": "advanced",
          "Status": "hold",
          "Created": "2021-03-23T09:59:29.000000Z",
          "Updated": "2021-03-23T09:59:29.000000Z"
        }
      ]
    }
    
    
As you see there is a task with id 2, which belongs to admin@test.com ( by the initial db seeding ),
but becuse we attached this task to the user user2@test.com ( id=2 ),
we now see the task in the list.


##### US3.1:	As a logged in user I expect to be able to create a message on tasks I’m attached to or I am the owner of

Lets create message with user user2@test.com access_token, to the task id=2: 

    PUT http://localhost:8000/api/message/create?Subject=Message%201%20test&Message=First%20message.%20A.%20b.c.&task_id=2

In response we get with http 200:
    
    501
    
This is a created message id.

If we try  to create a message to the task, to which user is not assigned , of example task_id=3. 

    PUT http://localhost:8000/api/message/create?Subject=Message%201%20test&Message=First%20message.%20A.%20b.c.&task_id=3

We receive a response with http code 400:

    {
      "Error": "You are not allowed to create message for task [3]",
      "Messages": []
    } 

##### US3.2:	As a logged in user I expect to be able to update a message if I am the owner of the message
 
With a user user2@test.com access_token lets try to update message id = 501

    POST http://localhost:8000/api/message/update/501?Subject=updated%20message%20title&Message%20=updated%20message%20body

We will receive a http 200 response:
    
    1
    
##### US3.3: As a logged in user I expect to be able to view a message of tasks I am attached to or I am the owner of
and
##### US3.6: As a logged in user I expect that when I view a message a log is created that I viewed the message including a timestamp of when I viewed it


Lets try to get view message id=501 with owner -  user2@test.com access_token:

    GET http://localhost:8000/api/message/view/501
    
You will receive with http 200:

    {
      "data": {
        "id": 501,
        "Subject": "updated message title",
        "Message": "First message. A. b.c.",
        "task_id": 2,
        "Created": "2021-03-23T11:17:50.000000Z",
        "Updated": "2021-03-23T11:22:58.000000Z",
        "ViewDate": "2021-03-23T11:25:46.000000Z"
      }
    }
    
Note, that there is a field ViewDate which is updated at the first moment when you make request api/message/view/501

If you try to access the message with user admin@test.com token:

    GET http://localhost:8000/api/message/view/501
     
You will receive http 200:

    {
      "data": {
        "id": 501,
        "Subject": "updated message title",
        "Message": "First message. A. b.c.",
        "task_id": 2,
        "Created": "2021-03-23T11:17:50.000000Z",
        "Updated": "2021-03-23T11:22:58.000000Z",
        "ViewDate": "2021-03-23T11:33:21.000000Z"
      }
    }
    
But ViewDate value will be different.

If you try to get message with user user2@test.com access_token again:

    GET http://localhost:8000/api/message/view/501

You receive http 200 response with the same ViewDate value:

    {
      "data": {
        "id": 501,
        "Subject": "updated message title",
        "Message": "First message. A. b.c.",
        "task_id": 2,
        "Created": "2021-03-23T11:17:50.000000Z",
        "Updated": "2021-03-23T11:22:58.000000Z",
        "ViewDate": "2021-03-23T11:25:46.000000Z"
      }
    }

Lets try to get message with id=4 which is seeded for task_id=4, of owner admin@test.com, with user user2@test.com access_token:

    GET http://localhost:8000/api/message/view/4
    
You will receive a response with http 400:

    {
      "Error": "You are not allowed to view message [4]",
      "Messages": []
    }
 
But if you do the same with user admin@test.com access token:
 
    GET http://localhost:8000/api/message/view/4
    
You will receive http 200 response:

    {
      "data": {
        "id": 4,
        "Subject": "Bertrand Paucek",
        "Message": "Et facere quidem enim alias ut praesentium nam. Magnam quia sed quasi dolores.",
        "task_id": 4,
        "Created": "2021-03-23T09:20:37.000000Z",
        "Updated": "2021-03-23T09:20:37.000000Z",
        "ViewDate": "2021-03-23T11:41:24.000000Z"
      }
    }
    
##### US3.4: As a logged in user I expect to be able to delete a message that I am the owner of
    
Lets try to delete message id=501 with a user admin@test.com access token:
    
    DELETE http://localhost:8000/api/message/delete/501 
 
You will receive http 400:

    {
      "Error": "You are not allowed to update message [501] because you are not an owner of it",
      "Messages": []
    }
 
If you do the same with user user2@test.com access_token:

    DELETE http://localhost:8000/api/message/delete/501
    
You will receive http 200 response:

    1
    
If you repeat the deletion request, then you will receive 400 response:

    {
      "Error": "There is no message with id [501]",
      "Messages": []
    }
    
##### US 3.7: As a logged in user I expect that I’m able to view a tasks message log for all tasks I am attached to or I am the owner of    

With user admin@test.com access_token lets view message 4 logs:
    
    http://localhost:8000/api/message/viewlog/4  
    
Http 200 response:
    
    {
      "data": [
        {
          "id": 4,
          "user_id": 1,
          "viewed_at": "2021-03-23T11:41:24.000000Z"
        }
      ]
    }
    
 
 
# Summary

* All the requested user stories working.
* Instead of testing manually I could write tests, but this would be a next step.
* Error code could be used instead of messages, to be able to translate in frontend.
* May be should log all views of the message, not only first one.  