## Focal-X-Task-6

#Task Management Team API

# role
- use spatie : General distribution of roles in the system
- user policies : Verify user permissions in the project

**Response**
    - A middleware called JsonResponse was used to model all responses, whether in valid or exception cases.
    - Middleware is added by default. I make append this middleware in Karnel.

**Exception**
    - handle Exceptions in Exception/Handler.php is used to capture all types of Exceptions and model their display using a
      status code.

**Request**
    - All requests are processed within Form Request to verify their validity, organize them, and benefit from all its features as needed.
    - The Request form has been used with all the features and services available in it

 **Validations**
    - Use simple and important expressions and rules in the validation process like: required , exists , unique ,
      numeric ,.......
    - (note) : We did not focus on the(failedAuthorization, failedValidation) cases because they are all handled in the
      handle exception.

# operation
- use observer for update last_activity At each store , update and delete task
- use many relationships like: many to many , through

# note 
- usually, I did not use all the form request processes because I am modeling the response through Middleware and the error is in the handle exception + I didn't have time to add that before delivery.
