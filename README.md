# Community Packages - Form Component

The Form Component application allows you to make customisable forms as per your business requirements. The user can seamlessly embed the form code ino their own websites. It's drag and drop functionality allows you to create beautiful and effective forms without having to write any HTML or CSS of your own. 
You can add as many labels within the Form Component, and create labels of your own, the Custom Fields within the Form Component. When the form is submitted, a UVdesk ticket will be generated automatically.
For customizing the look of the Form, you can change the CSS within the application file. 

## Installation

In the root of your UVdesk project, go inside the apps folder and create a new directory called uvdesk.

Inside the uvdesk directory, your form-component directory will exist and will contain the application; directly download the application and place it within the respective directory or clone the application from within the uvdesk directory.

### Configuration

Run the given commands below, inside the project root:

```
php bin/console c:c

php bin/console assets:install

php bin/console uvdesk_extensions:configure-extensions

php bin/console doctrine:schema:update --force
```
 
Now your Form Component application should be up and running.
