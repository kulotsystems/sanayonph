## sanayonph.com
Source code for sanayonph.com.

### Development Environment
* PHP `^8.0`
* Node `^16.14`
* Composer `^2.2.7`

### Development Setup
1. Clone or download this repository.
2. Open the project directory and copy `.env-example` to `.env` in the root directory.
3. Create a MySQL database named `sanayonph`.
4. Open cloned directory in terminal then run the following commands in order:

    *a. Install backend dependencies:*
    ```composer log
    composer update
    ```
   
    *b. Install frontend dependencies:*
    ```composer log
    npm install
    ```
   
    *c. Migrate database tables:*
    ```composer log
    php artisan migrate
    ```
   
    *d. Spin a local dev server:*
    ```composer log
    php artisan serve
    ```
   
   *e. Compile and run frontend in different terminal session:*
    ```composer log
    npm run watch
    ```
   
   *... Alternatively, instead of commands in [ d ] and [ e ]:*
   ```composer log
   run
   ```
   
    
5. Access the created local dev server and sign-up for an account using any of the following mobile numbers:
    * 09000000000
    * 09111111111
    * 09222222222
    * 09333333333
    * 09444444444
    * 09555555555
    * 09666666666
    * 09777777777
    * 09888888888
    * 09999999999
    
    The verification code can be found in the `otps` database table.


### Optional
Adjust the database connection in `.env` to match your own configuration.

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sanayonph
DB_USERNAME=root
DB_PASSWORD=
```

